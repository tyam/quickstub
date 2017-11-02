<?php

namespace Custom;

use Psr\Log\LoggerInterface;
use tyam\edicue\Dispatcher;

class Service
{
    protected static $singleton;
    private $logger;
    private $dispatcher;

    public function __construct($logger, $dispatcher) 
    {
        if (self::$singleton) {
            throw new \LogicException('singleton duplicated!');
        }
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        self::$singleton = $this;
    }

    public static function hasSingleton(): bool 
    {
        return (! is_null(self::$singleton));
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
}