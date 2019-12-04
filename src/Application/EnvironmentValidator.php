<?php

namespace Emartech\Application;

use Dotenv\Dotenv;
use Dotenv\Exception\ExceptionInterface;

interface EnvironmentValidator
{
    /**
     * @throws ExceptionInterface
     */
    public function validateEnvironment(Dotenv $dotenv): void;
}
