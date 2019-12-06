<?php

namespace Emartech\Application;

use Exception;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class LoggerFactory
{
    /**
     * @var ErrorHandler
     */
    private $errorHandler;


    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function createLogger(string $logLevel): LoggerInterface
    {
        try {
            return new Logger('app', [$this->createHandler($logLevel)]);
        } catch (Exception $e) {
            $this->errorHandler->handleException(new NullLogger(), $e);
            return new NullLogger();
        }
    }

    private function createFormatter(): JsonFormatter
    {
        return new class extends JsonFormatter
        {
            public function format(array $record): string
            {
                unset($record['datetime']);
                return parent::format($record);
            }
        };
    }

    private function createHandler(string $logLevel): HandlerInterface
    {
        $formatter = $this->createFormatter();
        $formatter->includeStacktraces();

        $streamHandler = new StreamHandler('php://stderr', Logger::getLevels()[$logLevel]);
        $streamHandler->setFormatter($formatter);

        return $streamHandler;
    }
}
