<?php

namespace lightBear\RpcClient;

use LightBear\RpcClient\Contracts\PackerInterface;
use LightBear\RpcClient\Contracts\TransporterInterface;
use InvalidArgumentException;

class Client
{
    /**
     * @var PackerInterface
     */
    private $packer;

    /**
     * @var TransporterInterface
     */
    private $transporter;

    public function send($data)
    {
        if (!$this->packer) {
            throw new InvalidArgumentException('Packer missing.');
        }
        if (!$this->transporter) {
            throw new InvalidArgumentException('Transporter missing.');
        }
        $packer = $this->getPacker();
        $packedData = $packer->pack($data);
        $response = $this->getTransporter()->send($packedData);
        return $packer->unpack((string)$response);
    }

    public function receive()
    {
        $packer = $this->getPacker();
        $response = $this->getTransporter()->receive();
        return $packer->unpack((string)$response);
    }

    public function getPacker(): PackerInterface
    {
        return $this->packer;
    }

    public function setPacker(PackerInterface $packer): self
    {
        $this->packer = $packer;

        return $this;
    }

    public function getTransporter(): TransporterInterface
    {
        return $this->transporter;
    }

    public function setTransporter(TransporterInterface $transporter): self
    {
        $this->transporter = $transporter;

        return $this;
    }
}
