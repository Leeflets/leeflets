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

        $_SERVER = [
            'path_info' => '/',
            'request_method' => 'GET'
        ];

        $this->application = new Application($routerFactory, [
            'one_pager_template_dir' => 'tests/integration'
        ]);
    }

    /**
     * @test
     */
    public function itShouldCallIndexActionInHomeControllerWhenRequestingIndexWithGet() {
        // TODO: create spy for indexAction
        $this->application->run();
    }
}