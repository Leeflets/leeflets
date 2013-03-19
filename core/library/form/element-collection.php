<?php
namespace Leeflets\Form;

class Element_Collection extends Element {
    public $elements, $errors;

    function __construct( $parent, $id, $args = array() ) {
        if ( isset( $args['elements'] ) ) {
            $elements = $args['elements'];
            unset( $args['elements'] );
        }
        else {
            $elements = array();
        }

        parent::__construct( $parent, $id, $args );

        $this->add_elements( $elements, $parent );

        $this->errors = array();
    }

	function add_elements( $elements ) {
		foreach ( $elements as $id => $el ) {
			$class = '\\Leeflets\\Form\\' . \Leeflets\String::camelize( $el['type'] );

			unset( $el['type'] );

			$obj = new $class( $this, $id, $el );
			$this->elements[$id] = $obj;
		}
	}

    function get_element() {
        return $this->vget_element( func_get_args() );
    }

    function vget_element( $ids ) {
        $id = array_shift( $ids );

        if ( !isset( $this->elements[$id] ) ) {
            return false;
        }

        $el = $this->elements[$id];
        
        if ( empty( $ids ) ) {
            return $el;
        }

        if ( method_exists( $el, 'vget_element' ) ) {
            return $el->vget_element( $ids );
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

    function set_values( $values ) {
        foreach ( $this->elements as $el ) {
            if ( method_exists( $el, 'set_value_from_array' ) ) {
                $el->set_value_from_array( $values );
            }
            elseif ( method_exists( $el, 'set_values' ) ) {
                $el->set_values( $values );
            }
        }
    }

    function get_value() {
        return $this->get_values();
    }

    function get_values() {
        $values = array();

        foreach ( $this->elements as $id => $el ) {
            $value = null;
            
            if ( method_exists( $el, 'get_value' ) ) {
                $value = $el->get_value();
            }

            if ( !is_null( $value ) ) {
                $values[$id] = $value;
            }
        }

        return $values;
    }
}
