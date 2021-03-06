<?php
/**
 * Access
 *
 * スタブに発生したアクセス。アクセス履歴として参照できるようになっている。
 *
 */

namespace Domain;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable as Datetime;

class Access
{
    private $accessId;
    private $stubId;
    private $ownerId;
    private $request;
    private $response;
    private $accessed;

    public function __construct(int $accessId, StubId $stubId, UserId $ownerId, Request $request, Response $response, Datetime $accessed)
    {
        $this->accessId = $accessId;
        $this->stubId = $stubId;
        $this->ownerId = $ownerId;
        $this->request = $request;
        $this->response = $response;
        $this->accessed = $accessed;
    }

    public function getAccessId(): int
    {
        return $this->accessId;
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

    public function getAccessed(): Datetime
    {
        return $this->accessed;
    }

    /**
     * イベントからアクセスオブジェクトを作る。
     *
     * @param AccessEvent $ev
     * @return Access
     */
    public static function createFromEvent($generateId, AccessEvent $ev): Access
    {
        $accessId = $generateId();
        return new Access($accessId, 
                          $ev->getStub()->getStubId(), 
                          $ev->getStub()->getOwnerId(), 
                          $ev->getRequest(), 
                          $ev->getResponse(), 
                          $ev->getAccessed());
    }
}