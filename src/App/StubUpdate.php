<?php

namespace App;

use Domain\Stub;
use Domain\StubId;
use Domain\StubRepository;
use tyam\radarx\PayloadFactory;
use tyam\fadoc\Converter;

class StubUpdate
{
    private $stubRepo;
    private $converter;

    public function __construct(StubRepository $stubRepo, Converter $converter)
    {
        $this->stubRepo = $stubRepo;
        $this->converter = $converter;
    }

    public function __invoke(StubId $stubId, $form, $payloadFactory)
    {
        $userId = \Session::getCurrentUser();
        if (is_null($userId)) {
            return $payloadFactory->notAuthenticated();
        }

        $stub = $this->stubRepo->find($stubId);
        if (is_null($stub)) {
            return $payloadFactory->notFound();
        }

        $cd1 = $this->converter->objectize([$stub, 'modify'], $form);
        if (! $cd1()) {
            return $payloadFactory->notValid($stub, $cd1->describe());
        }

        list($matcher, $authorizer, $responder) = $cd1->get();
        $stub->modify($matcher, $authorizer, $responder);
        $this->stubRepo->store($stub);
        return $payloadFactory->success($stub, null);
    }
}