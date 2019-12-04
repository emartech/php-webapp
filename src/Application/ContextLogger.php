<?php

namespace Emartech\Application;


use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;

class ContextLogger extends AbstractLogger
{
    /**
     * @var LoggerInterface
     */
    private $delegate;

    /**
     * @var array
     */
    private $context;


    public function __construct(LoggerInterface $delegate, array $context)
    {
        $this->delegate = $delegate;
        $this->context = $context;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function log($level, $message, array $context = array())
    {
        $this->delegate->log($level, $message, $this->context + $context);
    }
}
