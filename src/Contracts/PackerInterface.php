<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 11:07
 */

namespace LightBear\RpcClient\Contracts;

interface PackerInterface
{
    public function pack($data): string;

    public function unpack(string $data);
}