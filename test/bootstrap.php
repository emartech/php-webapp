<?php

require_once __DIR__.'/../vendor/autoload.php';

use Test\Helpers\ResourceChecker;

$resourceChecker = new ResourceChecker([
    // add resource checkers implementing CheckerInterface here
]);

$resourceChecker->check();
