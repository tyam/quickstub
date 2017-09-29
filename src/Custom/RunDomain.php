<?php

namespace Custom;

trait RunDomain 
{
    private $resolve;

    public function setResolve(Callable $resolve)
    {
        $this->resolve = $resolve;
    }

    public function runDomain($spec, $args) 
    {
        $domain = $this->resolve($spec);
        $payload = call_user_func_array($domain, $args);
        return $payload;
    }
}