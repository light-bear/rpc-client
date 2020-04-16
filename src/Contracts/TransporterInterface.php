<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 13:36
 */

namespace LightBear\RpcClient\Contracts;


interface TransporterInterface
{
    /**
     * @param string $data
     *
     * @return bool
     */
    public function send(string $data);

    /**
     * @return string|bool
     */
    public function receive();

    /**
     * @return LoadBalancerInterface|null
     */
    public function getLoadBalancer(): ?LoadBalancerInterface;

    /**
     * @param LoadBalancerInterface $loadBalancer
     * @return TransporterInterface
     */
    public function setLoadBalancer(LoadBalancerInterface $loadBalancer): TransporterInterface;
}