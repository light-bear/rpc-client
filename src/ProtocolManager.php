<?php

namespace LightBear\RpcClient;

use LightBear\RpcClient\Exceptions\ConfigException;

class ProtocolManager
{

    private $config;


    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getPacker(string $name): string
    {
        return $this->getTarget($name, 'packer');
    }

    public function getTransporter(string $name): string
    {
        return $this->getTarget($name, 'transporter');
    }

    public function getPathGenerator(string $name): string
    {
        return $this->getTarget($name, 'path-generator');
    }

    public function getDataFormatter(string $name): string
    {
        return $this->getTarget($name, 'data-formatter');
    }

    private function getTarget(string $name, string $target)
    {
        $config = $this->config($name);

        if (!is_string($config[$target])) {
            throw new ConfigException(sprintf('protocols.%s.%s is not exists.', $name, $target));
        }

        return $config[$target];
    }

    protected function config(string $name): array
    {
        if (empty($this->config[$name]) || !is_array($this->config[$name])) {
            throw new ConfigException(sprintf('protocol[%s] is not exists!', $name));
        }

        return $this->config[$name];
    }

}
