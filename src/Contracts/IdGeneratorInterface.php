<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 18:24
 */

namespace LightBear\RpcClient\Contracts;

interface IdGeneratorInterface
{
    public function generate();
}