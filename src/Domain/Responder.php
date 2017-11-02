<?php

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