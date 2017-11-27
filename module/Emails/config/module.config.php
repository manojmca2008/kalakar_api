<?php
namespace Emails;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'email-sending' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/emails/emailtest[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\EmailController::class,
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'email-layout/default' => BASE_DIR . '/module/Emails/src/Mails/layouts/default.phtml',
            'email-template/user-registration' => BASE_DIR . '/module/Emails/src/Mails/templates/01_Welcome-Friend.phtml',
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
