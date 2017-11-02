<?php

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;

class NoneAuthenticator implements Authenticator
{
    public function authenticate(Request $request): bool 
    {
        return true;
    }
}