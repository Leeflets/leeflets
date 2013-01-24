<?php
class LF_Admin_Scripts extends LF_Scripts {
	function __construct( $base_url ) {
		parent::__construct( $base_url );

		$this->enqueue( 'wysihtml5' );
		$this->enqueue( 'jquery' );
		$this->enqueue( 'jquery-ui-widget' );
		$this->enqueue( 'jquery-iframe-transport' );
		$this->enqueue( 'jquery-fileupload' );
		$this->enqueue( 'bootstrap' );
		$this->enqueue( 'bootstrap-datepicker' );
		$this->enqueue( 'bootstrap-wysihtml5' );
		
		$this->add_enqueue( 'lf-script', '/core/theme/asset/js/script.js' );
	}
}
