<?php

namespace Web;

use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class StubExecResponder
{
    public function __invoke(Request $request, Response $response, PayloadInterface $payload = null)
    {
        $response = $payload->getOutput();
        return $response;
    }
}