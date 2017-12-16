<?php

namespace Link;

use Domain\Access;
use Domain\AccessRepository;
use tyam\radarx\PayloadFactory;

class AccessRef
{
    private $accessRepo;

    public function __construct(AccessRepository $accessRepo)
    {
        $this->accessRepo = $accessRepo;
    }

    public function __invoke($_form, $payloadFactory)
    {
        $userId = \App::getCurrentUser();
        if (is_null($userId)) {
            return $payloadFactory->notAuthenticated();
        }

        $accessList = $this->accessRepo->searchByUser($userId);
        return $payloadFactory->success(null, $accessList);
    }
}