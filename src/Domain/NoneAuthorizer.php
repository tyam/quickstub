<?php
/**
 * NoneAuthorizer
 *
 * Authorizerインターフェイスのデフォルト実装。
 * 常にアクセスを許可する。
 */

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;

class NoneAuthorizer implements Authorizer
{
    public function __construct()
    {

    }
    
    public function authorize(Request $request): bool 
    {
        return true;
    }
}