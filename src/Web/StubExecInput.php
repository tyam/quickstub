<?php
/**
 * Stub実行用のInput兼relayミドルウェア
 *
 * レスポンスオブジェクトをドメイン層に渡す。
 */

namespace Web;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class StubExecInput
{
    /**
     * relayミドルウェアのメソッド
     */
    public static function carryResponse(Request $request, Response $response, $next)
    {
        $request = $request->withAttribute('app:response', $response);
        return $next($request, $response);
    }
    
    /**
     * radarのInputのメソッド
     */
    public function __invoke(Request $request)
    {
        $response = $request->getAttribute('app:response');
        return [$request->withoutAttribute('app:response'), $response];
    }
}