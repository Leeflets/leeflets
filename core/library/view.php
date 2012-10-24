<?php
class LF_View {

    public $router, $config;

    private $vars;

    function __construct( $config, $router ) {
        $this->config = $config;
        $this->router = $router;
    }
    
    function render( $vars = array(), $file = '' ) {
        if ( !$file ) {
            $file = $this->router->controller_name . '/' . $this->router->action;
        }

        if ( !isset( $vars['content'] ) ) {
            $vars['content'] = '';
        }

        $path = $this->config->view_path . '/' . $file . '.php';

        if ( file_exists( $path ) ) {
            $content = $this->get_content( $path, $vars );
            $vars['content'] .= $content;
        }

        echo $this->get_content( $this->config->theme_path . '/layout.php', $vars );
    }

    function get_content( $path, $vars ) {
        if ( is_array( $vars ) ) $this->vars = $vars;
        extract( $vars );
        ob_start();
        include $path;
        return ob_get_clean();
    }

    function out( $var ) {
        if ( isset( $this->vars[$var] ) ) {
            echo $this->vars[$var];
        }
    }

    function get( $var ) {
        if ( isset( $this->vars[$var] ) ) {
            return $this->vars[$var];
        }
        else {
            return '';
        }
    }
}
