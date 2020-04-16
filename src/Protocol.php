<?php

namespace LightBear\RpcClient;

use LightBear\RpcClient\Contracts\DataFormatterInterface;
use LightBear\RpcClient\Contracts\PackerInterface;
use LightBear\RpcClient\Contracts\PathGeneratorInterface;
use LightBear\RpcClient\Contracts\TransporterInterface;

class Protocol
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $options;

    public function __construct($name, array $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPacker(): PackerInterface
    {
        $packer = $this->getProtocolManager()->getPacker($this->name);
        if (!class_exists($packer)) {
            throw new \InvalidArgumentException("Packer {$packer} for {$this->name} does not exist");
        }

        return new $packer($this->options);
    }

    public function getTransporter(): TransporterInterface
    {
        $transporter = $this->getProtocolManager()->getTransporter($this->name);

        if (!class_exists($transporter)) {
            throw new \InvalidArgumentException("Transporter {$transporter} for {$this->name} does not exist");
        }

        return new $transporter($this->options);
    }

    public function getPathGenerator(): PathGeneratorInterface
    {
        $pathGenerator = $this->getProtocolManager()->getPathGenerator($this->name);

        if (!class_exists($pathGenerator)) {
            throw new \InvalidArgumentException("PathGenerator {$pathGenerator} for {$this->name} does not exist");
        }
        return new $pathGenerator();
    }

    public function getDataFormatter(): DataFormatterInterface
    {
        $dataFormatter = $this->getProtocolManager()->getDataFormatter($this->name);
        if (!class_exists($dataFormatter)) {
            throw new \InvalidArgumentException("DataFormatter {$dataFormatter} for {$this->name} does not exist");
        }

        return new $dataFormatter();
    }

    /**
     * @return ProtocolManager
     */
    protected function getProtocolManager()
    {
        return ServiceManager::instance()->getProtocolManager();
    }
}
