<?php

use Leeflets\Controller\HomeController;
use Leeflets\Controller\SettingsController;
use Leeflets\Controller\SignInController;
use Widi\Components\Router\Route\Comparator\Equal;
use Widi\Components\Router\Route\Method\Get;

return [
    'routes' => [
        'home_route'    => [
            'route'   => '/',
            'options' => [
                'method'     => Get::class,
                'comparator' => Equal::class,
                'controller' => HomeController::class,
                'action'     => 'indexAction',
            ],
        ],
        'settings_route'    => [
            'route'   => '/settings',
            'options' => [
                'comparator' => Equal::class,
                'controller' => SettingsController::class,
                'action'     => 'editAction',
            ],
        ],
        'login_route'    => [
            'route'   => '/login',
            'options' => [
                'comparator' => Equal::class,
                'controller' => SignInController::class,
                'action'     => 'loginAction',
            ],
        ],
    ]
];