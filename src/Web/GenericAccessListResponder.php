<?php
/**
 * 汎用のアクセス履歴の応答
 *
 * Payloadでアクセス履歴が引き渡されるので、それをjsonに変換して応答する。
 */

namespace Web;

use Aura\Payload_Interface\PayloadStatus;
use Aura\Payload_Interface\PayloadInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Domain\AccessList;

class GenericAccessListResponder extends AbstractResponder
{
    public function __invoke(Request $request, Response $response, PayloadInterface $payload)
    {
        if ($payload->getStatus() == PayloadStatus::SUCCESS) {
            $body = $this->accessListToJson($payload->getOutput());
            $response->getBody()->write($body);
            return $response->withStatus(200)
                            ->withHeader('Content-Type', 'application/json');
        } else {
            throw new \RuntimeException('never');
        }
    }

    protected function accessListToJson(AccessList $list)
    {
        $rv = [];
        foreach ($list as $access) {
            $req = $access->getRequest();
            $res = $access->getResponse();
            $rv[] = ['request' => $this->convertRequest($req), 
                     'response' => $this->convertResponse($res)];
        }
        return json_encode($rv);
    }

    protected function convertRequest($request)
    {
        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = ['name' => $name, 'value' => $value];
            }
        }

        $request->getBody()->rewind();
        $body = $request->getBody()->getContents();

        $rv = [
            'method' => $request->getMethod(), 
            'target' => $request->getRequestTarget(), 
            'headers' => $headers
        ];
        if ($body) {
            $rv['body'] = $body;
        }
        return $rv;
    }

    protected function convertResponse($response)
    {
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = ['name' => $name, 'value' => $value];
            }
        }

        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();

        $rv = [
            'status' => $response->getStatusCode(), 
            'headers' => $headers
        ];
        if ($body) {
            $rv['body'] = $body;
        }
        return $rv;
    }
}