<?php

namespace Link;

use Domain\User;
use Domain\UserId;
use Domain\UserRepository;
use Domain\Stub;
use Domain\StubId;
use Domain\StubRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class StubInit 
{
    private $userRepo;
    private $stubRepo;
    
    public function __construct(UserRepository $userRepo, StubRepository $stubRepo)
    {
        $this->userRepo = $userRepo;
        $this->stubRepo = $stubRepo;
    }

    public function __invoke($_form)
    {
        $user = User::register([$this->userRepo, 'reserveId'], '新規ユーザー', true);
        $this->userRepo->store($user);

        $stub = Stub::create([$this->stubRepo, 'reserveId']);
        $this->stubRepo->store($stub);

        $payload = new Payload();
        $payload->setStatus(PayloadStatus::CREATED)->setOutput($user);
        return $payload;
    }
}