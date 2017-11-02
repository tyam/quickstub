<?php

namespace tests\Domain;

use Domain\Stub;
use Domain\StubId;
use Domain\UserId;
use Domain\Matcher;
use Domain\NoneAuthenticator;
use Domain\Responder;
use Domain\StubList;
use tyam\condition\Condition;
use Psr\Http\Message\ResponseInterface as IResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Custom\Reveal;

class StubListTest extends BaseCase
{
    public function setUp()
    {
        parent::setUp();
        \App::setCurrentUser(new UserId(5));
    }

    private function createGenerator(int $i)
    {
        return function () use ($i) {
            return new StubId($i);
        };
    }

    private function createStub($id, $method, $path, $statusCode, $body)
    {
        $getEnabled = ($method == 'GET');
        $postEnabled = ($method == 'POST');
        return new Stub(new StubId($id), 
                        \App::getCurrentUser(), 
                        new Matcher($getEnabled, $postEnabled, false, false, false, $path), 
                        new NoneAuthenticator(), 
                        new Responder($statusCode, 'X-HEADER: abc', $body));
    }

    private function createStubList()
    {
        $stubs = [
            $this->createStub(1, 'GET', '/profile', 200, 'hello'), 
            $this->createStub(10, 'POST', '/items/new', 301, 'item created'), 
            $this->createStub(2, 'GET', '/items/{id}', 200, 'here is {id}'), 
            $this->createStub(3, 'POST', '/items/{id}', 301, '{id} modified')
        ];
        return new StubList($stubs);
    }

    public function testBasic()
    {
        $list = $this->createStubList();
        $this->assertEquals(count($list), 4);
        $this->assertEquals($list[2], $this->createStub(2, 'GET', '/items/{id}', 200, 'here is {id}'));
        unset($list[1]);
        $this->assertEquals(count($list), 3);
        $this->assertEquals($list[2], $this->createStub(3, 'POST', '/items/{id}', 301, '{id} modified'));
    }

    public function testMoveItem()
    {
        $list = $this->createStubList();        
        $list->moveItem(new StubId(2), 0);
        $this->assertEquals($list[0]->getStubId()->getValue(), 2);
        $this->assertEquals($list[1]->getStubId()->getValue(), 1);
        
        $list = $this->createStubList();
        $list->moveItem(new StubId(10), 2);
        $this->assertEquals($list[1]->getStubId()->getValue(), 2);
        $this->assertEquals($list[2]->getStubId()->getValue(), 10);

        $list = $this->createStubList();
        $list->moveItem(new StubId(10), 3);
        $this->assertEquals($list[2]->getStubId()->getValue(), 3);
        $this->assertEquals($list[3]->getStubId()->getValue(), 10);
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
        $list = $this->createStubList();

        $r0 = $list->execute($this->mockRequest('GET', '/profile'), new Response());
        $this->assertEquals($r0->getBody().'', 'hello');

        $r1 = $list->execute($this->mockRequest('POST', '/items/new'), new Response());
        $this->assertEquals($r1->getBody().'', 'item created');

        $r2 = $list->execute($this->mockRequest('GET', '/items/3'), new Response());
        $this->assertEquals($r2->getBody().'', 'here is 3');

        $list->moveItem(new StubId(10), 3);
        $r3 = $list->execute($this->mockRequest('POST', '/items/new'), new Response());
        $this->assertEquals($r3->getBody().'', 'new modified');
    }
}