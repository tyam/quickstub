<?php

namespace Domain;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use tyam\condition\Condition;

class Stub
{
    private $stubId;
    private $ownerId;
    private $matcher;
    private $authenticator;
    private $responder;

    public function __construct(StubId $stubId, 
                                UserId $ownerId, 
                                Matcher $matcher, 
                                Authenticator $authenticator, 
                                Responder $responder)
    {
        $this->stubId = $stubId;
        $this->ownerId = $ownerId;
        $this->matcher = $matcher;
        $this->authenticator = $authenticator;
        $this->responder = $responder;
    }

    public static function create(Callable $reserveId)
    {
        $stubId = $reserveId();
        $ownerId = \App::getCurrentUser();
        $matcher = new Matcher(true, false, false, false, false, '/stub'.$stubId->getValue());
        $authenticator = new NoneAuthenticator();
        $responder = new Responder(200, '', 'Here QUICKSTUB is!');
        return new Stub($stubId, $ownerId, $matcher, $authenticator, $responder);
    }

    /**
     * @return maybe(Response); 
     */
    public function execute(Request $request, Response $response)
    {
        $result = $this->matcher->match($request);
        if ($result === false) {
            return null;
        }
        $vars = $result;

        if (! $this->authenticator->authenticate($request)) {
            return null;
        }

        return $this->responder->respond($response, $vars);
    }

    public function getStubId(): StubId 
    {
        return $this->stubId;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getMatcher(): Matcher 
    {
        return $this->matcher;
    }

    public function getAuthenticator(): Authenticator
    {
        return $this->authenticator;
    }

    public function getResponder(): Responder 
    {
        return $this->responder;
    }

    public function modify(Matcher $matcher, Authenticator $authenticator, Responder $responder)
    {
        $this->matcher = $matcher;
        $this->authenticator = $authenticator;
        $this->responder = $responder;
    }
}