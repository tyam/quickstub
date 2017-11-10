<?php

namespace Link;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Domain\UserId;
use Domain\StubList;
use Domain\StubRepository;
use Domain\UserRepository;
use Aura\Payload\Payload;
use Aura\Payload_Interface\PayloadStatus;

class StubExec
{
    private $stubRepo;
    private $userRepo;

    public function __construct(StubRepository $stubRepo, UserRepository $userRepo)
    {
        $this->stubRepo = $stubRepo;
        $this->userRepo = $userRepo;
    }

    private function getUserId(): UserId
    {
        return $this->userRepo->getTheUserId();
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