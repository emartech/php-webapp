<?php

namespace Emartech\Application;

use Exception;

class Environment
{
    public function checkVariables(array $variables): void
    {
        $missing = [];

        foreach ($variables as $variable) {
            if ($this->getRawValue($variable) === false) {
                $missing[] = $variable;
            }
        }

        if ($missing) {
            throw new Exception('Environment variables missing: [ '.implode(', ', $missing).' ]');
        }
    }

    public function getRawValue($variableName)
    {
        return getenv($variableName);
    }

    public function getJSON($variableName): array
    {
        $result = json_decode($this->getRawValue($variableName), true, 512, JSON_THROW_ON_ERROR);

        if($result === false) {
            throw new Exception('Environemt variable value is not a valid JSON: '.$variableName);
        }

        return $result;
    }
}
