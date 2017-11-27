<?php
namespace Cms;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'cms-test' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/cms/test[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TestController::class,
                    ],
                ],
            ],
            'cms-user-detail' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/cms/user[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                    ],
                ],
            ],
            'cms-users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/cms/users',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                    ],
                ],
            ],
            'cms-user-login' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/cms/login',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                    ],
                ],
            ],
            'cms-user-register' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/cms/register',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                    ],
                ],
            ],
            'cms-user-logout' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/cms/logout',
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