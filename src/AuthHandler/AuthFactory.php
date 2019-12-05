<?php

namespace Emartech\AuthHandler;

use Escher\Escher;

class AuthFactory
{
    private $keyDb;
    private $credentialScope;

    public function __construct(array $keyDb, string $credentialScope)
    {
        $this->keyDb = $keyDb;
        $this->credentialScope = $credentialScope;
    }

    public function createEscherAuth(): AuthHandlerInterface
    {
        return new EscherAuth($this->createEscher(), $this->keyDb);
    }

    public function createEscher(): Escher
    {
        return Escher::create($this->credentialScope)
            ->setAlgoPrefix('EMS')
            ->setVendorKey('EMS')
            ->setAuthHeaderKey('X-Ems-Auth')
            ->setDateHeaderKey('X-Ems-Date');
    }
}
