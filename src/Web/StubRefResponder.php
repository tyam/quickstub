<?php

namespace Web;

use Aura\Payload_Interface\PayloadStatus;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Domain\User;
use tyam\bamboo\VariableProvider;

class StubRefResponder extends AbstractResponder
{
    public function provideVariables(string $template): array
    {
        return ['feedback' => $this->session->getFeedback()] + parent::provideVariables($template);
    }

    public function getForm($payload) 
    {
        $stub = $payload->getOutput();
        $args = [$stub->getMatcher(), $stub->getAuthorizer(), $stub->getResponder()];
        return $this->converter->formulize(['Domain\Stub', 'modify'], $args);
    }

    public function __invoke(Request $request, Response $response, PayloadInterface $payload)
    {
        $output = $response->getBody();
        $vars = ['stub' => $payload->getOutput(), 'form' => $this->getForm($payload)];
        $output->write($this->bamboo->render('stubRef', $vars));
        return $response;
    }
}