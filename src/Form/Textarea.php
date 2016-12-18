<?php

namespace Leeflets\Core\Library\Form;

class Textarea extends Control {
    function __construct( $parent, $id, $args = array() ) {
        if ( !isset( $args['class'] ) ) {
            $args['class'] = 'input-block-level';
        }

        if ( !isset( $args['rows'] ) ) {
            $args['rows'] = 7;
        }

        parent::__construct( $parent, $id, $args );
    }

    function html_middle() {
        ?>
        <textarea <?php echo $this->atts_html(); ?>><?php echo $this->value_html(); ?></textarea>
        <?php
    }    
}
