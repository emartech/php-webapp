<?php

namespace Emartech\StopWatch;


use Psr\Log\LoggerInterface;

class StopWatch
{
    const LOG_KEY = 'measurements';

    private $measurements = [];
    private $logger;
    private $logKey;

    public function __construct(LoggerInterface $logger, string $logKey)
    {
        $this->logger = $logger;
        $this->logKey = $logKey;
    }

    public static function create(LoggerInterface $logger)
    {
        return new self($logger, self::LOG_KEY);
    }

    public function measure(string $measurementId, callable $callable)
    {
        $start = microtime(true);
        $result = call_user_func($callable);
        $this->measurements[$measurementId] = (microtime(true) - $start) * 1000;
        return $result;
    }

    public function logMeasurements(string $message, array $additionalData = []): void
    {
        $this->logger->info($message, [$this->logKey => $this->measurements] + $additionalData);
    }
}
