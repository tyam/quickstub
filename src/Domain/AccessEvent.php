<?php

namespace Domain;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable as Datetime;

class AccessEvent
{
    private $request;
    private $stub;
    private $response;
    private $accessed;

    public function __construct(Request $request, Stub $stub, Response $response, Datetime $accessed = null)
    {
        $this->request = $request;
        $this->stub = $stub;
        $this->response = $response;
        if (is_null($accessed)) {
            $accessed = new Datetime();
        }
        $this->when = $when;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getStub(): Stub
    {
        return $this->stub;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getAccessed(): Datetime
    {
        return $this->accessed;
    }
}