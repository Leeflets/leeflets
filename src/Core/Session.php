<?php

namespace Leeflets\Core;

/**
 * Simple object oriented wrapper around the session variable
 * @package Leeflets\Core
 */
class Session implements SessionInterface {

    private function __construct() {

    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        return $_SESSION[$key];
    }

    public function delete($key) {
        unset($_SESSION[$key]);
    }

    public function exists($key) {
        return isset($_SESSION[$key]);
    }

    public function destroy() {
        session_destroy();
    }

    public function id() {
        return session_id();
    }

    /**
     * @param array $data
     *
     * @return SessionInterface
     */
    public static function init($data = []) {
        $_SESSION = $data;
        return new Session();
    }
}