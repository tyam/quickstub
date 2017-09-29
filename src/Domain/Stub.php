<?php

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use tyam\condition\Condition;

class Stub
{
    private $stubId;
    private $owner;
    private $matcher;
    private $authenticator;
    private $responder;

    // @return Condition [<name> => <value>, ...] when matched, null when unmatched.
    public function match(Request $request): Condition 
    {
    }

    // @return string HTTP Status-Code
    public function authenticate(Request $request): string 
    {

    }

    // @return Response to respond.
    public function respond(array $vars, Response $response): Response 
    {
        
    }
}