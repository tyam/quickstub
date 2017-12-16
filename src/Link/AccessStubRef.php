<?php

namespace Link;

use Domain\Access;
use Domain\AccessRepository;
use tyam\radarx\PayloadFactory;

class AccessStubRef
{
    private $accessRepo;

    public function __construct(AccessRepository $accessRepo)
    {
        $this->accessRepo = $accessRepo;
    }

    public function __invoke(StubId $stubId, $_form, $payloadFactory)
    {
        $userId = \App::getCurrentUser();
        if (is_null($userId)) {
            return $payloadFactory->notAuthenticated();
        }

        $stub = $this->accessRepo->find($stubId);
        if (is_null($stub)) {
            return $payloadFactory->notFound();
        }

        if ($stub->getOwner() != $userId) {
            return $payloadFactory->notAuthorized($stub);
        }

        $accessList = $this->accessRepo->searchByStub($stubId);
        return $payloadFactory->success($stub, $accessList);
    }
}