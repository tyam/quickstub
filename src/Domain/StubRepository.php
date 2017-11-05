<?php

namespace Domain;

class StubRepository
{
    public function reserveId(): StubId;

    public function store(Stub $stub): void;

    /**
     * @return: maybe(Stub)
     */
    public function find(StubId $stubId);

    public function searchByOwner(UserId $userId): StubList;

    public function storeOrdering(StubList $stubs): void;

    public function dispose(Stub $stub): void;
}