<?php

namespace LightBear\RpcClient\LoadBalancers;

class Random extends AbstractLoadBalancer
{
    /**
     * Select an item via the load balancer.
     */
    public function select(array ...$parameters): Node
    {
        if (empty($this->nodes)) {
            throw new \RuntimeException('Cannot select any node from load balancer.');
        }
        $key = array_rand($this->nodes);
        return $this->nodes[$key];
    }
}
