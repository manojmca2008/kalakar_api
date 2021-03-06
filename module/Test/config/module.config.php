<?php
namespace Test;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'form-test' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/test/form[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TestController::class,
                    ],
                ],
            ],
            'mongo-test' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/test/mongo[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TestingController::class,
                    ],
                ],
            ],
        ],
    ],
];
