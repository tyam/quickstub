<?php

namespace Link;

use Domain\User;
use Domain\UserId;
use Domain\UserRepository;
use Domain\Stub;
use Domain\StubRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class StubList
{
    private $userRepo;
    private $stubRepo;
    
    public function __construct(UserRepository $userRepo, StubRepository $stubRepo)
    {
        $this->userRepo = $userRepo;
        $this->stubRepo = $stubRepo;
    }

    public function __invoke()
    {
        $userId = \App::getCurrentUser();
        if (! is_null($userId)) {
            $user = $this->userRepo->find($userId);
        }
        if (is_null($user) || empty($user)) {
            return (new Payload())->setStatus(PayloadStatus::NOT_AUTHENTICATED);
        }

        $stubs = $this->stubRepo->searchByOwner($userId);
        
        $payload = new Payload();
        $payload->setStatus(PayloadStatus::FOUND)->setOutput($stubs);
        return $payload;
    }
}