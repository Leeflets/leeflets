<?php

namespace Leeflets;

use Widi\Components\Router\Route\Method\Get;
use Widi\Components\Router\Router;
use Widi\Components\Router\RouterFactory;

/**
 * Class Leeflets
 * @package Leeflets\Core\Library
 */
class Application {

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Application constructor.
     *
     * @param array $config
     * @param RouterFactory $routerFactory
     */
    public function __construct($config, RouterFactory $routerFactory) {
        $this->config = $config;

        $this->router = $routerFactory($config['routes']);
        $this->router->setEnableRouteCallbacks(true);
    }

    public function run() {
        $route = $this->router->route();
        if ($this->router->isRouteNotFound()) {
            $route = $this->router->route('/404', Get::METHOD_STRING);
        }

        $controller = $route->getController();

    }

}
