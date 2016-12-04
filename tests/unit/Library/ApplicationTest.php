<?php

namespace LeefletsTest\Library;

use Leeflets\Application;
use LeefletsTest\AbstractUnitTestCase;
use Mockery as m;
use Widi\Components\Router\RouterFactory;

class ApplicationTest extends AbstractUnitTestCase {

    /**
     * @test
     */
    public function itShouldBeInstantiated() {
        $factoryMock = m::mock(
            RouterFactory::class,
            ['__invoke' => 1]);
        new Application(['routes' => []], $factoryMock);
    }
}