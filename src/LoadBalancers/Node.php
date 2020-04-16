<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 15:59
 */

namespace LightBear\RpcClient\LoadBalancers;


class Node
{
    /**
     * @var int
     */
    public $weight;

    /**
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port;

    public function __construct(string $host = '127.0.0.1', int $port, int $weight = 0)
    {
        $this->host = $host;
        $this->port = $port;
        $this->weight = $weight;
    }
}