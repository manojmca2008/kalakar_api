<?php
namespace Emails;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'email-sending' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/emails/mytest[/:id]',
                    'constraints' => [
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\MytestController::class,
                    ],
                ],
            ],
        ],
    ],
];
