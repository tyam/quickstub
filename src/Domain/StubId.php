<?php
/**
 * StubId
 */

namespace Domain;

use tyam\condition\Condition;

class StubId
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromInt(int $i): StubId
    {
        return new StubId(sprintf('%06d', $i));
    }

    public static function validateValue($x): Condition
    {
        $i = intval($x);
        if (sprintf('%06d', $i) === $x) {
            return Condition::fine($x);
        } else {
            return Condition::poor('invalid');
        }
    }

    public function toInt(): int
    {
        return intval($this->value);
    }
}