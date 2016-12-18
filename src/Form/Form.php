<?php

namespace Leeflets\Form;

/**
 * Class Form
 * @package Leeflets\Form
 */
class Form {

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var FieldInterface[]
     */
    private $fields;

    /**
     * Form constructor.
     *
     * @param array $options
     */
    function __construct($options) {
        $this->attributes = array_merge([
            'method' => 'POST',
            'action' => '',
            'accept-charset' => 'UTF-8',
            'class' => ''
        ], $options);

        $this->fields = [];
    }

    /**
     * @param FieldInterface $field
     */
    public function add(FieldInterface $field) {
        $this->fields[] = $field;
    }

    /**
     * @return bool
     */
    public function validate() {
        return true;
    }

    /**
     * @return string
     */
    public function render() {
        $openTag = sprintf(
            '<form class="%s" method="%s" action="%s" accept-charset="%s">',
            $this->attributes['class'],
            $this->attributes['method'],
            $this->attributes['action'],
            $this->attributes['accept-charset']
        );
        $closeTag = '</form>';

        $html = $openTag;

        foreach ($this->fields as $field) {
            $html .= $field->render();
        }

        $html .= $closeTag;

        return $html;
    }

    public function __toString() {
        return $this->render();
    }
}
