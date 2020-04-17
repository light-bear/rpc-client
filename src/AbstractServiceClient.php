<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 9:42
 */

namespace LightBear\RpcClient;

use InvalidArgumentException;
use LightBear\RpcClient\Contracts\IdGeneratorInterface;
use LightBear\RpcClient\Contracts\RpcServiceInterface;
use LightBear\RpcClient\Exceptions\RequestException;
use LightBear\RpcClient\Exceptions\RpcClientException;
use LightBear\RpcClient\LoadBalancers\Node;

abstract class AbstractServiceClient implements RpcServiceInterface
{
    /**
     * @var string
     */
    protected $serviceName = '';

    /**
     * @var string
     */
    protected $protocol = 'json-rpc';

    /**
     * @var string
     */
    protected $loadBalancer = 'random';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var
     */
    protected $idGenerator;

    /**
     * @var
     */
    protected $pathGenerator;

    /**
     * @var
     */
    protected $dataFormatter;

    protected $registry;

    protected $nodes = [];

    public function __construct($name, $config = [])
    {
        $this->serviceName = $name;
        $this->protocol = $config['protocol'] ?? $this->protocol;
        $this->loadBalancer = $config['load_balancer'] ?? $this->loadBalancer;
        $this->nodes = $config['nodes'] ?? [];

        $protocol = new Protocol($this->protocol, $config['options'] ?? []);

        $transporter = $protocol->getTransporter()->setLoadBalancer($this->createLoadBalancer());

        $this->client = (new Client())->setPacker($protocol->getPacker())->setTransporter($transporter);

        $this->pathGenerator = $protocol->getPathGenerator();

        $this->dataFormatter = $protocol->getDataFormatter();

        $this->idGenerator = $this->getIdGenerator();
    }

    public function call($method, $args, ?string $id = null)
    {
        if (!$id && $this->idGenerator instanceof IdGeneratorInterface) {
            $id = $this->idGenerator->generate();
        }

        $data = $this->generateData($method, $args, $id);

        $response = $this->client->send($data);

        if (is_array($response)) {
            $response = $this->checkRequestIdAndTryAgain($response, $id);

            if (array_key_exists('result', $response)) {
                return $response['result'];
            }

            if (array_key_exists('error', $response)) {
                $message = $response['error']['message'] ?? 'Invalid response.';
                $code = $response['error']['code'] ?? -32600;
                throw new RpcClientException($message, $code);
            }
        }

        throw new RpcClientException('Invalid response.', -32600);
    }


    /**
     * Create nodes the first time.
     *
     * @return array [array, callable]
     */
    protected function createNodes(): array
    {
        $refreshCallback = null;

        $nodes = [];
        if ($this->nodes) {
            foreach ($this->nodes ?? [] as $node) {
                if (!isset($node['host'], $node['port'])) {
                    continue;
                }

                if (!is_int($node['port'])) {
                    $msg = sprintf('Invalid node config [%s], the port option has to a integer.', implode(':', $node));

                    throw new InvalidArgumentException($msg);
                }

                $nodes[] = new Node($node['host'], $node['port']);
            }

            return [$nodes, $refreshCallback];
        }

        throw new InvalidArgumentException('Config of registry or nodes missing.');
    }


    protected function createLoadBalancer(callable $refresh = null)
    {
        $loadBalancer = ServiceManager::instance()->loadBalancerManager()
            ->getInstance($this->serviceName, $this->loadBalancer)
            ->setNodes(...$this->createNodes());

        $refresh && $loadBalancer->refresh($refresh);

        return $loadBalancer;
    }

    protected function generateData(string $methodName, array $params, ?string $id)
    {
        return $this->dataFormatter->formatRequest([$this->generateRpcPath($methodName), $params, $id]);
    }

    protected function generateRpcPath($methodName): string
    {
        if (!$this->serviceName) {
            throw new InvalidArgumentException('Parameter $serviceName missing.');
        }

        $this->serviceName = 'UserService';
        return $this->pathGenerator->generate($this->serviceName, $methodName);
    }

    protected function getIdGenerator(): IdGeneratorInterface
    {
        return ServiceManager::instance()->getIdGenerator();
    }

    protected function checkRequestIdAndTryAgain(array $response, $id, int $again = 1): array
    {
        if (is_null($id)) {
            // If the request id is null then do not check.
            return $response;
        }

        if (isset($response['id']) && $response['id'] === $id) {
            return $response;
        }

        if ($again <= 0) {
            throw new RequestException(sprintf(
                'Invalid response. Request id[%s] is not equal to response id[%s].',
                $id,
                $response['id'] ?? null
            ));
        }

        $response = $this->client->receive();
        --$again;

        return $this->checkRequestIdAndTryAgain($response, $id, $again);
    }
}