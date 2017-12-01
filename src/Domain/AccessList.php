<?php
/**
 * AccessList
 *
 * アクセスのリスト。
 */

namespace Domain;

class AccessList implements \IteratorAggregate, \ArrayAccess, \Countable
{
    private $accesses;

    public function __construct(array $accesses)
    {
        $this->accesses = $accesses;
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->accesses);
    }

    public function offsetExists($offset)
    {
        return isset($this->accesses[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->accesses[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('not allowed');
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('not allowed');
    }

    public function count()
    {
        return count($this->accesses);
    }
}