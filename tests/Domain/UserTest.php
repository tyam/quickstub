<?php

namespace tests\Domain;

use PHPUnit\Framework\TestCase;
use Domain\User;
use Domain\UserId;

class UserTest extends TestCase
{
    public function generateId()
    {
        return new UserId('1');
    }
    public function testRegister()
    {
        $user = User::register([$this, 'generateId']);
        $this->assertEquals($user->getUserId()->getValue(), '1');
        $this->assertEquals($user->getDisplayName(), '新規ユーザー'); 
    }
}