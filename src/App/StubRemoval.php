<?php

namespace App;

use Domain\Stub;
use Domain\StubId;
use Domain\StubRepository;
use tyam\radarx\PayloadFactory;
use tyam\fadoc\Converter;

class StubRemoval
{
    private $stubRepo;

    public function __construct(StubRepository $stubRepo)
    {
        $this->stubRepo = $stubRepo;
    }

    public function __invoke(StubId $stubId, $_form, $payloadFactory)
    {
        $userId = \Session::getCurrentUser();
        if (is_null($userId)) {
            return $payloadFactory->notAuthenticated();
        }

        $stub = $this->stubRepo->find($stubId);
        if (is_null($stub)) {
            return $payloadFactory->notFound();
        }

        $this->stubRepo->dispose($stub);
        return $payloadFactory->success($stub, null);
    }
}