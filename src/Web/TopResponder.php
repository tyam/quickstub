<?php

namespace Web;

use Aura\Payload_Interface\PayloadStatus;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TopResponder extends AbstractResponder
{
    use \Custom\RunDomain;
    
    public function __invoke(Request $request, Response $response, PayloadInterface $payload = null)
    {
        $output = $response->getBody();
        $output->write($this->bamboo->render('top', []));
        return $response;
    }
}