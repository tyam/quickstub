<?php

namespace Store;

use Domain\UserRepository;
use Domain\User;
use Domain\UserId;
use DateTimeImmutable as Datetime;

class UserMapper implements UserRepository
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function reserveId(): UserId 
    {
        $sql = "SELECT nextval('userId') AS userId";
        $stt = $this->pdo->query($sql);
        $userId = $stt->fetchColumn();
        return new UserId($userId);
    }

    public function store(User $user)
    {
        $record = $this->toRecord($user);
        if ($this->find($user->getUserId())) {
            // update
            $sql0 = "UPDATE user SET displayName = :displayName WHERE userId = :userId";
            $this->pdo->prepare($sql0)
                      ->execute([':displayName' => $record['displayName'], 
                                 ':userId' => $record['userId']]);
        } else {
            // delete
            $sql0 = "DELETE FROM user";
            $this->pdo->prepare($sql0)->execute([]);
            // insert
            $sql1 = "INSERT INTO user VALUES (:userId, :displayName, :created)";
            $this->pdo->prepare($sql1)
                      ->execute([':userId' => $record['userId'], 
                                 ':displayName' => $record['displayName'], 
                                 ':created' => $record['created']]);
        }
    }

    private function toRecord(User $user)
    {
        return [
            'userId' => $user->getUserId()->getValue(), 
            'displayName' => $user->getDisplayName(), 
            'created' => $user->getCreated()->format('Y-m-d H:i:s')
        ];
    }

    private function fromRecord($user)
    {
        $userId = new UserId($user['userId']);
        $displayName = $user['displayName'];
        $created = new Datetime($user['created']);
        return new User($userId, $displayName, $created);
    }

    public function find(UserId $userId)
    {
        $sql0 = "SELECT * FROM user WHERE userId = :userId";
        $stt0 = $this->pdo->prepare($sql0);
        $stt0->execute([':userId' => $userId->getValue()]);
        $res0 = $stt0->fetch(\PDO::FETCH_ASSOC);
        if ($res0) {
            return $this->fromRecord($res0);
        } else {
            return null;
        }
    }

    public function getTheUserId(): UserId
    {
        $sql0 = "SELECT * FROM user";
        $stt0 = $this->pdo->prepare($sql0);
        $stt0->execute([]);
        $res0 = $stt0->fetch(\PDO::FETCH_ASSOC);
        $userId = new UserId($res0['userId']);
        return $userId;
    }
}