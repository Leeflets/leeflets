<?php

namespace Tests;

use Leeflets\Application;
use LeefletsTest\AbstractIntegrationTestCase;
use Widi\Components\Router\RouterFactory;

class RoutingTest extends AbstractIntegrationTestCase {

    /**
     * @var Application
     */
    private $application;

    public function setUp() {
        parent::setUp();
        $routerFactory = new RouterFactory();
        $config = include 'src/config.php';

        $_SERVER = [
            'path_info' => '/',
            'request_method' => 'GET'
        ];

        $this->application = new Application($config, $routerFactory);
    }

    /**
     * @test
     */
    public function itShouldCallIndexActionInHomeControllerWhenRequestingIndexWithGet() {
        $this->application->run();
    }
}