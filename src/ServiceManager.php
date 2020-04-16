<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 14:54
 */

namespace LightBear\RpcClient;

use LightBear\RpcClient\Contracts\IdGeneratorInterface;
use LightBear\RpcClient\Exceptions\ConfigException;
use LightBear\RpcClient\LoadBalancers\LoadBalancerManager;

class ServiceManager
{
    /**
     * @var static
     */
    protected static $instance;

    protected $config;

    protected $services = [];

    protected $protocols = [];

    /**
     * @var LoadBalancerManager
     */
    protected $loadBalancerManager;

    /**
     * @var ProtocolManager
     */
    protected $protocolManager;

    /**
     * @var IdGeneratorInterface
     */
    protected $idGenerator;

    protected function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param null $config
     * @return static
     */
    public static function instance($config = null)
    {
        if (!(static::$instance instanceof static)) {
            static::$instance = new static($config);
        }

        return static::$instance;
    }

    public function call($serviceName, $method, Array $args = [], ?string $id = null)
    {
        return $this->service($serviceName)->call($method, $args, $id);
    }

    /**
     * @param $name
     * @return AbstractServiceClient
     */
    public function service($name)
    {
        if (!isset($this->services[$name]) || !($this->services[$name] instanceof AbstractServiceClient)) {
            $config = $this->config['services'][$name] ?? null;

            if (!is_array($config)) {
                throw new ConfigException("[services.{$name}] config is not array!");
            }

            $serviceClass = new \ReflectionClass($config['service']);

            if ($serviceClass->isInstantiable()) {
                $service = $serviceClass->newInstance($name, $config);
            } else {
                $service = new ServiceClient($name, $config);
            }

            $this->services[$name] = $service;
        }

        return $this->services[$name];
    }


    /**
     * @return ProtocolManager
     */
    public function getProtocolManager()
    {
        if (!($this->protocolManager instanceof ProtocolManager)) {
            $config = $this->config['protocols'] ?? [];

            if (!is_array($config)) {
                throw new ConfigException("protocols config is not array!");
            }

            return $this->protocolManager = new ProtocolManager($config);
        }

        return $this->protocolManager;
    }

    /**
     * @return LoadBalancerManager
     */
    public function loadBalancerManager()
    {
        if (!($this->loadBalancerManager instanceof LoadBalancerManager)) {
            $this->loadBalancerManager = new LoadBalancerManager();
        }
        return $this->loadBalancerManager;
    }

    /**
     * @return IdGeneratorInterface
     */
    public function getIdGenerator()
    {
        if (!($this->idGenerator instanceof IdGeneratorInterface)) {
            $idGeneratorClass = $this->config['id-generator'] ?? '';
            if (!class_exists($idGeneratorClass)) {
                throw new ConfigException("Id generator class is not exists!");
            }
            $this->idGenerator = new $idGeneratorClass;
        }

        return $this->idGenerator;
    }
}