<?php
/**
 * Session
 *
 * プレゼンテーション層のセッションインターフェイス。
 * 同じセッションでも、ドメイン層とプレゼンテーション層では使い方が異なるため、インターフェイスを分離している。
 */

namespace Web;

use tyam\radarx\CsrfTokenHolder;

interface Session extends \Domain\Session, CsrfTokenHolder 
{
    /**
     * フィードバック識別子をセットする。
     * セットされたフィードバックは、次のHTTPリクエストの終了後に削除される。
     *
     * @param mixed $feedback
     * @return void
     */
    public function setFeedback($feedback): void;

    /**
     * フィードバック識別子を取得する。
     * 取得できるのは、前回のHTTPリクエストでセットされたフィードバック識別子。
     * 前回のHTTPリクエストの最中にフィードバック識別子がセットされていない場合はnullを返す。
     *
     * @return mixed
     */
    public function getFeedback();
}