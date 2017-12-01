<?php
/**
 * Authorizer
 *
 * スタブへのアクセスの可否を判断するオブジェクト。のインターフェイス。
 */

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;

interface Authorizer
{
    /**
     * スタブへのアクセスの可否を判断する。
     *
     * @param ServerRequestInterface $request 認可対象のリクエスト
     * @return bool 許可ならtrue、そうでないならfalse。
     */
    public function authorize(Request $request): bool;
}