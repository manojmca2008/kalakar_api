<?php
namespace Invoice;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'sms-send' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/invoice/sms-send[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SmsController::class,
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
        ],
    ],
];
