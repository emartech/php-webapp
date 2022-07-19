<?php

namespace Test\Helpers;

class ResourceChecker
{
    private const MAX_TRIES = 60;

    /** @var CheckerInterface[] */
    private array $checkers;

    public function __construct(array $checkers)
    {
        $this->checkers = $checkers;
    }

    public function check()
    {
        $retryCount = 0;
        $success = false;
        while ($retryCount < self::MAX_TRIES && !$success) {
            $retryCount++;
            sleep(1);

            $success = true;
            foreach ($this->checkers as $checker) {
                echo "Checking {$checker->getResourceName()} attempt {$retryCount}: ";

                $resourceConnected = $checker->check();

                if ($resourceConnected) {
                    echo "success\n";
                } else {
                    echo "failed\n";
                }

                $success &= $resourceConnected;
            }
        }
    }
}
