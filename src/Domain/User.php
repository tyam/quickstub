<?php

namespace Domain;

use DateTimeImmutable as Datetime;

class User 
{
    private $userId;
    private $displayName;
    private $created;

    public function __construct(UserId $userId, string $displayName, Datetime $created = null)
    {
        $this->userId = $userId;
        $this->displayName = $displayName;
        $this->created = ($created) ? $created : new Datetime();
    }

    public static function register(Callable $generateId, string $displayName = '新規ユーザー')
    {
        return new User($generateId(), $displayName);
    }

    public function getUserId(): UserId 
    {
        return $this->userId;
    }

    public function getDisplayName(): string 
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName)
    {
        $this->displayName = $displayName;
    }

    public function getCreated(): Datetime
    {
        return $this->created;
    }
}