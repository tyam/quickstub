<?php

namespace Link;

use Domain\User;
use Domain\UserId;
use Domain\UserRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class UserEntry 
{
    private $repo;
    
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function __invoke()
    {
        $user = User::register([$this->repo, 'reserveId']);
        \App::setCurrentUser($user->getUserId());
        $this->repo->store($user);
        $payload = new Payload();
        $payload->setStatus(PayloadStatus::CREATED)->setOutput($user);
        return $payload;
    }
}