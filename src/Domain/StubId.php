<?php

namespace Domain;

class StubId
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string 
    {
        return $this->value;
    }
}