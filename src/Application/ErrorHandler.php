<?php

namespace Emartech\Application;

use Exception;
use Psr\Log\LoggerInterface;
use Throwable;

class ErrorHandler
{
    private $emergencyExit;


    public static function createForWebApplication(): self
    {
        return new self(function () {
            http_response_code(500);
            exit();
        });
    }

    public static function createForScript(int $emergencyExitStatusCode = 1): self
    {
        return new self(function () use ($emergencyExitStatusCode) {
            exit($emergencyExitStatusCode);
        });
    }

    public function __construct(callable $emergencyExit)
    {
        $this->emergencyExit = $emergencyExit;
    }

    public function initialize(LoggerInterface $logger)
    {
        set_error_handler($this->createErrorHandler());
        set_exception_handler($this->createExceptionHandler($logger));
    }

    private function createErrorHandler(): callable
    {
        return function ($errno, $errstr, $errfile, $errline) {
            if (!($errno & (E_DEPRECATED | E_USER_DEPRECATED))) {
                throw new Exception($errstr . ' in ' . $errfile . ' on line ' . $errline, $errno);
            }
        };
    }

    private function createExceptionHandler(LoggerInterface $logger): callable
    {
        return function (Throwable $t) use ($logger) {
            $this->handleException($logger, $t);
        };
    }

    public function handleException(LoggerInterface $logger, Throwable $t): void
    {
        $logger->error($t->getMessage(), ['exception' => $t]);
        call_user_func($this->emergencyExit);
    }
}
