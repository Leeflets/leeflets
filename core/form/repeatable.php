<?php
class LF_Form_Repeatable extends LF_Form_Fieldset {
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

        if ( isset( $_POST[$this->id] ) ) {
            $this->group_count = count( $_POST[$this->id] );
        }
        else {
            $this->group_count = $this->empty_to_show;
        }

        if ( isset( $args['elements'] ) ) {
            $this->orig_elements = $args['elements'];
        }

        parent::__construct( $parent, $id, $args );
    }

    function add_elements( $elements ) {
        for ( $i = 0; $i < $this->group_count; $i++ ) {
            $id = $this->id . '[' . $i . ']';
            $fieldset = new LF_Form_Fieldset( $this, $id );
            $fieldset->add_elements( $elements );
            $this->elements[$id] = $fieldset;
        }
    }

    function set_values( $values ) {
        if ( isset( $values[$this->id] ) ) {
            $this->group_count = count( $values[$this->id] );
            $this->elements = array();
            $this->add_elements( $this->orig_elements );
        }
        parent::set_values( $values );
    }
}
