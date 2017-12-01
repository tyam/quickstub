<?php
/**
 * アクセスのレポジトリ
 */

namespace Domain;

interface AccessRepository
{
    /**
     * アクセス（Domain\Access）のIDを採番する。
     *
     * @return int
     */
    public function reserveId(): int;
    
    /**
     * アクセスオブジェクトを永続化する。
     *
     * @param Access $access
     * @return void
     */
    public function store(Access $access): void;

    /**
     * 特定のスタブのアクセス履歴を取得する。
     *
     * @param StubId $stubId 対象のスタブのID
     * @param int $maxNum 取得する履歴の最大数（デフォルト20）
     * @param int $maxAge 何秒以内の履歴を取得するか（デフォルト120）
     * @return AccessList
     */
    public function searchByStub(StubId $stubId, int $maxNum = 20, int $maxAge = 120): AccessList;

    /**
     * 特定のユーザ（が所有する全スタブ）のアクセス履歴を取得する。
     *
     * @param UserId $userId 対象のユーザのID
     * @param int $maxNum 取得する履歴の最大数（デフォルト20）
     * @param int $maxAge 何秒以内の履歴を取得するか（デフォルト120）
     * @return AccessList
     */
    public function searchByUser(UserId $userId, int $maxNum = 20, int $maxAge = 120): AccessList;
}