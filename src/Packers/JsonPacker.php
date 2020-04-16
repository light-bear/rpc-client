<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 11:09
 */

namespace LightBear\RpcClient\Packers;

use LightBear\RpcClient\Contracts\PackerInterface;

class JsonPacker implements PackerInterface
{
    public function pack($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function unpack(string $data)
    {
        return json_decode($data, true);
    }
}