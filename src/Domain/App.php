<?php
/**
 * App
 *
 * ドメイン層固有の基本サービス。
 * 継承元のServiceクラスに加えて、セッションが使えるようになっている。
 * なお、このソフトでは、セッションにアクセスする際にはこのクラスを使う
 * ことを必須としている。セッションデータにアクセスするのにメソッドを
 * 使うことで、セッションがグローバル変数領域のようにゴチャゴチャになる
 * のを防ぐ。
 * Web\Sessionも参照のこと。
 */

namespace Domain;

use tyam\radarx\Service;

class App extends Service
{
    private $session;

    public function __construct($logger, $dispatcher, Session $session)
    {
        parent::__construct($logger, $dispatcher);
        $this->session = $session;
    }

    /**
     * 現在のユーザをセッションに保存する。
     *
     * @param UserId $userId
     * @return void
     */
    public static function setCurrentUser(UserId $userId): void 
    {
        self::$singleton->session->setCurrentUser($userId);
    }

    /**
     * 現在のユーザをセッションから取得する。セッションにユーザがセットされてない場合はnullを返す。
     *
     * @return UserId|null 
     */
    public static function getCurrentUser() 
    {
        return self::$singleton->session->getCurrentUser();
    }
}