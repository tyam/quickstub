<?php

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use tyam\condition\Condition;

class StubList implements \IteratorAggregate, \ArrayAccess, \Countable
{
    private $stubs;
    private $responder404;

    public static function createDefaultResponder404(): Responder
    {
        $responder = new Responder(404, '', '');
        return $responder;
    }

    public function __construct(array $stubs, Responder $responder404 = null)
    {
        $this->stubs = $stubs;
        if (is_null($responder404)) {
            $responder404 = self::createDefaultResponder404();
        }
        $this->responder404 = $responder404;
    }

    public function getResponder404(): Responder
    {
        return $this->responder404;
    }

    public function setResponder404(Responder $responder404): void
    {
        $this->responder404 = $responder404;
    }

    public function indexOf(StubId $stubId): int 
    {
        $len = count($this->stubs);
        for ($i = 0; $i < $len; $i++) {
            if ($this->stubs[$i]->getStubId() == $stubId) {
                return $i;
            }
        }
        return -1;
    }

    public function moveItem(StubId $stubId, int $index): void
    {
        $from = $this->indexOf($stubId);
        list($target) = array_splice($this->stubs, $from, 1);
        array_splice($this->stubs, $index, 0, [$target]);
    }

    public function execute(Request $request, Response $response)
    {
        foreach ($this->stubs as $stub) {
            $result = $stub->execute($request, $response);
            if (! is_null($result)) {
                return $result;
            }
        }
        return $this->responder404->respond($response, []);
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->stubs);
    }

    public function offsetExists($offset)
    {
        return isset($this->stubs[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->stubs[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->stubs[] = $value;
        } else {
            $this->stubs[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        array_splice($this->stubs, $offset, 1);
    }

    public function count()
    {
        return count($this->stubs);
    }
}