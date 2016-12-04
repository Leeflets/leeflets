<?php

namespace Leeflets\Core\Library;

class Controller {

    public $router, $view, $filesystem, $config, $user, 
        $template, $settings, $hook, $content, $addon;
    protected $no_auth_actions;

    function __construct( 
        Router $router, View $view, Filesystem $filesystem,
        Config $config, User $user, Template $template, 
        Settings $settings, Hook $hook, Content $content
    ) {
        $this->router = $router;
        $this->view = $view;
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->user = $user;
        $this->template = $template;
        $this->settings = $settings;
        $this->hook = $hook;
        $this->content = $content;
    }

    function is_no_auth_action() {
        if ( is_null( $this->no_auth_actions ) ) return false;
        return in_array( $this->router->action, $this->no_auth_actions );
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

    function _check_connection() {
        $class_name = $this->filesystem->get_class_name( $_POST['connection']['type'] );
        $this->filesystem = new $class_name( $this->config, array(
            'connection_type' => $_POST['connection']['type'],
            'hostname' => $_POST['connection']['hostname'],
            'username' => $_POST['connection']['username'],
            'password' => $_POST['connection']['password']
        ));

        return $this->filesystem->connect();
    }
}
