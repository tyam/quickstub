<?php

namespace App;

use Domain\User;
use Domain\UserId;
use Domain\UserRepository;
use Domain\Stub;
use Domain\StubRepository;
use tyam\radarx\PayloadFactory;

class StubList
{
    private $userRepo;
    private $stubRepo;
    
    public function __construct(UserRepository $userRepo, StubRepository $stubRepo)
    {
        $this->userRepo = $userRepo;
        $this->stubRepo = $stubRepo;
    }

    public function __invoke($_form, $payloadFactory)
    {
        $userId = \Session::getCurrentUser();
        if (is_null($userId)) {
            $userId = $this->userRepo->getTheUserId();
        }
        if (! is_null($userId)) {
            $user = $this->userRepo->find($userId);
        }
        if (is_null($user) || empty($user)) {
            return $payloadFactory->notAuthenticated();
        }

        $stubs = $this->stubRepo->searchByOwner($userId);

        if (count($stubs) == 0) {
            $stub = Stub::create([$this->stubRepo, 'reserveId']);
            $stubs[] = $stub;
            $this->stubRepo->store($stub);
            $this->stubRepo->storeOrdering($stubs);
        }
        return $payloadFactory->success(null, $stubs);
    }
}