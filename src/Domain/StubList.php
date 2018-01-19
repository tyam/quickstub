<?php
/**
 * StubList
 *
 * スタブのリスト。
 * HTTPリクエストはこのリストに対してマッチングがかけられる。
 * リストの順序は重要で、マッチングは上から順に行われる。
 * HTTPリクエストにマッチするスタブが無い場合には404レスポンスが返却される。
 */

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use tyam\condition\Condition;
use DateTimeImmutable as Datetime;

class StubList implements \IteratorAggregate, \ArrayAccess, \Countable
{
    private $stubs;
    private $responder404;
    private $responder403;

    /**
     * デフォルトの404レスポンダを作る。
     *
     * @return ResponseInterface
     */
    public static function createDefaultResponder404(): Responder
    {
        $headers = 'Content-Type: text/html';
        $body = '<html><body><h1>404 Not Found</h1><p>Try <a href="'.\getEnv('USER_PATH').'">here</a></p></body></html>';
        $responder = new Responder(404, $headers, $body);
        return $responder;
    }

    public static function createDefaultResponder403(): Responder
    {
        $headers = 'Content-Type: text/html';
        $body = '<html><body><h1>403 Not Authorized</h1><p>Try <a href="'.\getEnv('USER_PATH').'">here</a></p></body></html>';
        $responder = new Responder(403, $headers, $body);
        return $responder;
    }

    /**
     * `responder404`は、HTTPリクエストにマッチするスタブが無かった場合のレスポンスを
     * 作成するレンポンダ。これにnullが与えられた場合はデフォルトのレスポンダが使われる。
     */
    public function __construct(array $stubs, Responder $responder404 = null, Responder $responder403 = null)
    {
        $this->stubs = $stubs;
        if (is_null($responder404)) {
            $responder404 = self::createDefaultResponder404();
        }
        if (is_null($responder403)) {
            $responder403 = self::createDefaultResponder403();
        }
        $this->responder404 = $responder404;
        $this->responder403 = $responder403;
    }

    public function getResponder404(): Responder
    {
        return $this->responder404;
    }

    public function setResponder404(Responder $responder404): void
    {
        $this->responder404 = $responder404;
    }

    public function getResponder403(): Responder
    {
        return $this->responder403;
    }

    public function setResponder403(Responder $responder403): void
    {
        $this->responder403 = $responder403;
    }

    /**
     * IDが`stubId`のスタブを探して、リスト上のその添字を返す。
     * 対応するスタブが見つからない場合は-1を返す。
     *
     * @param StubId $stubId
     * @return int 添字または-1
     */
    public function indexOf(StubId $stubId): int 
    {
        $len = count($this->stubs);
        for ($i = 0; $i < $len; $i++) {
            if ($this->stubs[$i]->getStubId() == $stubId) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * IDが`stubId`であるスタブを、リスト上の`index`の場所に移動する。
     * `index`は対象のスタブをリストからいったん取り除いた後の添字であることに注意。
     *
     * @param StubId $stubId 対象となるスタブのID
     * @param int $index 移動先の添字
     * @return bool 移動が成功したか否か
     */
    public function moveItem(StubId $stubId, int $index): bool
    {
        if ($index < 0 || $index >= count($this->stubs)) {
            return false;
        }
        $from = $this->indexOf($stubId);
        if ($from === -1) {
            return false;
        }
        list($target) = array_splice($this->stubs, $from, 1);
        array_splice($this->stubs, $index, 0, [$target]);
        return true;
    }

    /**
     * HTTPリクエストに対してスタブリストを起動する。
     *
     * @param ServerRequestInterface $request HTTPリクエスト
     * @param ResponseInterface $response ベースとなるHTTPレスポンス
     * @return ResponseInterface 実行結果のHTTPレスポンス
     */
    public function execute(Request $request, Response $response)
    {
        foreach ($this->stubs as $stub) {
            $result = $stub->execute($request, $response, $this->responder403);
            if (! is_null($result)) {
                \Dispatcher::getInstance()(new AccessEvent($request, $stub, $result, new Datetime()));
                return $result;
            }
        }
        return $this->responder404->respond($response, []);
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->stubs);
    }

    public function offsetExists($offset)
    {
        return isset($this->stubs[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->stubs[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->stubs[] = $value;
        } else {
            $this->stubs[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        array_splice($this->stubs, $offset, 1);
    }

    public function count()
    {
        return count($this->stubs);
    }
}