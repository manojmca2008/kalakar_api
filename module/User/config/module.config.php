<?php
namespace User;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'test' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/test[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TestController::class,
                    ],
                ],
            ],
            'user' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/user[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                    ],
                ],
            ],
            'user-detail' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/users',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                    ],
                ],
            ],
            'user-login' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/login',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                    ],
                ],
            ],
            'user-register' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/register',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                    ],
                ],
            ],
            'user-logout' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/logout',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LogoutController::class,
                    ],
                ],
            ],
        ],
    ],
];