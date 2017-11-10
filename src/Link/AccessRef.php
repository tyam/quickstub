<?php

namespace Link;

use Domain\Access;
use Domain\AccessRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class AccessRef
{
    private $accessRepo;

    public function __construct(AccessRepository $accessRepo)
    {
        $this->accessRepo = $accessRepo;
    }

    public function __invoke()
    {
        $userId = \App::getCurrentUser();
        if (is_null($userId)) {
            return (new Payload())->setStatus(PayloadStatus::NOT_AUTHENTICATED);
        }

        $accessList = $this->accessRepo->searchByUser($userId);

        return (new Payload())->setStatus(PayloadStatus::FOUND)->setOutput($accessList);
    }
}