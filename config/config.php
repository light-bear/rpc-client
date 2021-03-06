<?php
return [
    'services' => [
        'user' => [
            // name 需与服务提供者的 name 属性相同
            'name' => 'UserService',
            // 服务接口名，可选，默认值等于 name 配置的值，如果 name 直接定义为接口类则可忽略此行配置，如 name 为字符串则需要配置 service 对应到接口类
            'service' => \App\RpcServices\UserServiceInterface::class,
            // 对应容器对象 ID，可选，默认值等于 service 配置的值，用来定义依赖注入的 key
            'id' => \App\RpcServices\UserServiceInterface::class,
            // 服务提供者的服务协议，可选，默认值为 jsonrpc-http
            // 可选 jsonrpc-http jsonrpc jsonrpc-tcp-length-check
            'protocol' => 'http-json-rpc',
            // 负载均衡算法，可选，默认值为 random
            'load_balancer' => 'random',
            // 这个消费者要从哪个服务中心获取节点信息，如不配置则不会从服务中心获取节点信息
//            'registry' => [
//                'protocol' => 'consul',
//                'address' => 'http://127.0.0.1:8500',
//            ],
            // 如果没有指定上面的 registry 配置，即为直接对指定的节点进行消费，通过下面的 nodes 参数来配置服务提供者的节点信息
            'nodes' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 9504
                ]
            ],
        ]
    ],
    'protocols' => [
        'http-json-rpc' => [
            'packer' => \LightBear\RpcClient\Packers\JsonPacker::class,
            'transporter' => \LightBear\RpcClient\Transporters\JsonRpcHttpTransporter::class,
            'path-generator' => \LightBear\RpcClient\PathGenerators\PathGenerator::class,
            'data-formatter' => \LightBear\RpcClient\DataFormatters\JsonDataFormatter::class,
        ],
        'json-rpc' => [
            'packer' => \LightBear\RpcClient\Packers\JsonPacker::class,
            'transporter' => \LightBear\RpcClient\Transporters\JsonRpcTcpTransporter::class,
            'path-generator' => \LightBear\RpcClient\PathGenerators\PathGenerator::class,
            'data-formatter' => \LightBear\RpcClient\DataFormatters\JsonDataFormatter::class,
            'options' => [
                'package_eof' => "\r\n"
            ]
        ],
    ],
    'id-generator' => \LightBear\RpcClient\IdGenerators\RequestIdGenerator::class,
];