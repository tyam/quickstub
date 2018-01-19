<?php

namespace App;

use Domain\AccessRepository;
use Domain\AccessEvent;
use Domain\Access;

class AccessEntry 
{
    private $accessRepo;

    public function __construct(AccessRepository $accessRepo) 
    {
        $this->accessRepo = $accessRepo;
    }

    public function __invoke($ev)
    {
        $access = Access::createFromEvent([$this->accessRepo, 'reserveId'], $ev);
        $this->accessRepo->store($access);
    }
}