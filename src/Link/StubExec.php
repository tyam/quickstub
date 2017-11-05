<?php

namespace Link;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Domain\StubList;
use Domain\StubRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class StubExec
{
    private $stubRepo;

    public function __construct(StubRepository $stubRepo)
    {
        $this->stubRepo = $stubRepo;
    }
    public function __invoke(Request $request, Response $response)
    {
        $userId = $this->getUserId();
        $list = $this->stubRepo->searchByOwner($userId);
        $response = $list->execute($request, $response);

        $payload = new Payload();
        $payload->setStatus(PayloadStatus::SUCCESS)->setOutput($response);
        return $payload;
    }
}