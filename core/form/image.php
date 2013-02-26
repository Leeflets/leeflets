<?php
class LF_Form_Image extends LF_Form_Control {
    function __construct( $parent, $id, $args = array() ) {
        if ( !isset( $args['class'] ) ) {
            $args['class'] = '';
        }

        $args['class'] = trim( $args['class'] . ' file-upload' );

        if ( !isset( $args['accept_types'] ) ) {
            $args['accept_types'] = array( 'jpeg', 'jpg', 'gif', 'png' );
        }

        if ( isset( $args['sizes'] ) ) {
            $args['data-sizes'] = json_encode( $args['sizes'] );
            unset( $args['sizes'] );
        }
        
        parent::__construct( $parent, $id, $args );

        // We don't want this saved on form submit
        $this->atts['data-name'] = $this->atts['name'];
        $this->atts['name'] = 'files';
    }
   
    function html_middle() {
        ?>
        <?php if ( $this->value ) : ?>
        <img src="<?php echo $this->esc_html( $this->value ); ?>" alt="" />
        <?php endif; ?>

        <div class="input-append">
            <div class="uneditable-input span4"><i class="icon-file"></i> <span class="filename"><?php echo $this->esc_html( $this->value ); ?></span></div>
            <input type="hidden" name="<?php echo $this->esc_att( $this->atts['data-name'] ); ?>" class="filename-hidden" value="<?php echo $this->esc_att( $this->value ); ?>" />
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
