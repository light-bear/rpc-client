<?php

namespace LightBear\RpcClient\Providers;

use LightBear\RpcClient\ServiceManager;

class LumenServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->configure('rpc-client');

        $path = realpath(__DIR__ . '/../../config/config.php');

        $this->mergeConfigFrom($path, 'rpc-client');

        $this->registerServices();
    }
}
