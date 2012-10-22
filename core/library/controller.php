<?php
class LF_Controller {

    public $router, $view, $filesystem, $config, $user;

    function __construct( 
        LF_Router $router, LF_View $view, LF_Filesystem $filesystem,
        LF_Config $config, LF_User $user
    ) {
        $this->router = $router;
        $this->view = $view;
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->user = $user;
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
