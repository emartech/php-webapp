<?php

namespace Emartech\Application;

use Psr\Log\LoggerInterface;

interface Worker extends Application
{
    public function run(LoggerInterface $logger): void;
}
