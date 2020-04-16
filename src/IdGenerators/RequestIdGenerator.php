<?php

namespace LightBear\RpcClient\IdGenerators;

use LightBear\RpcClient\Contracts\IdGeneratorInterface;

class RequestIdGenerator implements IdGeneratorInterface
{
    public function generate(): string
    {
        $us = strstr(microtime(), ' ', true);
        return strval($us * 1000 * 1000) . rand(100, 999);
    }
}
