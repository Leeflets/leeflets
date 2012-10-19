<?php
class LF_Form_Element_Collection extends LF_Form_Element {
    public $elements, $errors;

    function __construct( $form, $id, $args = array() ) {
        if ( isset( $args['elements'] ) ) {
            $this->add_elements( $args['elements'] );
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
			$this->elements[] = $obj;
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
}
