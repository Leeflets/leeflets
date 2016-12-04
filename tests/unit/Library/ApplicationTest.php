<?php

namespace LeefletsTest\Library;

use Leeflets\Application;
use LeefletsTest\AbstractUnitTestCase;
use Mockery as m;
use Widi\Components\Router\Router;

class ApplicationTest extends AbstractUnitTestCase {

    /**
     * @test
     */
    public function itShouldBeInstantiated() {
        $application = new Application([], m::mock(Router::class));
    }
}