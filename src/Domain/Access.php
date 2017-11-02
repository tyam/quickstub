<?php

namespace Domain;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable as Datetime;

class Access
{
    private $stubId;
    private $ownerId;
    private $request;
    private $response;
    private $accessed;

    public function __construct(StubId $stubId, UserId $ownerId, Request $request, Response $response, Datetime $accessed)
    {
        $this->stubId = $stubId;
        $this->ownerId = $ownerId;
        $this->request = $request;
        $this->response = $response;
        $this->accessed = $accessed;
    }

    public function getStubId(): StubId
    {
        return $this->stubId;
    }

    public function getOwnerId(): UserId 
    {
        return $this->ownerId;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getAccessed(): \Datetime
    {
        return $this->accessed;
    }

    public static function createFromEvent(AccessEvent $ev): Access
    {
        return new Access($ev->getStub()->getStubId(), 
                          $ev->getStub()->getOwnerId(), 
                          $ev->getRequest(), 
                          $ev->getResponse(), 
                          $ev->getAccessed());
    }
}