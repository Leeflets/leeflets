<?php

namespace Leeflets;

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
     * @var RouterFactory
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
    }

    public function run() {

    }

}
