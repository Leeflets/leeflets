<?php

namespace Leeflets\Form;

use Widi\Components\Router\Request;

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
            'value' => '',
            'required' => false
        ];

        $this->name = $name;
        $this->options = array_merge($defaults, $options);
    }

    /**
     * @inheritdoc
     */
    public function render() {
        return sprintf(
            '<input type="%s" name="%s" class="%s" placeholder="%s" value="%s" required="%s"/>',
            static::$type,
            $this->name,
            $this->options['class'],
            $this->options['placeholder'],
            $this->options['value'],
            $this->options['required']
        );
    }

    /**
     * @inheritdoc
     */
    public function validate(array $request) {
        $result = new ValidationResult($this->name);
        $value = null;

        if(isset($request[$this->name])) {
            $value = $request[$this->name];
        }

        if(!$this->options['required']) {
            $result->setValue($value);
            return $result;
        }

        if(empty($value)) {
            $result->setError('Required but not provided');
        } else {
            $result->setValue($value);
        }

        return $result;
    }
}