<?php

namespace Custom;

trait Reveal 
{
    public function reveal($obj, string $method): \Closure 
    {
        $m = new \ReflectionMethod($obj, $method);
        return $m->getClosure();
    }
}