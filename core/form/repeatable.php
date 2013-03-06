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

        if ( isset( $_POST[$id] ) ) {
            $this->group_count = count( $_POST[$id] );
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
        $args['class'] = 'well';

        for ( $i = 0; $i < $this->group_count; $i++ ) {
            $fieldset = new LF_Form_Fieldset( $this, $i, $args );
            $fieldset->add_elements( $elements );
            $this->elements[$i] = $fieldset;
        }
    }
}
