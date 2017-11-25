<?php
namespace Email;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'test' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/email/test[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\TestController::class,
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'email-layout/default' => __DIR__ . '/../Mails/User/layouts/default.phtml',
            'email-template/user-registration' => '/home/manoj/workspace/kalakar_api/public/templates/01_Welcome-Friend.phtml',
            'email-template/user-reservation-confirmation' => __DIR__ . '/../Mails/User/templates/reservation-placed.phtml',
            'email-template/munchado-customer-reservation-confirmation' => __DIR__ . '/../Mails/User/templates/22_Reservation-From-a-MunchAdo-Customer.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../Mails'
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];