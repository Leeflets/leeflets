<?php
class LF_Form_File extends LF_Form_Control {
    private $drop_msg, $button_txt, $accept_types;

    function __construct( $parent, $id, $args = array() ) {
        if ( !isset( $args['class'] ) ) {
            $args['class'] = '';
        }

        $args['class'] = trim( $args['class'] . ' file-upload' );

        if ( isset( $args['multiple'] ) && $args['multiple'] ) {
            $this->has_multiple_values = true;
            $this->drop_msg = 'Drop files here to upload';
            $this->button_txt = 'Select Files';
        }
        else {
            $this->drop_msg = 'Drop a file here to upload';
            $this->button_txt = 'Select a File';
        }

        $this->special_args( 'accept_types', $args, true );

        parent::__construct( $parent, $id, $args );

        // We don't want this saved on form submit
        $this->atts['data-name'] = $this->atts['name'];
        $this->atts['name'] = 'files';
    }

    function html_middle() {
        ?>
        <div class="drop-pad <?php echo ( !$this->has_multiple_values && $this->value ) ? 'hide' : ''; ?>">
            <div>
                <p><?php echo $this->drop_msg; ?></p>
                <span class="btn fileinput-button">
                    <span><?php echo $this->button_txt; ?></span>
                    <input type="file" <?php echo $this->atts_html(); ?> />
                </span>
            </div>
        </div>
        
        <div class="progress progress-success progress-striped active hide" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="bar" style="width:0%;"></div>
        </div>

        <?php
        $this->file_list_html();
    }

    function get_file_list_html() {
        ob_start();
        $this->file_list_html();
        return ob_get_clean();
    }

    function file_list_html() {
        ?>
        <div class="file-list">
        <?php
        $hidden_name = $this->atts['data-name'];

        if ( !is_array( $this->value ) ) {
            $this->value = array();
        }

        if ( $this->has_multiple_values ) {
            $hidden_name .= '[]';
        }
        
        foreach ( $this->value as $i => $val ) :
            if ( !$val ) continue;
            ?>
            <div class="file-item">
                <a class="label label-inverse remove" href="<?php echo $this->form->router->admin_url( '/content/remove-upload/' . urlencode($this->atts['data-name']) . '/' . $i . '/' ); ?>">Remove</a>
                <div class="file-preview" title="<?php echo $this->esc_att( $val['name'] ); ?>">
                    <img class="img-rounded" src="<?php echo $this->esc_att( $this->form->router->get_uploads_url( $val['path'] ) ); ?>">
                </div>
            </div>        
            <?php
        endforeach;
        ?>
        </div>
        <?php        
    }

    function load_post_value() {
        $this->value = array();

        if ( !isset( $_FILES['files'] ) ) return;

        $options = array();
        
        if ( $this->accept_types ) {
            $types = array_map( 'preg_quote', $this->accept_types );
            $options['accept_file_types'] = '/\.(' . implode( '|', $types ) . ')$/i';
        }

        $upload = new LF_Upload( $this->form->config, $this->form->router, $this->form->settings, $options );
        $this->value = $upload->post( false );
    }
}
