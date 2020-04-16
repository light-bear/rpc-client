<?php

namespace LightBear\RpcClient\LoadBalancers;

use LightBear\RpcClient\Contracts\LoadBalancerInterface;
use Swoole\Timer;

abstract class AbstractLoadBalancer implements LoadBalancerInterface
{
    /**
     * @var Node[]
     */
    protected $nodes;

    /**
     * @param Node[] $nodes
     */
    public function __construct(array $nodes = [])
    {
        $this->nodes = $nodes;
    }

    /**
     * @param Node[] $nodes
     * @return $this
     */
    public function setNodes(array $nodes)
    {
        $this->nodes = $nodes;
        return $this;
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * Remove a node from the node list.
     */
    public function removeNode(Node $node): bool
    {
        foreach ($this->nodes as $key => $activeNode) {
            if ($activeNode === $node) {
                unset($this->nodes[$key]);
                return true;
            }
        }
        return false;
    }

    public function refresh(callable $callback, int $tickMs = 5000)
    {
        Timer::tick($tickMs, function () use ($callback) {
            $nodes = call($callback);
            is_array($nodes) && $this->setNodes($nodes);
        });
    }
}
