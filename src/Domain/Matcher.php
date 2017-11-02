<?php

namespace Domain;

use tyam\condition\Condition;
use Psr\Http\Message\ServerRequestInterface as Request;

class Matcher 
{
    private $getEnabled;
    private $postEnabled;
    private $putEnabled;
    private $deleteEnabled;
    private $patchEnabled;
    private $path;  // e.g., /items/{item}/comments/
    //private $fragments;  // path fragments e.g., ['items', '{item}', 'comments', ''] for /items/{item}/comments/

    public function __construct(bool $getEnabled, 
                                bool $postEnabled, 
                                bool $putEnabled, 
                                bool $deleteEnabled, 
                                bool $patchEnabled, 
                                string $path)
    {
        $this->getEnabled = $getEnabled;
        $this->postEnabled = $postEnabled;
        $this->putEnabled = $putEnabled;
        $this->deleteEnabled = $deleteEnabled;
        $this->patchEnabled = $patchEnabled;
        $this->path = $path;
    }

    public function isGetEnabled(): bool
    {
        return $this->getEnabled;
    }

    public function isPostEnabled(): bool
    {
        return $this->postEnabled;
    }

    public function isPutEnabled(): bool
    {
        return $this->putEnabled;
    }

    public function isDeleteEnabled(): bool
    {
        return $this->deleteEnabled;
    }

    public function isPatchEnabled(): bool
    {
        return $this->patchEnabled;
    }

    public function getPath(): string 
    {
        return $this->path;
    }

    protected static function explodePath($path)
    {
        // remove heading slash
        if ($path[0] === '/') {
            $path = substr($path, 1);
        }

        return explode('/', $path);
    }

    /**
     * validation method
     */
    public static function validatePath($val)
    {
        if (strlen($val) == 0) {
            return Condition::poor('empty');
        }
        if ($val[0] !== '/') {
            return Condition::poor('invalid');
        }
        
        $frags = self::explodePath($val);
        $vars = [];
        $len = count($frags);
        for ($i = 0; $i < $len; $i++) {
            $frag = $frags[$i];
            $ms = [];
            if (strpos($frag, '{') !== false) {
                if (! preg_match('/^\{(.*)\}$/', $frag, $ms)) {
                    return Condition::poor('invalid');
                }
                if (strlen($ms[1]) === 0) {
                    return Condition::poor('invalid');
                }
                if (isset($vars[$ms[1]])) {
                    return Condition::poor('duplicate');
                }
                $vars[$ms[1]] = true;
            } else {
                if ($i != $len - 1 && strlen($frag) === 0) {
                    return Condition::poor('invalid');
                }
            }
        }
        return Condition::fine($val);
    }

    protected function matchFragment($patn, $val)
    {
        if (strlen($patn) > 0 && $patn[0] == '{') {
            // case pattern
            $var = substr($patn, 1, strlen($patn) - 2);
            return [$var => $val];
        } else {
            // case const
            if ($patn === $val) {
                return [];
            } else {
                return false;
            }
        }
    }

    protected function matchMethod($method)
    {
        if ($method === 'GET' || $method === 'HEAD') {
            return $this->getEnabled;
        } else if ($method === 'POST') {
            return $this->postEnabled;
        } else if ($method === 'PUT') {
            return $this->putEnabled;
        } else if ($method === 'DELETE') {
            return $this->deleteEnabled;
        } else if ($method === 'PATCH') {
            return $this->patchEnabled;
        } else {
            return false;
        }
    }

    public function match(Request $request)
    {
        $method = $request->getMethod();
        $vals = self::explodePath($request->getUri()->getPath());
        $pats = self::explodePath($this->path);

        if (! $this->matchMethod($method)) {
            return false;
        }
        if (count($vals) !== count($pats)) {
            return false;
        }

        $len = count($vals);
        $vars = [];
        for ($i = 0; $i < $len; $i++) {
            $kv = $this->matchFragment($pats[$i], $vals[$i]);
            if ($kv === false) {
                return false;
            }
            $vars += $kv;
        }

        return $vars;
    }
}