<?php

namespace Leeflets\Core\Library\Admin;

use Leeflets\Core\Library\Config;
use Leeflets\Core\Library\Router;

class Styles extends \Leeflets\Core\Library\Styles {

    function __construct($base_url, Router $router, Config $config) {
        parent::__construct($base_url, $router, $config, $config->version);

        $this->enqueue('bootstrap');
        $this->enqueue('bootstrap-responsive');
        $this->enqueue('bootstrap-datepicker');
        $this->enqueue('bootstrap-wysihtml5');
        $this->enqueue('jquery-fileupload');

        $this->add_enqueue('lf-style', '/core/theme/asset/css/style.css');
    }
}
