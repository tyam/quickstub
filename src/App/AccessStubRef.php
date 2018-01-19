<?php

namespace App;

use Domain\StubId;
use Domain\Access;
use Domain\AccessRepository;
use Domain\StubRepository;
use tyam\radarx\PayloadFactory;

class AccessStubRef
{
    private $accessRepo;
    private $stubRepo;

    public function __construct(AccessRepository $accessRepo, StubRepository $stubRepo)
    {
        $this->accessRepo = $accessRepo;
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

        if ($stub->getOwnerId() != $userId) {
            return $payloadFactory->notAuthorized($stub);
        }

        $accessList = $this->accessRepo->searchByStub($stubId);
        return $payloadFactory->success($stub, $accessList);
    }
}