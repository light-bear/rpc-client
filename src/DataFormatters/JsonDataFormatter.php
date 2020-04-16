<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 11:33
 */

namespace LightBear\RpcClient\DataFormatters;


use LightBear\RpcClient\Contracts\DataFormatterInterface;

class JsonDataFormatter implements DataFormatterInterface
{
    const JSON_RPC_VERSION = 2.0;

    public function formatRequest($data)
    {
        list($path, $params, $id) = $data;

        return [
            'jsonrpc' => static::JSON_RPC_VERSION,
            'method' => $path,
            'params' => $params,
            'id' => $id
        ];
    }
}