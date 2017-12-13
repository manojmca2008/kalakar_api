<?php
namespace Invoice;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'resend-otp' => [
                'type'    => Segment::class,
                'options' => [
                        'route'    => '/api/invoice/resend-otp[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SmsController::class,
                    ],
                ],
            ],
            'resend-email' => [
                'type'    => Segment::class,
                'options' => [
                        'route'    => '/api/invoice/resend-email[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\EmailController::class,
                    ],
                ],
            ],
            'get-notification' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/invoice/notifications[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\NotificationController::class,
                    ],
                ],
            ],
            'invoice-user-signup' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/invoice/signup',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SignupController::class,
                    ],
                ],
            ],
            'invoice-user-signin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/invoice/signin[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SigninController::class,
                    ],
                ],
            ],
            'user-accounts' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/invoice/user-accounts[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserAccountsController::class,
                    ],
                ],
            ],
            'reset-password' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/invoice/reset-password[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ResetPasswordController::class,
                    ],
                ],
            ],
            'forget-password' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/invoice/forget-password[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ForgetPasswordController::class,
                    ],
                ],
            ],
            'otp-varify' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/invoice/validate-otp[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SignupController::class,
                    ],
                ],
            ],
        ],
    ],
];
