<?php

namespace Leeflets\Core\Library\Admin;

use Leeflets\Core\Library\Config;
use Leeflets\Core\Library\Router;

class Scripts extends \Leeflets\Core\Library\Scripts {

    function __construct($base_url, Router $router, Config $config) {
        parent::__construct($base_url, $router, $config, $config->version);

        $this->enqueue('wysihtml5');
        $this->enqueue('jquery');
        $this->enqueue('jquery-ui-widget');
        $this->enqueue('jquery-iframe-transport');
        $this->enqueue('jquery-fileupload');
        $this->enqueue('bootstrap');
        $this->enqueue('bootstrap-datepicker');
        $this->enqueue('bootstrap-wysihtml5');
        $this->enqueue('md5');

        $min = $config->debug ? '' : '.min';

        $this->add_enqueue('lf-script', '/core/theme/asset/js/script' . $min . '.js');
    }
}
