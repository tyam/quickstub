<?php

namespace Domain;

class UserId
{
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return sprintf('%06d', $this->value);
    }

    public function getValue(): int
    {
        return $this->value;
    }
}