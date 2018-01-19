<?php

namespace Web;

use Aura\Payload_Interface\PayloadStatus;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Domain\User;

class StubRemovalResponder extends AbstractResponder
{
    public function __invoke(Request $request, Response $response, PayloadInterface $payload)
    {
        if ($payload->getStatus() == PayloadStatus::SUCCESS) {
            $this->session->setFeedback('stubDeleted');
            $response = $response->withStatus(301)
                                 ->withHeader('Location', '/'.getEnv('USER_PATH'));
            return $response;
        } else {
            throw new \RuntimeException('never');
        }
    }
}