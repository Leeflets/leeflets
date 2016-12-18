<?php

namespace Leeflets\Form;

/**
 * Class ValidationResult
 * @package Leeflets\Form
 */
class ValidationResult {

    /**
     * @var string
     */
    private $error;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * ValidationResult constructor.
     *
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error) {
        $this->error = $error;
    }

    public function hasError() {
        return isset($this->error) && !empty($this->error);
    }

}