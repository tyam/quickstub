<?php

namespace Store;

use Domain\UserId;

class PhpSession
{
    private $sessionStarted;

    public function __construct()
    {
        $this->sessionStarted = false;
    }

    private function init()
    {
        session_start();
        $this->sessionStarted = true;
        $this->rotate();
    }
    
    protected function rotate()
    {
        $_SESSION['curr'] = $_SESSION['next'];
        unset($_SESSION['next']);
    }

    public function setCurrentUser(UserId $userId)
    {
        if (!$this->sessionStarted) $this->init();
        $_SESSION['currentUser'] = $userId;
    }

    public function getCurrentUser()
    {
        if (!$this->sessionStarted) $this->init();
        return ($_SESSION['currentUser']) ? $_SESSION['currentUser'] : null;
    }

    public function setFeedback(string $ident)
    {
        if (!$this->sessionStarted) $this->init();
        $_SESSION['next']['feedback'] = $ident;
    }

    public function getFeedback(): string 
    {
        if (!$this->sessionStarted) $this->init();
        return $_SESSION['curr']['feedback'];
    }

    public function increment()
    {
        if (!$this->sessionStarted) $this->init();
        $rv = $_SESSION['a'] + 1;
        $_SESSION['a'] = $rv;
        return $rv;
    }
}