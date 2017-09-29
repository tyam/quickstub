<?php

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Relay\Middleware\ExceptionHandler;
use Relay\Middleware\ResponseSender;
use Relay\Middleware\SessionHeadersHandler;
use Relay\Middleware\JsonContentHandler;
use Zend\Diactoros\Response as Response;

class WebConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        // set project-specific router to customise responder auto-resolution.
        $di->setters['Aura\Router\RouterContainer']['setRouteFactory'] = $di->newFactory('Custom\Route');
        
        // setup RunDomain trait
        $di->setters['Custom\RunDomain']['setResolve'] = $di->lazyNew('Aura\Di\ResolutionHelper');
    }
    
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        // middlewares
        $adr->middle(new ResponseSender());
        $adr->middle(new SessionHeadersHandler());
        $adr->middle(new JsonContentHandler());
        $adr->middle(new ExceptionHandler(new Response()));
        $adr->middle('Radar\Adr\Handler\RoutingHandler');
        $adr->middle('Radar\Adr\Handler\ActionHandler');
        $adr->input('Custom\Input');

        // routes
        $adr->get('Top',        '/');
        $adr->post('UserEntry', '/user',            'Link\UserEntry');
        $adr->get('Home',       '/home',            'Link\Home');
        //$adr->get('StubList',   '/stub',            'Link\StubList');
        //$adr->get('Stub',       '/stub/{stubId}',   'Link\Stub');
    }
}