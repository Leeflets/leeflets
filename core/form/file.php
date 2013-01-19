<?php
class LF_Form_File extends LF_Form_Control {
    function __construct( $parent, $id, $args = array() ) {
        /*
        if ( !isset( $args['validation'] ) ) {
            $args['validation'] = array();
        }

        array_unshift( $args['validation'], array(
            'callback' => array( $this, 'valid_email' ),
            'msg' => 'Sorry, that is not a valid email address.'
        ));
        */
        if ( !isset( $args['class'] ) ) {
            $args['class'] = '';
        }

        $args['class'] = trim( $args['class'] . ' file-upload' );

        if ( isset( $args['multiple'] ) && $args['multiple'] ) {
            $this->has_multiple_values = true;
        }

        parent::__construct( $parent, $id, $args );

        //$this->atts['name'] = 'files';
    }
   
    function html_middle() {
        ?>
        <div class="input-append">
            <div class="uneditable-input span4"><i class="icon-file"></i> <span class="filename"><?php echo $this->esc_html( $this->value ); ?></span></div>
            <span class="btn btn-primary fileinput-button">
                <span>Browse</span>
                <input type="file" <?php echo $this->atts_html(); ?> />
            </span>
            <button type="button" class="btn btn-remove">Remove</button>
        </div>
        <div class="progress progress-success progress-striped active hide" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="bar" style="width:0%;"></div>
        </div>
        <div class="drop-pad hide"><div>Drop files here</div></div>
        <?php
    }
}
