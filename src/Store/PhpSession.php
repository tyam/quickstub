<?php

namespace Store;

use Domain\UserId;

class PhpSession implements \Domain\Session, \Web\Session
{
    private $sessionStarted;

    public function __construct()
    {
        $this->sessionStarted = false;
    }

    private function init()
    {
        $sevenDaysInSec = 7 * 24 * 60 * 60;
        session_set_cookie_params($sevenDaysInSec);
        session_start();
        $this->sessionStarted = true;
        $this->rotate();
    }
    
    protected function rotate()
    {
        $_SESSION['curr'] = $_SESSION['next'];
        unset($_SESSION['next']);
    }

    public function setCurrentUser(UserId $userId): void
    {
        if (!$this->sessionStarted) $this->init();
        $_SESSION['currentUser'] = $userId;
    }

    public function getCurrentUser(): UserId
    {
        if (!$this->sessionStarted) $this->init();
        return ($_SESSION['currentUser']) ? $_SESSION['currentUser'] : null;
    }

    public function setFeedback(s$ident): void
    {
        if (!$this->sessionStarted) $this->init();
        $_SESSION['next']['feedback'] = $ident;
    }

    public function getFeedback() 
    {
        if (!$this->sessionStarted) $this->init();
        return $_SESSION['curr']['feedback'];
    }
}