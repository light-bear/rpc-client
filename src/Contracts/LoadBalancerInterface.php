<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 15:57
 */

namespace LightBear\RpcClient\Contracts;

use LightBear\RpcClient\LoadBalancers\Node;

interface LoadBalancerInterface
{
    /**
     * Select an item via the load balancer.
     */
    public function select(array ...$parameters): Node;

    /**
     * @param Node[] $nodes
     * @return $this
     */
    public function setNodes(array $nodes);

    /**
     * @return Node[] $nodes
     */
    public function getNodes(): array;

    /**
     * Remove a node from the node list.
     */
    public function removeNode(Node $node): bool;

    public function refresh(callable $callback, int $tickMs = 5000);
}