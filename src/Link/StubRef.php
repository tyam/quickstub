<?php

namespace Link;

use Domain\StubId;
use Domain\Stub;
use Domain\StubRepository;
use tyam\radarx\PayloadFactory;

class StubRef
{
    private $stubRepo;
    private $converter;

    public function __construct(StubRepository $stubRepo)
    {
        $this->stubRepo = $stubRepo;
    }

    public function __invoke(StubId $stubId, $form, $payloadFactory)
    {
        $userId = \App::getCurrentUser();
        if (is_null($userId)) {
            return $payloadFactory->notAuthenticated();
        }

        $stub = $this->stubRepo->find($stubId);
        if (is_null($stub)) {
            return $payloadFactory->notFound();
        }

        return $payloadFactory->success(null, $stub);
    }
}