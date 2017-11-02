<?php

namespace tests\Domain;

use PHPUnit\Framework\TestCase;
use Domain\App;
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
    public function setUp()
    {
        if (! App::hasSingleton()) {
            $this->initApp();
        }
    }
    private function initApp()
    {
        $logger = new \Monolog\Logger('myapp');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));
        $dispatcher = new \tyam\edicue\Dispatcher();
        $session = new FakeSession();
        new App($logger, $dispatcher, $session);
        class_alias('Domain\App', 'App');
    }
}