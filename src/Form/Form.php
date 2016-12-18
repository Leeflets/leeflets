<?php

namespace Leeflets\Form;

class Form {

    private $attributes;
    
    function __construct($options) {
        $this->attributes = array_merge([
            'method' => 'POST',
            'action' => '',
            'novalidate' => true,
            'accept-charset' => 'UTF-8',
            'class' => ''
        ], $options);
    }

}
