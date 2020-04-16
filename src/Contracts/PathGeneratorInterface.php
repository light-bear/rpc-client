<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 11:25
 */

namespace LightBear\RpcClient\Contracts;


interface PathGeneratorInterface
{
    public function generate(string $service, string $method): string;
}