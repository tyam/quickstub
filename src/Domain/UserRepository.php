<?php

namespace Domain;

interface UserRepository 
{
    public function reserveId(): UserId;

    public function store(User $user);

    /**
     *
     * @return Nullable(User) returns User when found, returns null when not found.
     *
     */
    public function find(UserId $userId);

    public function getTheUserId(); UserId;
}