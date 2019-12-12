<?php

require_once __DIR__.'/../vendor/autoload.php';

use Emartech\TestHelper\SpyLogger;
use Test\Helpers\ResourceChecker;

$logger = new SpyLogger();

$resourceChecker = new ResourceChecker([
    // add resource checkers implementing CheckerInterface here
]);

$resourceChecker->check();
