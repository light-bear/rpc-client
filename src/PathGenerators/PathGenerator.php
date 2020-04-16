<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/14
 * Time: 11:27
 */

namespace LightBear\RpcClient\PathGenerators;

use Illuminate\Support\Str;
use LightBear\RpcClient\Contracts\PathGeneratorInterface;

class PathGenerator implements PathGeneratorInterface
{

    public function generate(string $service, string $method): string
    {
        $handledNamespace = explode('\\', $service);
        $handledNamespace = Str::replaceArray('\\', ['/'], end($handledNamespace));
        $handledNamespace = Str::replaceLast('Service', '', $handledNamespace);
        $path = Str::snake($handledNamespace);

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        return $path . '/' . $method;
    }
}