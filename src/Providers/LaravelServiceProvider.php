<?php

namespace LightBear\RpcClient\Providers;

class LaravelServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../../config/config.php');

        $this->publishes([$path => config_path('rpc-client.php')], 'config');

        $this->mergeConfigFrom($path, 'rpc-client');

        $this->registerServices();
    }
}
