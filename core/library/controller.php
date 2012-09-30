<?php
class LF_Controller {

    public $router;
    public $view;

    function __construct( LF_Router $router, LF_View $view ) {
        $this->router = $router;
        $this->view = $view;
    }

    function call_action() {
    	$out = call_user_func_array( array( $this, $this->router->action ), $this->router->params );

        if ( is_null( $out ) ) {
        	$this->view->render();
        }
        elseif ( is_array( $out ) ) {
        	$this->view->render( $out );
        }
    }
}
