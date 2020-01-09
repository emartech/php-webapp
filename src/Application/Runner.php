<?php

namespace Emartech\Application;

use Emartech\Http\Http;
use Exception;
use HttpStatus\Status;
use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

class Runner
{
    private const ROUTE_INFO_RESULT = 0;
    private const ROUTE_INFO_REQUEST_HANDLER = 1;
    private const ROUTE_INFO_REQUEST_VARS = 2;

    public function runWebApplication(WebApplication $app): void
    {
        $logger = $this->initializeApplication(ErrorHandler::createForWebApplication(), $app);
        $routeCollector = $this->configureWebApplication($app, $logger);
        $this->executeWebApplication($routeCollector, $logger);
    }

    public function runWorkerForever(Worker $worker): void
    {
        $logger = $this->initializeApplication(ErrorHandler::createForScript(), $worker);

        while (true) {
            $worker->run($logger);
        }
    }

    public function runWorker(Worker $worker): void
    {
        $worker->run($this->initializeApplication(ErrorHandler::createForScript(), $worker));
    }

    private function configureWebApplication(WebApplication $app, $logger): RouteCollector
    {
        $routeCollector = new RouteCollector(new Std(), new GroupCountBasedDataGenerator());
        $app->configure($routeCollector, $logger);
        return $routeCollector;
    }

    private function executeWebApplication($routeCollector, $logger): void
    {
        $this->sendResponse(new Http(), $this->handleRequest(ServerRequest::fromGlobals(), $routeCollector, $logger));
    }

    private function initializeApplication(ErrorHandler $errorHandler, Application $app): LoggerInterface
    {
        $logger = $app->createLogger(new LoggerFactory($errorHandler));
        $errorHandler->initialize($logger);
        $app->validateEnvironment();
        return $logger;
    }

    private function handleRequest(ServerRequestInterface $request, RouteCollector $routeCollector, LoggerInterface $logger)
    {
        try {
            $routeInfo = $this->dispatch($request, $routeCollector);
            switch ($routeInfo[self::ROUTE_INFO_RESULT]) {
                case Dispatcher::NOT_FOUND:
                    return $this->createNotFoundResponse();
                case Dispatcher::METHOD_NOT_ALLOWED:
                    return $this->createMethodNotAllowedResponse();
                case Dispatcher::FOUND:
                    return $this->runRequestHandler($request, $routeInfo[self::ROUTE_INFO_REQUEST_HANDLER], $routeInfo[self::ROUTE_INFO_REQUEST_VARS]);
                default:
                    throw new Exception("Invalid dispatch result: {$routeInfo[self::ROUTE_INFO_RESULT]}");
            }
        } catch (Throwable $t) {
            $logger->error($t->getMessage(), $this->createErrorLogContext($t, $request));
            return $this->createInternalServerErrorResponse();
        }
    }

    private function createErrorLogContext(Throwable $t, ServerRequestInterface $request): array
    {
        return [
            'exception' => $t,
            'url' => (string)$request->getUri(),
            'request_body' => $request->getBody()->getContents(),
        ];
    }

    private function createInternalServerErrorResponse(): ResponseInterface
    {
        return self::createErrorResponse(Status::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function createNotFoundResponse(): ResponseInterface
    {
        return self::createErrorResponse(Status::HTTP_NOT_FOUND);
    }

    private function createMethodNotAllowedResponse(): ResponseInterface
    {
        return self::createErrorResponse(Status::HTTP_METHOD_NOT_ALLOWED);
    }

    private function createErrorResponse($errorCode): ResponseInterface
    {
        return new Response($errorCode, [], Status::getReasonPhrase($errorCode));
    }

    private function runRequestHandler(ServerRequestInterface $request, callable $requestHandler, array $vars): ResponseInterface
    {
        return call_user_func($requestHandler, $request, $vars);
    }

    private function createDispatcher(RouteCollector $routeCollector): GroupCountBasedDispatcher
    {
        return new GroupCountBasedDispatcher($routeCollector->getData());
    }

    private function dispatch(ServerRequestInterface $request, RouteCollector $routeCollector): array
    {
        return $this->createDispatcher($routeCollector)->dispatch($request->getMethod(), $request->getUri()->getPath());
    }

    private function sendResponse(Http $http, ResponseInterface $response): void
    {
        if ($http->headersSent()) {
            throw new RuntimeException('Headers were already sent. The response could not be emitted!');
        }
        $http->sendStatusLine($response);
        $http->sendHeaders($response);
        $http->sendBody($response);
    }
}
