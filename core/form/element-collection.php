<?php
class LF_Form_Element_Collection extends LF_Form_Element {
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
			$class = 'LF_Form_' . LF_String::camelize( $el['type'] );

			unset( $el['type'] );

			$obj = new $class( $this, $id, $el );
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
}
