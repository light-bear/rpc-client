<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 10:15
 */

namespace LightBear\RpcClient;

class ServiceClient extends AbstractServiceClient
{
    public function __call(string $method, $params)
    {
        return $this->call($method, $params);
    }
}