<?php

namespace Leeflets\Form;

class TextField implements FieldInterface {

    protected static $type = 'text';

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $options;

    /**
     * TextField constructor.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options) {
        $defaults = [
            'placeholder' => '',
            'class' => '',
            'value' => ''
        ];

        $this->name = $name;
        $this->options = array_merge($defaults, $options);
    }

    public function render() {
        return sprintf(
            '<input type="%s" class="%s" placeholder="%s" value="%s" />',
            static::$type,
            $this->options['class'],
            $this->options['placeholder'],
            $this->options['value']
        );
    }
}