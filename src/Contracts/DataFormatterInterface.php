<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 11:32
 */

namespace LightBear\RpcClient\Contracts;


interface DataFormatterInterface
{
    /**
     * @param array $data [$path, $params, $id]
     * @return array
     */
    public function formatRequest($data);
}
