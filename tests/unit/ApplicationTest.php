<?php

namespace LeefletsTest;

use Leeflets\Application;
use Mockery as m;
use Widi\Components\Router\Router;
use Widi\Components\Router\RouterFactory;

class ApplicationTest extends AbstractUnitTestCase {

    /**
     * @test
     */
    public function itShouldBeInstantiated() {
        $routerMock = $this->buildRouterMock();
        $routerMock->method('setEnableRouteCallbacks')
            ->with(true);

        $factoryMock = m::mock(
            RouterFactory::class,
            ['__invoke' => $routerMock]);
        new Application($factoryMock, ['routes' => []]);
    }

    private function buildRouterMock() {
        $mockBuilder = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor();
        return $mockBuilder->getMock();
    }
}