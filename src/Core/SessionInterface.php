<?php

namespace Leeflets\Core;

interface SessionInterface {

    public function set($key, $value);

    public function get($key);

    public function delete($key);

    public function exists($key);

    public function id();

    public function destroy();
}