<?php

namespace Emartech\Request;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

class Validator
{
    public function validatePostData(ServerRequestInterface $request, array $requiredPostParams): array
    {
        $postData = json_decode($request->getBody()->getContents(), true);

        if (!$postData) {
            $postData = [];
        }

        foreach ($requiredPostParams as $requiredPostParam) {
            if (!isset($postData[$requiredPostParam])) {
                throw new InvalidArgumentException($requiredPostParam." postData is missing");
            }
        }

        return $postData;
    }
}
