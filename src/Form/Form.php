<?php

namespace Leeflets\Form;

use Widi\Components\Router\Request;

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
     * @var ValidationResult[]
     */
    private $validation;

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
     * @inheritdoc
     */
    public function validate(Request $request) {
        $data = $request->getpost();
        $validation = array_map(function($field) use ($data) {
            /** @var FieldInterface $field */
            return $field->validate($data);
        }, $this->fields);

        $this->validation = $validation;

        $result = [];

        if(!$this->isValid()) {
            return $result;
        }

        foreach ($this->validation as $validationResult) {
            $result[$validationResult->getName()] = $validationResult->getValue();
        }

        return $result;
    }

    public function isValid() {
        if(!$this->validation) {
            return false;
        }
        foreach ($this->validation as $validationResult) {
            if($validationResult->hasError()) {
                return false;
            }
        }
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
