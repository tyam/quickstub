<?php

namespace Web;

use tyam\bamboo\Engine;

class AbstractResponder 
{
    protected $bamboo;

    public function __construct(Engine $bamboo)
    {
        $this->bamboo = $bamboo;

        $bamboo->loadFunctions();
    }
}