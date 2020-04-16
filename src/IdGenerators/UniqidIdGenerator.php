<?php

namespace LightBear\RpcClient\IdGenerators;

use LightBear\RpcClient\Contracts\IdGeneratorInterface;

class UniqidIdGenerator implements IdGeneratorInterface
{
    public function generate()
    {
        return uniqid();
    }
}
