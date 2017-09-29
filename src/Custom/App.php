<?php

namespace Custom;

use Psr\Log\LoggerInterface;
use tyam\edicue\Dispatcher;

class App
{
    private static $singleton;
    private $logger;
    private $dispatcher;
    private $session;

    public function __construct($logger, $dispatcher, $session) 
    {
        if (self::$singleton) {
            throw new \LogicException('App duplicated!');
        }
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        $this->session = $session;
        self::$singleton = $this;
    }

    public static function dispatch($event)
    {
        self::$singleton->$dispatcher($event);
    }

    public static function debug($message, array $context = [])
    {
        self::$singleton->logger->debug($message, $context);
    }

    public static function info($message, array $context = [])
    {
        self::$singleton->logger->info($message, $context);
    }

    public static function __callStatic($name, $args)
    {
        return call_user_func_array([self::$singleton->session, $name], $args);
    }
}