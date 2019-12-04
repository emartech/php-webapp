<?php

namespace Emartech\Application;

use FastRoute\RouteCollector;
use Psr\Log\LoggerInterface;

interface WebApplication extends Application
{
    public function configure(RouteCollector $routeCollector, LoggerInterface $logger): void;
}
