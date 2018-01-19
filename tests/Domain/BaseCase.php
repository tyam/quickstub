<?php

namespace tests\Domain;

use PHPUnit\Framework\TestCase;
use tyam\radarx\ServiceLocator;
use Domain\UserId;

class FakeSession implements \Domain\Session
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function setCurrentUser(UserId $userId): void 
    {
        $this->data['currentUser'] = $userId;
    }

    public function getCurrentUser(): UserId
    {
        return $this->data['currentUser'];
    }
}

class BaseCase extends TestCase
{
    /**
     * `obj`の`method`を表現するクロージャを返す。
     *
     * @param $obj ターゲットとなるオブジェクト
     * @param string $method ターゲットとなるメソッドの名前
     * @return Closure
     */
    public function reveal($obj, string $method): \Closure 
    {
        $m = new \ReflectionMethod($obj, $method);
        return $m->getClosure();
    }
    
    public function setUp()
    {
        if (! class_exists('Logger')) {
            $this->initApp();
        }
    }
    private function initApp()
    {
        $logger = new \Monolog\Logger('myapp');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));
        new class('Logger', $logger) extends ServiceLocator {};
        $dispatcher = new \tyam\edicue\Dispatcher();
        new class('Dispatcher', $dispatcher) extends ServiceLocator {};
        $session = new FakeSession();
        new class('Session', $session) extends ServiceLocator {};
    }
}