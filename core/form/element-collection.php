<?php
class LF_Form_Element_Collection extends LF_Form_Element {
    public $elements, $errors;

    function __construct( $form, $id, $args = array() ) {
        if ( isset( $args['elements'] ) ) {
            $this->add_elements( $args['elements'], $form );
            unset( $args['elements'] );
        }
        else {
            $this->elements = array();
        }

        $this->errors = array();

        parent::__construct( $form, $id, $args );
    }

	function add_elements( $elements, $form = null ) {
		if ( is_null( $form ) ) $form = $this;

		foreach ( $elements as $id => $el ) {
			$class = 'LF_Form_' . LF_String::camelize( $el['type'] );
			unset( $el['type'] );
			$obj = new $class( $form, $id, $el );
			$this->elements[$id] = $obj;
		}
	}

    function validate() {
    	$this->errors = array();

        foreach ( $this->elements as $el ) {
            $errors = $el->validate();
            if ( empty( $errors ) ) continue;
            $this->errors[$el->id] = $errors;
        }
        
        return $this->errors;
    }

    function get_values() {
        $values = array();
        foreach ( $this->elements as $el ) {
            if ( method_exists( $el, 'get_values' ) ) {
                $values = array_merge( $el->get_values(), $values );
            }
            else {
                $values[$el->id] = $el->value;
            }
        }
        return $values;
    }

    function set_values( $values ) {
        foreach ( $values as $id => $value ) {
            if ( isset( $this->elements[$id] ) ) {
                $this->elements[$id]->value = $value;
            }
        }

        foreach ( $this->elements as $el ) {
            if ( method_exists( $el, 'set_values' ) ) {
                $el->set_values( $values );
            }
        }
    }
}
