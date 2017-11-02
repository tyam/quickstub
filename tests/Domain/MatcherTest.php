<?php

namespace tests\Domain;

use Domain\Matcher;
use tyam\condition\Condition;
use Zend\Diactoros\ServerRequestFactory;

class MatcherTest extends BaseCase
{
    public function testGetter()
    {
        $m = new Matcher(true, true, true, true, true, '/path/to/something');
        $this->assertTrue($m->isGetEnabled() && 
                          $m->isPostEnabled() &&
                          $m->isPutEnabled() &&
                          $m->isDeleteEnabled() &&
                          $m->isPatchEnabled());
        $this->assertEquals($m->getPath(), '/path/to/something');

        $m2 = new Matcher(false, false, false, false, false, '/');
        $this->assertTrue(! $m2->isGetEnabled() && 
                          ! $m2->isPostEnabled() && 
                          ! $m2->isPutEnabled() && 
                          ! $m2->isDeleteEnabled() && 
                          ! $m2->isPatchEnabled());
        $this->assertEquals($m2->getPath(), '/');
    }

    public function testValidatePath()
    {
        $this->assertEquals(Matcher::validatePath(''), Condition::poor('empty'));
        $this->assertEquals(Matcher::validatePath('abc'), Condition::poor('invalid'));
        $this->assertEquals(Matcher::validatePath('/'), Condition::fine('/'));
        $this->assertEquals(Matcher::validatePath('/list/{id}'), Condition::fine('/list/{id}'));
        $this->assertEquals(Matcher::validatePath('/list/new/'), Condition::fine('/list/new/'));
        $this->assertEquals(Matcher::validatePath('/list/{id'), Condition::poor('invalid'));
        $this->assertEquals(Matcher::validatePath('/list/i{id}'), Condition::poor('invalid'));
        $this->assertEquals(Matcher::validatePath('/list/{}'), Condition::poor('invalid'));
        $this->assertEquals(Matcher::validatePath('/list/{id}/{id}'), Condition::poor('duplicate'));
        $this->assertEquals(Matcher::validatePath('/list//abc'), Condition::poor('invalid'));
    }

    private function mockRequest($method, $path)
    {
        $server = ['REQUEST_METHOD' => $method, 
                   'REQUEST_URI' => $path, 
                   'PATH_INFO' => $path];
        return ServerRequestFactory::fromGlobals($server, [], [], [], [], []);
    }

    public function testMatch0()
    {
        $m = new Matcher(true, true, true, true, true, '/');

        $r0 = $m->match($this->mockRequest('GET', '/'));
        $this->assertEquals($r0, []);

        $r1 = $m->match($this->mockRequest('POST', '/list'));
        $this->assertFalse($r1);

        $r2 = $m->match($this->mockRequest('DELETE', '/'));
        $this->assertEquals($r2, []);
    }

    public function testMatch1()
    {
        $m = new Matcher(true, false, false, false, false, '/list');

        $r0 = $m->match($this->mockRequest('POST', '/list'));
        $this->assertFalse($r0);

        $r1 = $m->match($this->mockRequest('GET', '/list'));
        $this->assertEquals($r1, []);

        $r2 = $m->match($this->mockRequest('GET', '/list/'));
        $this->assertFalse($r2);

        $r3 = $m->match($this->mockRequest('GET', '/'));
        $this->assertFalse($r3);

        $r4 = $m->match($this->mockRequest('GET', '/listsomething'));
        $this->assertFalse($r4);
    }

    public function testMatch2()
    {
        $m = new Matcher(false, true, true, false, false, '/list/{id}');

        $r0 = $m->match($this->mockRequest('POST', '/list/123'));
        $this->assertEquals($r0, ['id' => '123']);

        $r1 = $m->match($this->mockRequest('DELETE', '/list/123'));
        $this->assertFalse($r1);

        $r2 = $m->match($this->mockRequest('PUT', '/list/123/'));
        $this->assertFalse($r2);
    }

    public function testMatch3()
    {
        $m = new Matcher(false, true, true, false, false, '/{id}/comments/{cid}/');

        $r0 = $m->match($this->mockRequest('POST', '/123/comments/4/'));
        $this->assertEquals($r0, ['id' => '123', 'cid' => '4']);

        $r1 = $m->match($this->mockRequest('POST', '/123/comments/4'));
        $this->assertFalse($r1);
    }
}