<?php

namespace Leeflets\Form;

class SubmitButton implements FieldInterface {

    protected static $type = 'text';

    /**
     * @var string
     */
    private $value;

    /**
     * TextField constructor.
     *
     * @param $value
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function render() {
        return sprintf(
            '<button type="submit">%s</button>',
            $this->value
        );
    }

    /**
     * @inheritdoc
     */
    public function validate(array $request) {
        return new ValidationResult('submit');
    }
}