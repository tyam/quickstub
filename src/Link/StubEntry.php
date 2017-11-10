<?php

namespace Link;

use Domain\Stub;
use Domain\StubRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class StubEntry
{
    private $stubRepo;

    public function __construct(StubRepository $stubRepo)
    {
        $this->stubRepo = $stubRepo;
    }

    public function __invoke(_$form)
    {
        $userId = \App::getCurrentUser();
        if (is_null($userId)) {
            return (new Payload())->setStatus(PayloadStatus::NOT_AUTHENTICATED);
        }
        
        $stub = Stub::create([$this->stubRepo, 'reserveId']);

        $this->stubRepo->store($stub);

        return new Payload()->setStatus(PayloadStatus::CREATED)->setOutput($stub);
    }
}