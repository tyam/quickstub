<?php

namespace Domain;

interface Session
{
    public function getCurrentUser(): UserId;
    public function setCurrentUser(UserId $userId): void;
}