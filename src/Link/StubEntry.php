<?php

namespace Link;

use Domain\Stub;
use Domain\StubRepository;
use tyam\radarx\PayloadFactory;

class StubEntry
{
    private $stubRepo;

    public function __construct(StubRepository $stubRepo)
    {
        $this->stubRepo = $stubRepo;
    }

    public function __invoke($_form, $payloadFactory)
    {
        $userId = \App::getCurrentUser();
        if (is_null($userId)) {
            return $payloadFactory->notAuthenticated();
        }
        
        $stub = Stub::create([$this->stubRepo, 'reserveId']);
        $this->stubRepo->store($stub);
        return $payloadFactory->success(null, $stub);
    }
}