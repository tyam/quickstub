<?php

namespace Web;

use tyam\bamboo\Engine;

class AbstractResponder 
{
    protected $bamboo;
    protected $session;

    public function __construct(Engine $bamboo, Session $session)
    {
        $this->bamboo = $bamboo;
        $this->session = $session;

        $bamboo->loadFunctions();
    }
}