<?php

namespace Web;

use Aura\Payload_Interface\PayloadStatus;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserEntryResponder
{
    public function __invoke(Request $request, Response $response, PayloadInterface $payload)
    {
        if ($payload->getStatus() == PayloadStatus::CREATED) {
            \App::setFeedback('userEntered');
            return $response->withHeader('Location', '/home')->withStatus(302);
        } else {
            var_dump($payload);
        }
    }
}