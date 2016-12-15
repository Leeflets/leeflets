<?php

namespace Leeflets\Core\Library\Template;

class Settings {

    private $settings;

    function __construct(\Leeflets\Core\Library\Settings $settings) {
        $this->settings = $settings;
    }

    function out() {
        echo $this->settings->vget(func_get_args());
    }

    function get() {
        return $this->settings->vget(func_get_args());
    }
}
