<?php

namespace Leeflets\Core\Library\Template;

class Content {

    private $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function out() {
        echo $this->vget(func_get_args());
    }

    public function vget($keys) {
        $content = $this->data;

        foreach ($keys as $key) {
            if (!isset($content[$key])) {
                return '';
            }

            $content = $content[$key];
        }

        return $content;
    }

    public function get() {
        return $this->vget(func_get_args());
    }
}