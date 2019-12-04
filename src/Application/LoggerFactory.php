<?php

namespace Emartech\Application;

use Exception;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

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

    private function createFormatter(): FormatterInterface
    {
        return new class extends JsonFormatter
        {
            public function format(array $record): string
            {
                unset($record['datetime']);
                if (isset($record['context']['exception'])) {
                    $exception = $record['context']['exception'];
                    if ($exception instanceof Throwable) {
                        $record['context']['trace'] = $exception->getTraceAsString();
                    }
                }
                return parent::format($record);
            }
        };
    }

    /**
     * @throws Exception
     */
    private function createHandler(string $logLevel): HandlerInterface
    {
        $streamHandler = new StreamHandler('php://stderr', Logger::getLevels()[$logLevel]);
        $streamHandler->setFormatter($this->createFormatter());
        return $streamHandler;
    }
}
