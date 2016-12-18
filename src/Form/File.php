<?php

namespace Leeflets\Core\Library\Form;

class File extends Control {
    protected $drop_msg, $button_txt, $accept_types, $subfolder, $preview_style, $upload_options;

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

        $this->upload_options['image_versions'] = array(
            'thumbnail' => array(
                'width' => 150,
                'height' => 150,
                'crop' => true
            ),
            'thumbnail@2x' => array(
                'width' => 300,
                'height' => 300,
                'crop' => true
            )
        );

        $this->special_args( 'accept_types, subfolder, preview-style', $args, true );

        parent::__construct( $parent, $id, $args );

        // We don't want this saved on form submit
        $this->atts['data-name'] = $this->atts['name'];
        $this->atts['name'] = 'files';
        $this->atts['data-upload-url'] = $this->form->router->admin_url( '/content/upload/' );
    }

    function html() {
        ?>

        <div class="control-group <?php echo $this->class; if ( !empty( $this->errors ) ) echo ' error'; ?>" <?php echo $this->style_att(); ?>>
            <?php if ( $this->label != '' ) : ?>
            <label><?php echo $this->label; echo ($this->required) ? '<span class="req">*</span>' : '' ?></label>
            <?php endif; ?>

            <?php $this->tip_html(); ?>

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

            <?php $this->file_list_html(); ?>

            <?php $this->errors_html(); ?>
        </div>

        <?php
    }
    
    function get_file_list_html() {
        ob_start();
        $this->file_list_html();
        return ob_get_clean();
    }

    function file_list_html() {
        if ( !is_array( $this->value ) ) {
            $files = array();
        }
        else {
            $files = $this->value;
        }

        if ( !$this->has_multiple_values && $files ) {
            $files = array( $files );
        }

        if ( !$this->has_multiple_values && isset( $files[0]['error'] ) ) {
            $this->error_html( $files[0]['error'] );
            return;
        }

        if ( !$files ) {
            return;
        }
        ?>

        <div class="file-list">
        
        <?php
        foreach ( $files as $i => $file ) :
            if ( !$file || isset( $file['error'] ) ) continue;
            ?>

            <div class="file-item">
                <a class="label label-inverse remove" href="<?php echo $this->form->router->admin_url( '/content/remove-upload/' . urlencode($this->atts['data-name']) . '/' . $i . '/' ); ?>">Remove</a>
                <div class="file-preview img-rounded <?php echo $this->get_file_type_class( $file['type'] ); ?>" title="<?php echo $this->esc_att( $file['name'] ); ?>" style="<?php echo $this->esc_att( $this->preview_style ); ?>">
                    <?php if ( preg_match( '@^image@', $file['type'] ) ) : ?>
                        <?php $this->image_preview( $file ); ?>
                    <?php else : ?>
                    <div class="filename"><?php echo $this->esc_html( $file['name'] ); ?></div>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="<?php echo $this->esc_att( $this->atts['data-name'] ); ?>" value="<?php echo $this->esc_att( json_encode( $file ) ); ?>" />
            </div>

            <?php
        endforeach;
        ?>

        </div>

        <?php
        foreach ( $files as $file ) {
            if ( !isset( $file['error'] ) ) continue;
            $this->error_html( $file['error'] );
        }
    }

    function image_preview( $file ) {
        if ( isset( $file['in_template'] ) ) {
            $url = $this->form->router->get_template_url();
        }
        elseif ( isset( $file['in_addon'] ) ) {
            $url = $this->form->router->get_addon_url( $file['in_addon'] );
        }
        else {
            $url = $this->form->router->get_uploads_url();
        }

        if ( isset( $file['versions']['thumbnail']['path'] ) ) {
            $url_std = $url . $file['versions']['thumbnail']['path'];
        }
        else {
            $url_std = $url . $file['path'];
        }

        if ( isset( $file['versions']['thumbnail@2x']['path'] ) ) {
            $url_2x = $url . $file['versions']['thumbnail@2x']['path'];
        }
        else {
            $url_2x = $url . $file['path'];
        }
        ?>
        <img class="dpi-standard" src="<?php echo $this->esc_att( $url_std ); ?>">
        <img class="dpi-2x" src="<?php echo $this->esc_att( $url_2x ); ?>">
        <?php
    }

    function get_file_type_class( $type ) {
        $type = $this->get_file_type( $type );
        if ( !$type ) {
            $type = 'generic';
        }

        return 'file-type file-type-' . $type;
    }

    function get_file_type( $type ) {
        switch ( $type ) {
            case 'application/zip':
            case 'application/x-gzip':
                return 'compressed';
            case 'application/pdf':
                return 'pdf';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'word';
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return 'excel';
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                return 'powerpoint';
            case 'application/x-iwork-keynote-sffkey':
                return 'keynote';
            case 'application/x-iwork-pages-sffpages':
                return 'pages';
            case 'application/x-iwork-numbers-sffnumbers':
                return 'numbers';
        }

        $regexs = array(
            '^audio' => 'music',
            '^video' => 'movie',
            '^image' => 'image'
        );

        foreach ( $regexs as $regex => $class ) {
            if ( preg_match( '@' . $regex . '@', $type ) ) {
                return $class;
            }
        }

        return false;
    }

    function set_upload_options() {
        if ( $this->accept_types ) {
            $types = array_map( 'preg_quote', $this->accept_types );
            $this->upload_options['accept_file_types'] = '/\.(' . implode( '|', $types ) . ')$/i';
        }

        if ( $this->subfolder ) {
            $this->upload_options['upload_subdir'] = $this->subfolder;
        }
    }

    function load_post_value() {
        $this->value = $this->get_value_from_array( $_POST );

        if ( is_null( $this->value ) ) {
            $this->value = array();
        }

        if ( !isset( $_FILES['files'] ) ) {
            return;
        }

        if ( $_POST['input-name'] != $this->atts['name'] ) {
            return;
        }

        $this->set_upload_options();

        $upload = new \Leeflets\Upload( $this->form->config, $this->form->router, $this->form->settings, $this->upload_options );
        $this->value = $upload->post( false );

        if ( !$this->has_multiple_values && is_array( $this->value ) ) {
            $this->value = $this->value[0];
        }
    }

    // We override the function in the Control class because
    // we don't want to set the value if it's not in the array
    function set_value_from_array( $array ) {
        $value = $this->get_value_from_array( $array );
        //echo $this->name, ' - ', var_dump( $value ), "\n";
        //print_r($this->value);

        if ( is_null( $value ) ) {
            return false;
        }

        if ( !is_array( $value ) ) {
            if ( !( $value = json_decode( $value, true ) ) ) {
                return false;
            }
        }

        $this->value = $value;
        return true;
    }
}
