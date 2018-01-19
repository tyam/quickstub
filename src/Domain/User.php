<?php
/**
 * User
 *
 * ユーザ。
 * ユーザは当ソフトを操作するエンティティ。
 */

namespace Domain;

use DateTimeImmutable as Datetime;

class User 
{
    private $userId;
    private $displayName;
    private $created;

    public function __construct(UserId $userId, string $displayName, Datetime $created = null)
    {
        $this->userId = $userId;
        $this->displayName = $displayName;
        $this->created = ($created) ? $created : new Datetime();
    }

    /**
     * ユーザを登録する。
     * このメソッドは、ユーザ登録と同時にユーザを当ソフトにログインさせることもできる。
     * 
     * @param Callable $generateId ユーザIDを発行する関数
     * @param string $displayName ユーザの表示名
     * @param bool $doLogin trueが渡された場合には、登録と同時に当ソフトにログインする。
     */
    public static function register(Callable $generateId, string $displayName = '新規ユーザー', bool $doLogin = false)
    {
        $user = new User($generateId(), $displayName);
        if ($doLogin) {
            \Session::setCurrentUser($user->getUserId());
        }
        return $user;
    }

    public function getUserId(): UserId 
    {
        return $this->userId;
    }

    public function getDisplayName(): string 
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName)
    {
        $this->displayName = $displayName;
    }

    public function getCreated(): Datetime
    {
        return $this->created;
    }
}