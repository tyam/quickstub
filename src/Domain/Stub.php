<?php
/**
 * Stub
 *
 * 単一のスタブURLを表現するエンティティ。
 */

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use tyam\condition\Condition;

class Stub
{
    private $stubId;
    private $ownerId;
    private $matcher;
    private $authorizer;
    private $responder;

    public function __construct(StubId $stubId, 
                                UserId $ownerId, 
                                Matcher $matcher, 
                                Authorizer $authorizer, 
                                Responder $responder)
    {
        $this->stubId = $stubId;
        $this->ownerId = $ownerId;
        $this->matcher = $matcher;
        $this->authorizer = $authorizer;
        $this->responder = $responder;
    }

    /** 
     * デフォルト状態のスタブを作成する。
     * デフォルトのスタブは、'GET /stubXXXXX'のみを受け付け、権限を判定せずに、
     * 'Here QUICKSTUB is!'という文字列をステータスコード200で応答する。
     *
     * @param Callable $reserveId スタブIDを発行する関数
     * @return Stub
     */
    public static function create(Callable $reserveId)
    {
        $stubId = $reserveId();
        $ownerId = \App::getCurrentUser();
        $matcher = new Matcher(true, false, false, false, false, '/stub'.$stubId);
        $authorizer = new NoneAuthorizer();
        $responder = new Responder(200, '', 'Here QUICKSTUB is!');
        return new Stub($stubId, $ownerId, $matcher, $authorizer, $responder);
    }

    /**
     * スタブを実行する。実行の結果は次の通り：
     * - メソッドやパスがマッチしない -> false
     * - スタブへのアクセス権限が無い -> 403レスポンス
     * - スタブが実行された -> Responderによるレスポンス
     * @return maybe(Response); 
     */
    public function execute(Request $request, Response $response)
    {
        $result = $this->matcher->match($request);
        if ($result === false) {
            return null;
        }
        $vars = $result;

        if (! $this->authorizer->authorize($request)) {
            return $response->withStatusCode(403);
        }

        return $this->responder->respond($response, $vars);
    }

    public function getStubId(): StubId 
    {
        return $this->stubId;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getMatcher(): Matcher 
    {
        return $this->matcher;
    }

    public function getAuthorizer(): Authorizer
    {
        return $this->authorizer;
    }

    public function getResponder(): Responder 
    {
        return $this->responder;
    }

    public function modify(Matcher $matcher, Authorizer $authorizer, Responder $responder)
    {
        $this->matcher = $matcher;
        $this->authorizer = $authorizer;
        $this->responder = $responder;
    }
}