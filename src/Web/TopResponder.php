<?php

namespace Web;

use Aura\Payload_Interface\PayloadStatus;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TopResponder 
{
    use \Custom\RunDomain;
    
    public function __invoke(Request $request, Response $response, PayloadInterface $payload = null)
    {
        // \Domain\App::debug('abc');
        //phpinfo();exit;
        $output = $response->getBody();
        $output->write($this->getContent());
        return $response;
    }
    protected function getContent() 
    {
        return <<<EOS
<form method="POST" action="/user">
<button type="submit">登録</button>
</form>
EOS;
    }
}