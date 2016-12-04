<?php

namespace Leeflets;

use Widi\Components\Router\Router;

/**
 * Class Leeflets
 * @package Leeflets\Core\Library
 */
class Application {

    /**
     * @var array
     */
    private $config;

    /**
     * @var Router
     */
    private $router;

    /**
     * Application constructor.
     *
     * @param array $config
     * @param Router $router
     */
    public function __construct($config, Router $router) {
        $this->config = $config;

        $this->router = $router;
    }

    public function run() {

    }

}
