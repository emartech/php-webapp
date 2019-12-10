<?php

namespace Emartech\Application;

use Emartech\AuthHandler\AuthHandlerInterface;
use Emartech\AuthHandler\Exception as AuthHandlerException;
use Exception;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestHandler
{
    private $handler;
    private $authHandler;
    private $errorLogger;

    public function __construct(callable $handler, AuthHandlerInterface $authHandler, RequestHandlerErrorLogger $logger)
    {
        $this->handler = $handler;
        $this->authHandler = $authHandler;
        $this->errorLogger = $logger;
    }

    public function __invoke(ServerRequestInterface $request, array $vars): ResponseInterface
    {
        try {
            $this->authHandler->authenticate();
            return call_user_func($this->handler, $request, $vars);
        } catch (AuthHandlerException $ex) {
            $this->errorLogger->logError($request, $vars, $ex);
            return new Response(403, [], $ex->getCode() . ' ' . $ex->getMessage());
        } catch (InvalidArgumentException $ex) {
            $this->errorLogger->logError($request, $vars, $ex);
            return new Response(400, [], $ex->getMessage());
        } catch (Exception $ex) {
            $this->errorLogger->logError($request, $vars, $ex);
            return new Response(500, [], $ex->getCode() . ' ' . $ex->getMessage());
        }
    }
}
