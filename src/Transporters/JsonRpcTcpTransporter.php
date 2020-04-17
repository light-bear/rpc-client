<?php

namespace LightBear\RpcClient\Transporters;

use LightBear\RpcClient\Connectors\TcpConnector;
use LightBear\RpcClient\LoadBalancers\Node;
use LightBear\RpcClient\Contracts\LoadBalancerInterface;
use LightBear\RpcClient\Contracts\TransporterInterface;

class JsonRpcTcpTransporter implements TransporterInterface
{
    /**
     * @var null|LoadBalancerInterface
     */
    private $loadBalancer;

    /**
     * If $loadBalancer is null, will select a node in $nodes to request,
     * otherwise, use the nodes in $loadBalancer.
     *
     * @var Node[]
     */
    private $nodes = [];

    /**
     * @var float
     */
    private $connectTimeout = 5;

    /**
     * @var float
     */
    private $receiveTimeout = 5;

    /**
     * @var
     */
    private $clientFactory;

    private $packageEof = "\r\n";

    public function __construct($options)
    {
        $this->packageEof = $options['package_eof'] ?? "\r\n";
    }

    /**
     * @param string $data
     * @return string
     */
    public function send(string $data)
    {
        $node = $this->getNode();

        try {
            $client = $this->getClient($node)->connect();

            $client->send($data . $this->packageEof);

            $data = $client->receive($this->packageEof);
            return $data;
        } catch (\Exception $e) {
            // 忽略异常
        }

        $this->loadBalancer->removeNode($node);

        return '';
    }

    public function receive()
    {
        throw new \RuntimeException(__CLASS__ . ' does not support recv method.');
    }

    public function getClient(Node $node): TcpConnector
    {
        if (!$this->clientFactory) {
            $this->clientFactory = new TcpConnector($node->host, $node->port, $this->connectTimeout);
        }
        return $this->clientFactory;
    }

    public function getLoadBalancer(): ?LoadBalancerInterface
    {
        return $this->loadBalancer;
    }

    public function setLoadBalancer(LoadBalancerInterface $loadBalancer): TransporterInterface
    {
        $this->loadBalancer = $loadBalancer;

        return $this;
    }

    /**
     * @param Node[] $nodes
     * @return JsonRpcHttpTransporter
     */
    public function setNodes(array $nodes): self
    {
        $this->nodes = $nodes;
        return $this;
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * If the load balancer is exists, then the node will select by the load balancer,
     * otherwise will get a random node.
     */
    private function getNode(): Node
    {
        if ($this->loadBalancer instanceof LoadBalancerInterface) {
            return $this->loadBalancer->select();
        }
        return $this->nodes[array_rand($this->nodes)];
    }
}
