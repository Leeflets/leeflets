<?php

namespace Tests\Core;

use Leeflets\Core\Session;
use Leeflets\Core\SessionInterface;
use LeefletsTest\AbstractUnitTestCase;

/**
 * Class SessionTest
 * @package Tests\Core
 */
class SessionTest extends AbstractUnitTestCase {

    /**
     * @test
     */
    public function itShouldCreateAnInstance() {
        $session = Session::init();
        $this->assertInstanceOf(SessionInterface::class, $session);
    }

    /**
     * @test
     */
    public function existsShouldReturnFalseIfKeyIsNotSet() {
        $session = Session::init();
        $this->assertFalse($session->exists('foo'));
    }

    /**
     * @test
     */
    public function existsShouldReturnTrueIfKeyIsSet() {
        $session = Session::init(['foo' => 'key']);
        $this->assertTrue($session->exists('foo'));
    }

    /**
     * @test
     */
    public function getShouldReturnAValueForAKey() {
        $session = Session::init(['foo' => 'data']);
        $this->assertSame('data', $session->get('foo'));
    }

    /**
     * @test
     */
    public function setShouldUpdateAValueForAKey() {
        $session = Session::init(['foo' => 'data']);
        $session->set('foo', 'bar');
        $this->assertSame('bar', $session->get('foo'));
    }
}