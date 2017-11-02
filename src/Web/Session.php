<?php

namespace Web;

interface Session extends \Domain\Session
{
    public function setFeedback($feedback): void;
    public function getFeedback();
}