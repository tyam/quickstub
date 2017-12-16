<?php

namespace Web;

use Aura\Payload_Interface\PayloadStatus;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Domain\User;

class StubEntryResponder extends AbstractResponder
{
    public function __invoke(Request $request, Response $response, PayloadInterface $payload)
    {
        if ($payload->getStatus() == PayloadStatus::SUCCESS) {
            $path = sprintf('/%s/%s', getEnv('USER_PATH'), $payload->getOutput()->getStubId());
            $this->session->setFeedback('stubCreated');
            $response = $response->withStatus(301)
                                 ->withHeader('Location', $path);
            return $response;
        } else {
            throw new \RuntimeException('never');
        }
    }
}