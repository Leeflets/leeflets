<?php

namespace Leeflets\Form;

use Widi\Components\Router\Request;

/**
 * Interface FieldInterface
 * @package Leeflets\Form
 */
interface FieldInterface {

    /**
     * @return string
     */
    public function render();

    /**
     * @param array $request
     *
     * @return ValidationResult
     */
    public function validate(array $request);

}