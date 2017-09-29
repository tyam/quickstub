<?php

namespace Web;

use Aura\Payload_Interface\PayloadStatus;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Domain\User;

class HomeResponder 
{
    use \Custom\RunDomain;
    
    public function __invoke(Request $request, Response $response, PayloadInterface $payload = null)
    {
        //var_dump($_SESSION);exit;
        $output = $response->getBody();
        $output->write($this->getContent($payload->getOutput()));
        return $response;
    }
    protected function getContent(User $user) 
    {
        return <<<EOS
<p>こんにちは、{$user->getDisplayName()} [{$user->getUserId()->getValue()}]</p>
EOS;
    }
}