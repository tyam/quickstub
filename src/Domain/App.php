<?php

namespace Domain;

use Custom\Service;

class App extends Service
{
    private $session;

    public function __construct($logger, $dispatcher, Session $session)
    {
        parent::__construct($logger, $dispatcher);
        $this->session = $session;
    }

    public static function setCurrentUser(UserId $userId): void 
    {
        self::$singleton->session->setCurrentUser($userId);
    }

    public static function getCurrentUser(): UserId 
    {
        return self::$singleton->session->getCurrentUser();
    }
}