<?php

namespace Leeflets;

use Leeflets\Controller\AbstractController;
use Leeflets\Core\ResponseInterface;
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
     * @param RouterFactory $routerFactory
     * @param array $config
     */
    public function __construct(RouterFactory $routerFactory, $config = []) {
        $basicConfig = include dirname(__FILE__) . '/config.php';
        $this->config = array_merge($basicConfig, $config);

        $this->router = $routerFactory($this->config['routes']);
        $this->router->setEnableRouteCallbacks(true);
    }

    /**
     * @todo Use dispatcher and director
     */
    public function run() {
        $route = $this->router->route();
        if ($this->router->isRouteNotFound()) {
            $route = $this->router->route('/404', Get::METHOD_STRING);
        }

        $controllerName = $route->getController();
        $actionName = $route->getAction();
        $request = $this->router->getRequest();

        /** @var AbstractController $controller */
        $controller = new $controllerName();
        $controller->setConfig($this->config);

        /** @var ResponseInterface $response */
        $response = $controller->$actionName($request);

        $this->setHeader($response);
        echo $response->output();
    }

    /**
     * @param ResponseInterface $response
     */
    private function setHeader($response) {

    }

}
