<?php

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;

interface Authenticator
{
    public function authenticate(Request $request): bool;
}