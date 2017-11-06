<?php

namespace Store;

use Domain\Access;
use Domain\StubId;
use Domain\UserId;
use Domain\AccessList;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zend\Diactoros\Request\Serializer as RequestSerializer;
use Zend\Diactoros\Response\Serializer as ResponseSerializer;
use DateTimeImmutable as Datetime;

class AccessMapper;
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function reserveId(): int
    {
        $sql = "SELECT nextval('accessId') AS accessId";
        $stt = $this->pdo->query($sql);
        $stubId = $stt->fetchColumn();
        return intval($stubId);
    }

    public function store(Access $access): void
    {
        // insert only
        $record = $this->toRecord($stub);
        $sql1 = "INSERT INTO access VALUES (:accessId, :stubId, :ownerId, :request, :response, :accessed)";
        $this->pdo->prepare($sql1)
                  ->execute([':accessId' => $record['accessId'], 
                             ':stubId' => $record['stubId'], 
                             ':ownerId' => $record['ownerId'], 
                             ':request' => $record['request'], 
                             ':response' => $record['response'], 
                             ':accessed' => $record['accessed']]);
    }

    private function toRecord(Access $access): array
    {
        return ['accessId' => $access->getAccessId(), 
                'stubId' => $access->getStubId()->getValue(), 
                'ownerId' => $access->getOwnerId()->getValue(), 
                'request' => RequestSerializer::toString($access->getRequest()),  // TODO: server request
                'response' => ResponseSerializer::toString($access->getResponse()), 
                'accessed' => $access->getAccessed()->format('Y-m-d H:i:s')];
    }

    private function fromRecord(array $record): Access
    {
        return new Access(intval($record['accessId']), 
                          new StubId($record['stubId']), 
                          new UserId($record['ownerId']), 
                          RequestSerializer::fromString($record['request']),  // TODO: server request
                          ResponseSerializer::fromString($record['response']), 
                          new Datetime($record['accessed']));
    }

    public function searchByStub(StubId $stubId, int $maxNum = 20, int $maxAge = 120): AccessList
    {
        $sql0 = "SELECT * FROM access "
              . "WHERE stubId = :stubId AND accessed >= :accessed "
              . "ORDER BY accessed DESC "
              . "LIMIT :limit";
        $stt0 = $this->pdo->prepare($sql0);
        $stt0->execute([':stubId' => $stubId->getValue(), 
                        ':accessed' => date('Y-m-d H:i:s', strtotime("-{$maxAge} minute")), 
                        ':limit' => $maxNum]);
        $accesses = [];
        foreach ($stt0->fetch(\PDO::FETCH_ASSOC) as $res) {
            $accesses[] = $this->fromRecord($res);
        }
        return new AccessList($accesses);
    }

    public function searchByUser(UserId $userId, int $maxNum = 20, int $maxAge = 120): AccessList
    {
        $sql0 = "SELECT * FROM access "
              . "WHERE userId = :userId AND accessed >= :accessed "
              . "ORDER BY accessed DESC "
              . "LIMIT :limit";
        $stt0 = $this->pdo->prepare($sql0);
        $stt0->execute([':userId' => $userId->getValue(), 
                        ':accessed' => date('Y-m-d H:i:s', strtotime("-{$maxAge} minute")), 
                        ':limit' => $maxNum]);
        $accesses = [];
        foreach ($stt0->fetch(\PDO::FETCH_ASSOC) as $res) {
            $accesses[] = $this->fromRecord($res);
        }
        return new AccessList($accesses);
    }
}