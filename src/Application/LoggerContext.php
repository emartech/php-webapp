<?php

namespace Emartech\Application;


use Psr\Log\LoggerInterface;

trait LoggerContext
{
    public function createContextLogger(LoggerInterface $logger): LoggerInterface
    {
        return new ContextLogger($logger, $this->getLogContext());
    }

    public abstract function getLogContext(): array;
}
