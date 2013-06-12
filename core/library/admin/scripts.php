<?php
namespace Leeflets\Admin;

class Scripts extends \Leeflets\Scripts {
	function __construct( $base_url, \Leeflets\Router $router, $default_version = '' ) {
		parent::__construct( $base_url, $router, $default_version );

		$this->enqueue( 'wysihtml5' );
		$this->enqueue( 'jquery' );
		$this->enqueue( 'jquery-ui-widget' );
		$this->enqueue( 'jquery-iframe-transport' );
		$this->enqueue( 'jquery-fileupload' );
		$this->enqueue( 'bootstrap' );
		$this->enqueue( 'bootstrap-datepicker' );
		$this->enqueue( 'bootstrap-wysihtml5' );
		$this->enqueue( 'md5' );
		
		$this->add_enqueue( 'lf-script', '/core/theme/asset/js/script.js' );
	}
}
