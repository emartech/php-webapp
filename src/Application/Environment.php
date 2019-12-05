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

    public function getRawValue($variableName): string
    {
        return getenv($variableName);
    }

    public function getJSON($variableName): array
    {
        $result = json_decode($this->getRawValue($variableName), true);

        if($result === false) {
            throw new Exception('Environemt variable value is not a valid JSON: '.$variableName);
        }

        return $result;
    }
}
