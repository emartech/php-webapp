<?php

namespace Emartech\Application;

use Exception;
use Psr\Http\Message\ServerRequestInterface;

interface RequestHandlerErrorLogger
{
    public function logError(ServerRequestInterface $request, array $vars, Exception $ex): void;
}
