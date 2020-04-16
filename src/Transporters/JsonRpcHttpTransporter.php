<?php

namespace LightBear\RpcClient\Transporters;

use GuzzleHttp\Client;
use LightBear\RpcClient\LoadBalancers\Node;
use LightBear\RpcClient\Contracts\LoadBalancerInterface;
use LightBear\RpcClient\Contracts\TransporterInterface;

class JsonRpcHttpTransporter implements TransporterInterface
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

    /**
     * @param string $data
     * @return string
     */
    public function send(string $data)
    {
        $node = $this->getNode();

        $uri = $node->host . ':' . $node->port;
        $schema = value(function () use ($node) {
            $schema = 'http';
            if (property_exists($node, 'schema')) {
                $schema = $node->schema;
            }
            if (!in_array($schema, ['http', 'https'])) {
                $schema = 'http';
            }
            $schema .= '://';
            return $schema;
        });
        $url = $schema . $uri;
        $response = $this->getClient()->post($url, [
            'body' => $data,
        ]);

        if ($response->getStatusCode() === 200) {
            return $response->getBody()->getContents();
        }

        $this->loadBalancer->removeNode($node);

        return '';
    }

    public function receive()
    {
        throw new \RuntimeException(__CLASS__ . ' does not support recv method.');
    }

    public function getClient(): Client
    {
        if (!$this->clientFactory) {
            $this->clientFactory = new Client([
                'timeout' => $this->connectTimeout + $this->receiveTimeout,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'http_errors' => false,
            ]);
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
