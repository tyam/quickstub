<?php
/**
 * ドメイン層のセッション
 *
 * このソフトでは、クラスでセッションを取り扱う。
 * セッションデータをどこに保存するかはストレージ層の話なので、ドメイン層ではインターフェイスのみ定義する。
 */

namespace Domain;

interface Session
{
    /**
     * 現在のユーザをセッションから取得する。現在のユーザがセットされてない場合はnullを返す。
     *
     * @return UserId|null 
     */
    public function getCurrentUser(): UserId;

    /**
     * 現在のユーザをセッションにセットする。
     *
     * @param UserId $userId
     */
    public function setCurrentUser(UserId $userId): void;
}