<?php

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\StreamHandler;

class Config extends ContainerConfig
{
    const APP_NAME = 'myapp';

    public function define(Container $di)
    {
        // Application infrastructure
        // ---------------------------------------------------------
        // app
        $di->params['Domain\App'][0] = $di->lazyGet('logger');
        $di->params['Domain\App'][1] = $di->lazyNew('tyam\edicue\Dispatcher');
        $di->params['Domain\App'][2] = $di->lazyGet('session');

        // logger
        $di->set('logger', $di->lazyNew('Monolog\Logger'));
        $di->params['Monolog\Logger'][0] = self::APP_NAME;

        // session
        $di->set('session', $di->lazyNew('Store\PhpSession'));

        // dispatcher
        $di->params['Aura\Di\ResolutionHelper']['container'] = $di;
        $di->params['tyam\edicue\Dispatcher'][0] = $di->lazyNew('Aura\Di\ResolutionHelper');
        $di->params['tyam\edicue\Dispatcher'][1] = null;
        $di->params['tyam\edicue\Dispatcher'][2] = [
            // add listeners here.
        ];
        
        // Repositories
        // ---------------------------------------------------------
        $di->types['Domain\UserRepository'] = $di->lazyNew('Store\UserMapper');

        // Mappers  mysql:dbname=testdb;host=127.0.0.1
        // ---------------------------------------------------------
        $di->set('db', $di->lazyNew('PDO'));
        $di->params['PDO']['dsn'] = sprintf('mysql:dbname=%s;host=%s', getenv('DATABASE_NAME'), getenv('DATABASE_HOST'));
        $di->params['PDO']['username'] = getenv('DATABASE_USER');
        $di->params['PDO']['passwd'] = getenv('DATABASE_PASSWORD');
        $di->params['PDO']['options'] = array();
        $di->types['PDO'] = $di->lazyGet('db');
    }

    public function modify(Container $di)
    {
        // setup monolog
        $logger = $di->get('logger');
        //$logger->pushHandler(new SyslogHandler(self::APP_NAME));
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

        // instantiate App to make the singleton.
        $di->newInstance('Domain\App');
        class_alias('Domain\App', 'App');
    }
}
