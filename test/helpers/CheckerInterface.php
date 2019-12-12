<?php

namespace Test\Helpers;

interface CheckerInterface
{
    public function check(): bool;
    public function getResourceName(): string;
}
