<?php

namespace Link;

use Domain\Stub;
use Domain\StubRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;
use tyam\fadoc\Converter;

class StubRemoval
{
    private $stubRepo;
    private $converter;

    public function __construct(StubRepository $stubRepo, Converter $converter)
    {
        $this->stubRepo = $stubRepo;
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

        $this->stubRepo->dispose($stub);

        return (new Payload())->setStatus(PayloadStatus::DELETED)->setOutput($stub);
    }
}