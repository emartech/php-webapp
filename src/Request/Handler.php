<?php

namespace Emartech\Request;

use Emartech\AuthHandler\AuthHandlerInterface;
use Emartech\AuthHandler\Exception as AuthHandlerException;
use Exception;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class Handler
{
    const APPLICATION_ERROR = 'Application error';

    private $handler;
    private $authHandler;
    private $logger;

    public function __construct(callable $handler, AuthHandlerInterface $authHandler, LoggerInterface $logger)
    {
        $this->handler = $handler;
        $this->authHandler = $authHandler;
        $this->logger = $logger;
    }

    public function __invoke(ServerRequestInterface $request, array $vars): ResponseInterface
    {
        try {
            $this->authHandler->authenticate();
            return call_user_func($this->handler, $request, $vars);
        } catch (AuthHandlerException $ex) {
            $this->logError($request, $vars, $ex);
            return new Response(403, [], $ex->getCode() . ' ' . $ex->getMessage());
        } catch (InvalidArgumentException $ex) {
            $this->logError($request, $vars, $ex);
            return new Response(400, [], $ex->getMessage());
        } catch (Exception $ex) {
            $this->logError($request, $vars, $ex);
            return new Response(500, [], $ex->getCode() . ' ' . $ex->getMessage());
        }
    }

    private function logError(ServerRequestInterface $request, array $vars, Exception $ex): void
    {
        $postData = (new Validator())->validatePostData($request, []);
        $this->logger->error(
            self::APPLICATION_ERROR,
            [
                'customerId' => (int)($vars['customerId'] ?? 0),
                'programId' => (int)($vars['programId'] ?? 0),
                'entryId' => $postData['entryId'] ?? '',
                'exception' => $ex,
                'uri' => (string)$request->getUri(),
                'requestBody' => $request->getBody()->getContents(),
                'vars' => $vars,
                'postData' => $postData,
            ]
        );
    }
}
