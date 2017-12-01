<?php
/**
 * UserRepository
 */

namespace Domain;

interface UserRepository 
{
    /**
     * ユーザIDを発行する。
     *
     * @return UserId
     */
    public function reserveId(): UserId;

    /**
     * ユーザを永続化する。
     *
     * @param User $user
     * @return void
     */
    public function store(User $user);

    /**
     * `userId`で指定されるIDに合致するユーザの情報を引き出す。該当するユーザ情報が無い場合はnullを返す。
     *
     * @param UserId $userId
     * @return User|null
     */
    public function find(UserId $userId);

    /**
     * 暗黙のユーザIDを返す。
     * 当ソフトはマルチユーザ向けを視野に入れつつも、現在はシングルユーザ向けである。
     * シングルユーザの場合は、ユーザ登録やログインなどは不要なので、代わりに当ソフトは暗黙のユーザ情報を用いる。
     * このメソッドは、その暗黙のユーザIDを取得するもの。
     *
     * @return UserId
     */
    public function getTheUserId(); UserId;
}