<?php
/**
 * Responder
 *
 * スタブのレスポンスを作るオブジェクト。スタブのVO。
 */

namespace Domain;

use tyam\condition\Condition;
use Psr\Http\Message\ResponseInterface as Response;

class Responder 
{
    private $statusCode;
    private $header;
    private $body;

    public function __construct(int $statusCode, string $header, string $body)
    {
        $this->statusCode = $statusCode;
        $this->header = $header;
        $this->body = $body;
    }

    public function getStatusCode(): int 
    {
        return $this->statusCode;
    }

    public function getHeader(): string 
    {
        return $this->header;
    }

    public function getBody(): string 
    {
        return $this->body;
    }

    /**
     * statusCodeのバリデーションメソッド
     *
     * @param int $val
     * @return Condition(int,string) 成功時にはステータスコードを整数で返す、失敗時にはエラー文字列を返す。
     */
    public static function validateStatusCode(int $val)
    {
        if ($val < 100) {
            return Condition::poor('tooSmall');
        }
        if ($val > 599) {
            return Condition::poor('tooLarge');
        }
        return Condition::fine($val);
    }

    /**
     * 複数行のヘッダ文字列を、ヘッダ名とヘッダ値のリストのリストに分解する。
     *
     * @param string $header
     * @return array [[$name, $value], ...]
     */
    protected static function explodeHeader(string $header)
    {
        $rv = [];
        $lines = preg_split("/\r\n|\r|\n/", $header);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) === 0) {
                continue;
            }
            $rv[] = array_map('trim', explode(':', $line));
        }
        return $rv;
    }

    /**
     * 複数行のヘッダ文字列のバリデーションメソッド
     * 
     * @param string $header
     * @return Condition(string,string) 成功時にはヘッダ文字列を、失敗時にはエラー文字列を返す。
     */
    public static function validateHeader(string $header)
    {
        $lines = self::explodeHeader($header);
        foreach ($lines as $line) {
            if (count($line) != 2) {
                return Condition::poor('invalid');
            }
            list($name, $value) = $line;
            if ($name === '') {
                return Condition::poor('invalid');
            }
            if ($value === '') {
                return Condition::poor('invalid');
            }
        }
        return Condition::fine($header);
    }

    /**
     * 文字列`subject`に対して、環境`env`で差し込みを行う。
     *
     * @param string $subject
     * @param array $env 環境（変数名から値へのマッピング）
     * @return string 差込後の文字列
     */
    protected static function evaluate($subject, $env)
    {
        $lookup = function ($matches) use ($env)
        {
            if (isset($env[$matches[1]])) {
                return $env[$matches[1]];
            } else {
                return '';
            }
        };
        return preg_replace_callback('/{([a-zA-Z0-9]+)}/', $lookup, $subject);
    }

    /**
     * HTTPレスポンスを作成する。
     * 本来、このメソッドは`response`パラメータを受け取る必要はないが、PSR-7には
     * HTTPレスポンスをnewするI/Fが無いためこのような形にした。
     *
     * @param ResponseInterface $response ベースとなるレスポンス
     * @param array $env 環境（変数名から値へのマッピング）
     * @return ResponseInterface 作成されたレスポンス
     */
    public function respond(Response $response, $env): Response
    {
        $response = $response->withStatus($this->statusCode);

        $headers = self::explodeHeader($this->header);
        foreach ($headers as $header) {
            list($name, $value) = $header;
            $response = $response->withAddedHeader(self::evaluate($name, $env), self::evaluate($value, $env));
        }

        $stream = $response->getBody();
        $stream->write(self::evaluate($this->body, $env));

        return $response;
    }
}