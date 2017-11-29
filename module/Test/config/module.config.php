<?php
namespace Test;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'email-sending' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/test/form[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-  9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TestController::class,
                    ],
                ],
            ],
        ],
    ],
];
