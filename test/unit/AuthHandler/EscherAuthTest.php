<?php

namespace Test\Unit\AuthHandler;

use Emartech\AuthHandler\EscherAuth;
use Emartech\AuthHandler\Exception as AuthHandlerException;
use Escher\Exception as EscherException;
use Escher\Escher;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EscherAuthTest extends TestCase
{
    /**
     * @var Escher|MockObject
     */
    private $escher;

    /**
     * @var EscherAuth
     */
    private $authHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->escher = $this->createMock(Escher::class);
        $this->authHandler = new EscherAuth($this->escher, []);
    }

    /**
     * @test
     * @throws AuthHandlerException
     */
    public function authenticate_Perfect_Perfect()
    {
        $this->expectAuthentication();

        $this->authHandler->authenticate();
    }

    /**
     * @test
     * @throws AuthHandlerException
     */
    public function authenticate_EscherFails_ExceptionThrown()
    {
        $this->expectException(AuthHandlerException::class);
        $this->expectAuthentication()->willThrowException(new EscherException());

        $this->authHandler->authenticate();
    }

    private function expectAuthentication(): InvocationMocker
    {
        return $this->escher->expects($this->once())->method('authenticate');
    }
}
