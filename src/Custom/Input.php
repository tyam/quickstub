<?php

namespace Custom;

use Psr\Http\Message\ServerRequestInterface;

class Input
{
    public function __invoke(ServerRequestInterface $request)
    {
        // collect args (path0, path1, ..., $form)
        $args = array_values($request->getAttributes());
        switch (strtoupper($request->getMethod())) {
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
}