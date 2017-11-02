<?php

namespace tests\Domain;

use Domain\Responder;
use tyam\condition\Condition;
use Psr\Http\Message\ResponseInterface as IResponse;
use Zend\Diactoros\Response;
use Custom\Reveal;

class ResponderTest extends BaseCase
{
    use Reveal;

    public function testGetter()
    {
        $r0 = new Responder(200, 'ABC:xyz', 'hello');
        $this->assertEquals($r0->getStatusCode(), 200);
        $this->assertEquals($r0->getHeader(), 'ABC:xyz');
        $this->assertEquals($r0->getBody(), 'hello');
    }

    public function testValidateStatusCode()
    {
        $this->assertEquals(Responder::validateStatusCode(99), Condition::poor('tooSmall'));
        $this->assertEquals(Responder::validateStatusCode(600), Condition::poor('tooLarge'));
        $this->assertEquals(Responder::validateStatusCode(301), COndition::fine(301));
    }

    public function testValidateHeader()
    {
        $invalid = Condition::poor('invalid');
        $this->assertEquals(Responder::validateHeader(''), Condition::fine(''));
        $this->assertEquals(Responder::validateHeader('abc'), $invalid);
        $this->assertEquals(Responder::validateHeader('abc:xyz:123'), $invalid);
        $this->assertEquals(Responder::validateHeader('abc:'), $invalid);
        $this->assertEquals(Responder::validateHeader(':abc'), $invalid);
        $this->assertEquals(Responder::validateHeader("abc:\nxyz"), $invalid);
        
        // 複数の改行コードの混在
        $h0 = "X:abc\r\nY:abc\rZ:abc\nV:abc";
        $this->assertEquals(Responder::validateHeader($h0), Condition::fine($h0));
    }

    public function testEvaluate()
    {
        $evaluate = $this->reveal('Domain\Responder', 'evaluate');
        $env = ['id' => '0001', 'uid' => 'tyam'];

        $this->assertEquals($evaluate('abc', []), 'abc');
        $this->assertEquals($evaluate('abc', $env), 'abc');
        $this->assertEquals($evaluate('abc{id}', $env), 'abc0001');
        $this->assertEquals($evaluate('abc{xyz}', $env), 'abc');
        $this->assertEquals($evaluate('abc{id}-{id}:{uid}', $env), 'abc0001-0001:tyam');
    }

    public function testRespond()
    {
        $env = ['id' => '0001', 'uid' => 'tyam'];
        $responder = new Responder(301, "X-HEADER : abc\rContent-Type:text/plain", 'hello {uid} ({id})');
        
        $res = $responder->respond(new Response(), $env);

        $this->assertEquals($res->getStatusCode(), 301);
        $this->assertTrue($res->hasHeader('X-HEADER'));
        $this->assertEquals($res->getHeader('X-HEADER'), ['abc']);
        $this->assertFalse($res->hasHeader('X-CUSTOM'));
        $this->assertTrue($res->hasHeader('Content-Type'));
        $this->assertEquals($res->getHeader('Content-Type'), ['text/plain']);
        $this->assertEquals($res->getBody().'', 'hello tyam (0001)');
    }
}