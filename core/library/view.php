<?php
class LF_View {

    public $controller;
    public $router;

    private $vars;
    
    function render( $vars = array(), $file = '' ) {
        if ( !$file ) {
            $file = $this->router->controller_name . '/' . $this->router->action;
        }

        $path = LF_VIEW_PATH . '/' . $file . '.php';
        
        $content = $this->get_content( $path, $vars );

        $vars['content'] = $content;

        echo $this->get_content( LF_THEME_PATH . '/layout.php', $vars );
    }

    function get_content( $path, $vars ) {
        if ( is_array( $vars ) ) $this->vars = $vars;
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
