<?php

namespace tests\Domain;

use Domain\Stub;
use Domain\StubId;
use Domain\UserId;
use Domain\Matcher;
use Domain\NoneAuthorizer;
use Domain\Responder;
use tyam\condition\Condition;
use Psr\Http\Message\ResponseInterface as IResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class StubTest extends BaseCase
{
    public function setUp()
    {
        parent::setUp();
        \App::setCurrentUser(new UserId(5));
    }

    public function createGenerator(int $i)
    {
        return function () use ($i) {
            return new StubId($i);
        };
    }

    public function testGetter()
    {
        $stub = Stub::create($this->createGenerator(3));
        $this->assertEquals($stub->getStubId()->getValue(), 3);
        $this->assertEquals($stub->getOwnerId()->getValue(), 5);
        $this->assertEquals($stub->getMatcher(), new Matcher(true, false, false, false, false, '/stub3'));
        $this->assertEquals($stub->getAuthorizer(), new NoneAuthorizer());
        $this->assertEquals($stub->getResponder(), new Responder(200, '', 'Here QUICKSTUB is!'));
    }

    public function testModify()
    {
        $stub = Stub::create($this->createGenerator(10));
        $m = new Matcher(true, true, true, true, true, '/my/abc');
        $a = new NoneAuthorizer();
        $r = new Responder(301, 'X-HEADER: xyz', 'fine');
        $stub->modify($m, $a, $r);

        $this->assertEquals($stub->getMatcher(), $m);
        $this->assertEquals($stub->getAuthorizer(), $a);
        $this->assertEquals($stub->getResponder(), $r);
    }

    private function mockRequest($method, $path)
    {
        $server = ['REQUEST_METHOD' => $method, 
                   'REQUEST_URI' => $path, 
                   'PATH_INFO' => $path];
        return ServerRequestFactory::fromGlobals($server, [], [], [], [], []);
    }

    public function testExecute()
    {
        $stub = Stub::create($this->createGenerator(7));
        $m = new Matcher(true, false, false, false, false, '/item/{id}');
        $a = new NoneAuthorizer();
        $r = new Responder(200, 'X-HEADER: xyz', 'fine, {id}');
        $stub->modify($m, $a, $r);

        $result = $stub->execute($this->mockRequest('GET', '/item/3'), new Response());
        $this->assertEquals($result->getStatusCode(), 200);
        $this->assertEquals($result->getHeader('X-HEADER'), ['xyz']);
        $this->assertEquals($result->getBody().'', 'fine, 3');

        $result = $stub->execute($this->mockRequest('GET', '/item/3/x'), new Response());
        $this->assertNull($result);
    }
}