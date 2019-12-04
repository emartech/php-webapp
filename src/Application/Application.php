<?php

namespace Emartech\Application;


use Psr\Log\LoggerInterface;

interface Application extends EnvironmentValidator
{
    public function createLogger(LoggerFactory $loggerFactory): LoggerInterface;
}
