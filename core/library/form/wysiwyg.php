<?php
class LF_Form_Wysiwyg extends LF_Form_Control {
    function __construct( $parent, $id, $args = array() ) {
        if ( !isset( $args['class'] ) ) {
            $args['class'] = 'input-block-level';
        }

        $args['class'] = trim( 'textarea wysihtml5 ' . $args['class'] );

        if ( !isset( $args['rows'] ) ) {
            $args['rows'] = 7;
        }

        parent::__construct( $parent, $id, $args );

        // Bug in wysihtml5 requires an id on the textarea
        // https://github.com/jhollingworth/bootstrap-wysihtml5/issues/28#issuecomment-5191305
        $this->atts['id'] = $this->name;
    }

    function html_middle() {
        ?>
        <textarea <?php echo $this->atts_html(); ?>><?php echo $this->value_html(); ?></textarea>
        <?php
    }    
}
