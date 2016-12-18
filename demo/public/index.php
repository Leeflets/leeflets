<?php

use Leeflets\Application;
use Widi\Components\Router\RouterFactory;

chdir(dirname(dirname(__DIR__)));

session_start();

include 'vendor/autoload.php';

$routerFactory = new RouterFactory();

$application = new Application($routerFactory, include 'demo/public/config.php');

$application->run();

