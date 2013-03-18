<?php
namespace Leeflets\Library\Admin;

class LF_Admin_Styles extends LF_Styles {
	function __construct( $base_url, LF_Router $router ) {
		parent::__construct( $base_url, $router );

        $this->enqueue( 'bootstrap' );
        $this->enqueue( 'bootstrap-responsive' );
        $this->enqueue( 'bootstrap-datepicker' );
        $this->enqueue( 'bootstrap-wysihtml5' );
        $this->enqueue( 'jquery-fileupload' );

        $this->add_enqueue( 'lf-style', '/core/theme/asset/css/style.css' );
	}
}
