<?php

namespace Link;

use Domain\User;
use Domain\UserId;
use Domain\UserRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class Home
{
    private $repo;
    
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function __invoke()
    {
        $userId = \App::getCurrentUser();
        \App::debug('userId: '.$userId->getValue());
        $user = $this->repo->find($userId);
        \App::debug('userI2: '.$user->getUserId()->getValue());
        $payload = new Payload();
        $payload->setStatus(PayloadStatus::SUCCESS)->setOutput($user);
        return $payload;
    }
}