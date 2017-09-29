<?php

namespace Custom;

use Psr\Http\Message\ServerRequestInterface;

class Input
{
    public function __invoke(ServerRequestInterface $request)
    {
        // collect args (path0, path1, $form)
        $args = array_values($request->getAttributes());
        switch (strtoupper($this->detectMethod($request))) {
            case 'HEAD': 
            case 'GET': 
            case 'DELETE': 
                $args[] = $request->getQueryParams();
                break;
            case 'POST': 
            case 'PATCH':
            case 'PUT': 
                $args[] = $request->getParsedBody();
                break;
        }
        return $args;
    }

    protected function detectMethod(ServerRequestInterface $request)
    {
        if ($request->hasHeader('X-METHOD')) {
            return $request->getHeader('X-METHOD');
        }

        $post = $request->getParsedBody();
        if (isset($post['__METHOD'])) {
            return $post['__METHOD'];
        }

        return $request->getMethod();
    }
}