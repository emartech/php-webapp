<?php

namespace Emartech\AuthHandler;

interface AuthHandlerInterface
{
    /**
     * @return void
     * @throws Exception
     */
    public function authenticate();
}
