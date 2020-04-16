<?php

namespace LightBear\RpcClient\Providers;

use LightBear\RpcClient\ServiceManager;
use Illuminate\Support\ServiceProvider;

abstract class AbstractServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    abstract public function boot();

    protected function registerServices()
    {
        $config = config('rpc-client');

        $serviceManager = ServiceManager::instance($config);

        foreach ($config['services'] as $name => $service) {
            $this->app->singleton($service['id'] ?? $service['service'], function () use ($serviceManager, $name) {
                return $serviceManager->service($name);
            });
        }
    }
}
