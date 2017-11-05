<?php

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StubConfig extends ContainerConfig
{
    
    public function define(Container $di)
    {
        // set project-specific router to customise responder auto-resolution.
        $di->setters['Aura\Router\RouterContainer']['setRouteFactory'] = $di->newFactory('Web\Route');
    }
    
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        // middlewares
        $adr->middle(new ResponseSender());
        $adr->middle(['Web\StubExecInput', 'carryResponse']);
        $adr->middle('Radar\Adr\Handler\RoutingHandler');
        $adr->middle('Radar\Adr\Handler\ActionHandler');

        $adr->route('StubExec', '/', 'Link\StubExec')->wildcard('x')
            ->allows(['GET', 'PUT', 'POST', 'DELETE', 'PATCH']);
    }
}