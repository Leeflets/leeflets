<?php

namespace LeefletsTest\Library;

use Leeflets\Core\Library\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

    private $router = null;

    public function setUp() {
        $this->router = new Router([], 'http://localhost', 'http://localhost/hello', false);
    }

    public function testAdminUrl() {
        $this->markTestIncomplete();
    }
}