<?php

namespace tests\Domain;

use PHPUnit\Framework\TestCase;
use Domain\User;
use Domain\UserId;

class UserTest extends BaseCase
{
    private $sequence;

    public function generateId()
    {
        return new UserId($this->sequence++);
    }
    public function testRegister()
    {
        $this->sequence = 1;
        $user = User::register([$this, 'generateId']);
        $this->assertEquals($user->getUserId()->getValue(), 1);
        $this->assertEquals($user->getDisplayName(), '新規ユーザー'); 
    }
    public function testLogin()
    {
        $this->sequence = 2;
        $user = User::register([$this, 'generateId'], 'My Name', true);
        $this->assertEquals($user->getUserId()->getValue(), 2);
        $this->assertEquals($user->getDisplayName(), 'My Name');
        $this->assertEquals($user->getUserId(), \Session::getCurrentUser());
    }
}