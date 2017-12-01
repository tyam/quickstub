<?php
/**
 * StubRepository
 *
 * スタブのレポジトリ。
 */

namespace Domain;

interface StubRepository
{
    /**
     * StubIdを発行する。
     *
     * @return StubId 発行されたStubId
     */
    public function reserveId(): StubId;

    /**
     * スタブを永続化する。
     *
     * @param Stub $stub
     */
    public function store(Stub $stub): void;

    /**
     * IDが`stubId`であるスタブを引き出す。該当するスタブが無い場合にはnullを返す。
     *
     * @param StubId $stubId 引き出すスタブのID
     * @return Stub|null 
     */
    public function find(StubId $stubId);

    /**
     * ユーザが所有しているスタブのリストを引き出す。
     *
     * @param UserId $userId 引き出すスタブの所有者のID
     * @return StubList
     */
    public function searchByOwner(UserId $userId): StubList;

    /**
     * スタブリストの順序の入れ替えを永続化する。
     *
     * @param StubList $stubs
     */
    public function storeOrdering(StubList $stubs): void;

    /**
     * スタブを抹消する。このメソッドはオブジェクト化されているスタブを削除することはできない。
     *
     * @param Stub $stub
     */
    public function dispose(Stub $stub): void;
}