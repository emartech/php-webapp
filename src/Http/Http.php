<?php

namespace Emartech\Http;

use Psr\Http\Message\ResponseInterface;

class Http
{
    public function sendStatusLine(ResponseInterface $response): void
    {
        $this->sendHeaderLine(sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()), true);
    }

    public function sendHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            $this->sendHeaderLine(sprintf('%s: %s', $name, $response->getHeaderLine($name)), false);
        }
    }

    public function sendBody(ResponseInterface $response)
    {
        echo (string)$response->getBody();
    }

    public function headersSent(): bool
    {
        return headers_sent();
    }

    private function sendHeaderLine(string $headerLine, bool $replace): void
    {
        header($headerLine, $replace);
    }
}
