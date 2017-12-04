<?php

namespace Store;

use Domain\StubId;
use Domain\Stub;
use Domain\StubList;
use Domain\Matcher;
use Domain\Responder;
use Domain\UserId;
use Domain\StubRepository;

class StubMapper implements StubRepository
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function reserveId(): StubId
    {
        $sql = "SELECT nextval('stubId') AS stubId";
        $stt = $this->pdo->query($sql);
        $stubId = $stt->fetchColumn();
        return new StubId($stubId);
    }

    public function store(Stub $stub): void
    {
        $record = $this->toRecord($stub);
        if ($this->find($stub->getStubId())) {
            // update
            $sql0 = "UPDATE stub "
                  . "SET methods = :methods, "
                  . "    `path` = :path, "
                  . "    statusCode = :statusCode, "
                  . "    header = :header, "
                  . "    body = :body "
                  . "WHERE stubId = :stubId";
            $this->pdo->prepare($sql0)
                      ->execute([':methods' => $record['methods'], 
                                 ':path' => $record['path'], 
                                 ':statusCode' => $record['statusCode'], 
                                 ':header' => $record['header'], 
                                 ':body' => $record['body']]);
        } else {
            // insert
            $sql1 = "INSERT INTO stub VALUES (:stubId, :ownerId, :methods, :path, :statusCode, :header, :body)";
            $this->pdo->prepare($sql1)
                      ->execute([':stubId' => $record['stubId'], 
                                 ':ownerId' => $record['ownerId'], 
                                 ':methods' => $record['methods'], 
                                 ':path' => $record['path'], 
                                 ':statusCode' => $record['statusCode'], 
                                 ':header' => $record['header'], 
                                 ':body' => $record['body']]);
            // insert into ordering as the first item.
            $sql2 = "INSERT INTO stubOrdering (:ownerId, (SELECT MIN(ord) - 1 FROM stubOrdering WHERE ownerId = :ownerId), :stubId)";
            $this->pdo->prepare($sql2)
                      ->execute([':stubId' => $record['stubId'], 
                                 ':ownerId' => $record['ownerId']]);
        }
    }

    private function toRecord(Stub $stub): array
    {
        $methods = 0;
        $matcher = $stub->getMatcher();
        $methods += ($matcher->isGetEnabled()) ? 1 : 0;
        $methods += ($matcher->isPostEnabled()) ? 2 : 0;
        $methods += ($matcher->isPutEnabled()) ? 4 : 0;
        $methods += ($matcher->isDeleteEnabled()) ? 8 : 0;
        $methods += ($matcher->isPatchEnabled()) ? 16 : 0;
        return ['stubId' => $stub->getStubId()->getValue(), 
                'ownerId' => $stub->getOwnerId()->getValue(), 
                'methods' => $methods, 
                'path' => $matcher->getPath(), 
                'statusCode' => $stub->getResponder()->getStatusCode(), 
                'header' => $stub->getResponder()->getHeader(), 
                'body' => $stub->getResponder()->getBody()];
    }

    private function fromRecord(array $record): Stub
    {
        $stubId = new StubId($record['stubId']);
        $ownerId = new UserId($record['ownerId']);
        $getEnabled = (floor($record['methods'] / 2) * 2 != $record['methods']);
        $postEnabled = (floor($record['methods']) / 4) * 2 != $record['methods'] / 2;
        $putEnabled = (floor($record['methods'] / 8) * 2 != $record['methods'] / 4);
        $deleteEnabled = (floor($record['methods'] / 16) * 2 != $record['methods'] / 8);
        $patchEnabled = (floor($record['methods'] / 32) * 2 != $record['methods'] / 16);
        $matcher = new Matcher($getEnabled, $postEnabled, $putEnabled, $deleteEnabled, $patchEnabled, $record['path']);
        $authorizer = new NoneAuthorizer();
        $responder = new Responder($record['statusCode'], $record['header'], $record['body']);
        return new Stub($stubId, $ownerId, $matcher, $authorizer, $responder);
    }

    /**
     * @return: maybe(Stub)
     */
    public function find(StubId $stubId)
    {
        $sql0 = "SELECT * FROM stub WHERE stubId = :stubId";
        $stt0 = $this->pdo->prepare($sql0);
        $stt0->execute([':stubId' => $stubId->getValue()]);
        $res0 = $stt0->fetch(\PDO::FETCH_ASSOC);
        if ($res0) {
            return $this->fromRecord($res0);
        } else {
            return null;
        }
    }

    public function searchByOwner(UserId $ownerId): StubList
    {
        $sql0 = "SELECT * FROM stub "
              . "WHERE ownerId = :ownerId "
              . "ORDER BY ord";
        $stt0 = $this->pdo->prepare($sql0);
        $stt0->execute([':ownerId' => $ownerId->getValue()]);
        $stubs = [];

        while ($res = $stt0->fetch(\PDO::FETCH_ASSOC)) {
            $stubs[] = $this->fromRecord($res);
        }
        return new StubList($stubs);
    }

    public function storeOrdering(StubList $stubList): void
    {
        if (! count($stubList)) {
            // no items.
            return;
        }

        $ownerId = $stubList[0]->getStubId();
        $sql0 = "DELETE FROM stubOrdering WHERE ownerId = :ownerId";
        $stt0 = $this->pdo->prepare($sql0)
                          ->execute([':ownerId' => $ownerId->getValue()]);
        
        $sql1 = "INSERT INTO stubOrdering VALUES (:ownerId, :ord, :stubId)";
        $stt1 = $this->pdo->prepare($sql1);
        $ord = 1;
        foreach ($stubList as $stub) {
            $stt1->execute([':ownerId' => $ownerId->getValue(), 
                            ':ord' => $ord++, 
                            ':stubId' => $stub->getStubId()->getValue()]);
        }
    }

    public function dispose(Stub $stub): void
    {
        $sql0 = "DELETE FROM stub WHERE stubId = :stubId";
        $this->pdo->prepare($sql0)->execute([':stubId' => $stub->getStubId()->getValue()]);
    }
}