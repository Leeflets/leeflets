<?php
namespace Leeflets\Form;

class Repeatable extends Fieldset {
    public $empty_to_show, $orig_elements, $group_count;
    
    function __construct( $parent, $id, $args = array() ) {
        // Defaults
        $args = array_merge( array(
            'empty-to-show' => 1
        ), $args );

        $this->special_args( 'empty-to-show', $args );

        if ( !isset( $args['class'] ) ) {
            $args['class'] = '';
        }

        $args['class'] = trim( $args['class'] . ' repeatable' );

        if ( isset( $args['elements'] ) ) {
            $this->orig_elements = $args['elements'];
        }

        parent::__construct( $parent, $id, $args );
    }

    function add_elements( $elements ) {
        $args['class'] = 'well';

        for ( $i = 0; $i < $this->group_count; $i++ ) {
            $fieldset = new LF_Form_Fieldset( $this, $i, $args );
            $fieldset->add_elements( $elements );
            $this->elements[$i] = $fieldset;
        }
    }

    function set_value_from_array( $array ) {
        $value = $this->get_value_from_array( $this->id, $array );

        // Recreate the field groups based on the new values
        $this->elements = array();

        if ( $value && is_array( $value ) ) {
            $this->group_count = count( $value );
        }
        else {
            $this->group_count = $this->empty_to_show;
        }

        $this->add_elements( $this->orig_elements );

        // Set values for each of the fields in the field groups
        $this->set_values( $array );
    }
}
