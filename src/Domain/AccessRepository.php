<?php

namespace Domain;

interface AccessRepository
{
    public function reserveId(): int;
    
    public function store(Access $access): void;

    public function searchByStub(StubId $stubId, int $maxNum = 20, int $maxAge = 120): AccessList;

    public function searchByUser(UserId $userId, int $maxNum = 20, int $maxAge = 120): AccessList;
}