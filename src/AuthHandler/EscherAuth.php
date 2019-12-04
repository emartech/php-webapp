<?php

namespace Emartech\AuthHandler;

use Escher\Escher;
use Escher\Exception as EscherException;

class EscherAuth implements AuthHandlerInterface
{
    private $escher;
    private $keyDB;

    public function __construct(Escher $escher, array $keyDB)
    {
        $this->escher = $escher;
        $this->keyDB = $keyDB;
    }

    /**
     * @throws Exception
     */
    public function authenticate()
    {
        try {
            $this->escher->authenticate($this->keyDB);
        } catch (EscherException $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
