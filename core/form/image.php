<?php
class LF_Form_Image extends LF_Form_File {
    private $versions;

    function __construct( $parent, $id, $args = array() ) {
        if ( !isset( $args['accept_types'] ) ) {
            $args['accept_types'] = array( 'jpg', 'jpeg', 'png', 'gif' );
        }

        $this->special_args( 'versions', $args, true );

        parent::__construct( $parent, $id, $args );
    }

    function set_upload_options() {
        if ( $this->versions ) {
            // Intentionally overwriting their options with ours because we want to always have
            // thumbnails of the proper size to display in the admin
            $this->upload_options['image_versions'] = array_merge( $this->versions, $this->upload_options['image_versions'] );
        }

        parent::set_upload_options();
    }
}
