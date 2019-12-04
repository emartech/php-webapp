<?php

namespace Emartech\Application;

use Dotenv\Dotenv;

class Environment
{
    private const ESCHER_CREDENTIAL_SCOPE = 'ESCHER_CREDENTIAL_SCOPE';
    private const ESCHER_KEY_DB = 'ESCHER_KEY_DB';
    private const LOG_LEVEL = 'LOG_LEVEL';
    private const APPLICATION_ENV = 'APPLICATION_ENV';


    public static function create(): self
    {
        return new self();
    }

    public function getEscherCredentialScopeForAuthenticaton(): string
    {
        return $this->getenv(self::ESCHER_CREDENTIAL_SCOPE);
    }

    public function getEscherKeyDb(): array
    {
        return json_decode($this->getenv(self::ESCHER_KEY_DB), true);
    }

    public function getLogLevel(): string
    {
        return $this->getenv(self::LOG_LEVEL) ?: 'INFO';
    }

    public function getApplicationEnv(): string
    {
        return $this->getenv(self::APPLICATION_ENV) ?: 'production';
    }

    public function isTesting(): bool
    {
        return $this->getApplicationEnv() == 'testing';
    }

    public function isDevelopment(): bool
    {
        return $this->getApplicationEnv() == 'development';
    }

    private function getenv($variableName)
    {
        return getenv($variableName);
    }

    public function requireConfig(Dotenv $dotenv): void
    {
        $dotenv->required([
            self::APPLICATION_ENV,
            self::LOG_LEVEL,
        ])->notEmpty();
    }

    public function requireEscherConfig(Dotenv $dotenv): void
    {
        $dotenv->required([
            self::ESCHER_CREDENTIAL_SCOPE,
            self::ESCHER_KEY_DB,
        ])->notEmpty();
    }
}
