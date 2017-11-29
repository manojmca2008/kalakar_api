<?php

return [
    'constants' => [
        'protocol' => 'http',
        'smtp' => [
            'name' => 'Kalakar Support',
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'connection_class' => 'login',
            'username' => 'garg19feb@gmail.com',
            'password' => 'manoj_mca',
            'ssl' => 'tls'
        ],
        'pubnub' => [
            'subscribe_key' => 'sub-c-a097327c-cb5f-11e7-9319-62175e58f2c1',
            'publish_key' => 'pub-c-251a11f2-06a3-4076-b79e-cd049d519101',
        ],
        'redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'channel' => 'default',
            'enabled' => true
        ],
        'mongo' => [
                'host' => 'mongodb://127.0.0.1:27017',
                'database' => 'Kalakar',
                'enabled' => true,
                'collection' => 'logs',
        ],
    ]
];
