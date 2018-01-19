<?php
/**
 * AccessEvent
 *
 * このソフトでは、スタブとアクセス履歴の依存関係が小さくなるように配慮している。
 * 実際にスタブにアクセスがあった際、その対応はスタブ側で行うが、アクセスが
 * あったことをスタブからアクセス履歴側に伝えるために、PubSubを使う。
 * このクラスはそのイベントクラス。
 */

namespace Domain;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTimeImmutable as Datetime;

class AccessEvent
{
    private $request;
    private $stub;
    private $response;
    private $accessed;

    public function __construct(Request $request, Stub $stub, Response $response, Datetime $accessed = null)
    {
        $this->request = $request;
        $this->stub = $stub;
        $this->response = $response;
        if (is_null($accessed)) {
            $accessed = new Datetime();
        }
        $this->accessed = $accessed;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getStub(): Stub
    {
        return $this->stub;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getAccessed(): Datetime
    {
        return $this->accessed;
    }
}