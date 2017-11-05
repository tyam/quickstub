<?php

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Relay\Middleware\ExceptionHandler;
use Relay\Middleware\ResponseSender;
use Relay\Middleware\SessionHeadersHandler;
use Relay\Middleware\JsonContentHandler;
use Zend\Diactoros\Response as Response;

class ConsoleConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        // set project-specific router to customise responder auto-resolution.
        $di->setters['Aura\Router\RouterContainer']['setRouteFactory'] = $di->newFactory('Web\Route');
        
        // setup RunDomain trait
        $di->setters['Web\RunDomain']['setResolve'] = $di->lazyNew('Aura\Di\ResolutionHelper');
    }
    
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        // middlewares
        $adr->middle(new ResponseSender());
        $adr->middle(new SessionHeadersHandler());
        $adr->middle(new JsonContentHandler());
        $adr->middle(new ExceptionHandler(new Response()));
        $adr->middle('Web\XMethodHandler');
        $adr->middle('Radar\Adr\Handler\RoutingHandler');
        $adr->middle('Radar\Adr\Handler\ActionHandler');

        $adr->input('Web\ConsoleInput');

        // routes
        // /user            -- POST for signup/login, GET for stub list
        // /user/ordering   -- PUT for re-ordering
        // /user/new        -- POST for create stub
        // /user/{stub}     -- PUT for update, GET for refer, DELETE for delete
        // /user/access     -- GET for all accesses
        // /user/{stub}/access     -- GET for the stub accesses
        $base = '/' . getEnv('USER_PATH');
        $adr->get(   'StubList',      $base,                  'Link\StubList');
        $adr->put(   'StubOrdering',  $base,                  'Link\StubOrdering');
        $adr->post(  'StubEntry',     $base.'/new',           'Link\StubEntry');
        $adr->put(   'StubUpdate',    $base.'/{stub}',        'Link\StubUpdate');
        $adr->get(   'StubRef',       $base.'/{stub}',        'Link\StubRef');
        $adr->delete('StubRemoval',   $base.'/{stub}',        'Link\StubRemoval');
        $adr->get(   'AccessRef',     $base.'/access',        'Link\AccessRef');
        $adr->get(   'StubAccessRef', $base.'/{stub}/access', 'Link\StubAccessRef');
    }
}