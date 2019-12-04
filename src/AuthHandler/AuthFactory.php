<?php

namespace Participation;

use Dotenv\Dotenv;
use Dotenv\Exception\ExceptionInterface;
use Emartech\Application\EnvironmentValidator;
use Emartech\AuthHandler\AuthHandlerInterface;
use Emartech\AuthHandler\EscherAuth;
use Escher\Escher;

class AuthFactory implements EnvironmentValidator
{
    private $env;

    public static function create(Environment $env): self
    {
        return new self($env);
    }

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    /**
     * @throws ExceptionInterface
     */
    public function validateEnvironment(Dotenv $dotenv): void
    {
        $this->env->requireEscherConfigForAuthentication($dotenv);
    }

    public function createEscherAuth(): AuthHandlerInterface
    {
        return new EscherAuth($this->createEscher(), $this->env->getEscherKeyDb());
    }

    public function createEscher(): Escher
    {
        return Escher::create($this->env->getEscherCredentialScopeForAuthentication())
            ->setAlgoPrefix('EMS')
            ->setVendorKey('EMS')
            ->setAuthHeaderKey('X-Ems-Auth')
            ->setDateHeaderKey('X-Ems-Date');
    }
}
