<?php

namespace Link;

use Domain\Access;
use Domain\AccessRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;
use tyam\fadoc\Converter;

class AccessStubRef
{
    private $accessRepo;
    private $converter;

    public function __construct(AccessRepository $accessRepo, Converter $converter)
    {
        $this->accessRepo = $accessRepo;
        $this->converter = $converter;
    }

    public function __invoke($id)
    {
        $userId = \App::getCurrentUser();
        if (is_null($userId)) {
            return (new Payload())->setStatus(PayloadStatus::NOT_AUTHENTICATED);
        }

        $cd0 = $this->converter->objectize(['Domain\StubId', '__construct'], ['value' => $id]);
        if (! $cd0()) {
            return (new Payload())->setStatus(PayloadStatus::NOT_FOUND);
        }

        $stubId = call_user_func_array(['Domain\StubId', '__construct'], $cd0->get());

        $stub = $this->find($stubId);
        if (is_null($stub)) {
            return (new Payload())->setStatus(PayloadStatus::NOT_FOUND);
        }
        if ($stub->getOwner() != $userId) {
            return (new Payload())->setStatus(PayloadStatus::NOT_AUTHORIZED);
        }

        $accessList = $this->accessRepo->searchByStub($stubId);

        return (new Payload())->setStatus(PayloadStatus::FOUND)->setOutput($accessList);
    }
}