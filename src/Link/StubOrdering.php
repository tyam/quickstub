<?php

namespace Link;

use Domain\UserId;
use Domain\Stub;
use Domain\StubList;
use Domain\StubRepository;
use tyam\fadoc\Converter;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class StubOrdering
{
    private $stubRepo;
    private $converter;

    public function __construct(StubRepository $stubRepo, Converter $converter)
    {
        $this->stubRepo = $stubRepo;
        $this->converter = $converter;
    }

    public function __invoke($form)
    {
        $userId = \App::getCurrentUser();
        if (is_null($userId)) {
            return (new Payload())->setStatus(PayloadStatus::NOT_AUTHENTICATED);
        }
        $stubList = $this->stubRepo->searchByOwner($userId);

        $cd = $this->converter->objectize([$stubList, 'moveItem'], $form);
        if (! $cd()) {
            $payload = new Payload();
            $payload->setStatus(PayloadStatus::NOT_VALID);
            $payload->setOutput(['errors' => $cd->describe(), 'form' => $form]);
            return $payload;
        }

        $args = $cd->get();
        $result = call_user_func_array([$stubList, 'moveItem'], $args);
        if (! $result) {
            return (new Payload())->setStatus(PayloadStatus::NOT_UPDATED);
        }

        $this->stubRepo->storeOrdering($stubList);
        return (new Payload())->setStatus(PayloadStatus::SUCCESS);
    }
}