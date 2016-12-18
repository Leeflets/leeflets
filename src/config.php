<?php

use Leeflets\Controller\HomeController;
use Leeflets\Controller\SettingsController;
use Leeflets\Controller\SignInController;
use Widi\Components\Router\Route\Comparator\Equal;
use Widi\Components\Router\Route\Method\Get;
use Widi\Components\Router\Route\Method\Post;

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
                'method'     => Get::class,
                'comparator' => Equal::class,
                'controller' => SettingsController::class,
                'action'     => 'editAction',
            ],
        ],
        'login_route'    => [
            'route'   => '/login',
            'options' => [
                'method'     => Get::class,
                'comparator' => Equal::class,
                'controller' => SignInController::class,
                'action'     => 'indexAction',
            ],
        ],
        'signin_route'    => [
            'route'   => '/login',
            'options' => [
                'method'     => Post::class,
                'comparator' => Equal::class,
                'controller' => SignInController::class,
                'action'     => 'loginAction',
            ],
        ],
    ],
    'one_pager_template' => 'onepager.twig',
    'title' => 'Default Title',
];