<?php

namespace Leeflets\Core\Library\Template;

use Leeflets\Core\Library\Router;

class Upload {

    private $router;

    function __construct(Router $router) {
        $this->router = $router;
    }

    public function url($url = '') {
        echo $this->get_url($url);
    }

    public function get_url($url = '') {
        return $this->router->getUploadsUrl($url);
    }
}