<?php
class LF_Router {

    public $request_url;

    public $controller_name = '';
    public $action = '';
    public $params = array();
    public $controller_class = '';

    function __construct( $request_url = null ) {
        if ( is_null( $request_url ) ) $request_url = self::request_url();

        $this->request_url = $request_url;
        
        $this->parse_request_url();
        $this->set_controller_class();
    }

    private function parse_request_url() {
        $admin_url = parse_url( LF_ADMIN_URL );
        $request_url = parse_url( $this->request_url );

        $path = preg_replace( '@^' . $admin_url['path'] . '@', '', $request_url['path'] );
        $path = strtolower( trim( $path, '/' ) );
        $path = preg_replace( '@[^a-z0-9/\-_\.]@', '', $path );
        $path = str_replace( '..', '', $path );

        if ( !$path ) return;

        $segments = explode( '/', $path );

        $this->controller_name = array_shift( $segments );

        if ( !$segments ) return;
        
        $this->action = array_shift( $segments );

        $this->params = $segments;
    }
    
    private function set_controller_class() {
        $name = $this->controller_name;

        if ( !$name && !$this->action ) {
            $this->controller_name = 'home';
            $this->controller_class = 'LF_Controller_Home';
            $this->action = 'index';
            return;
        }

        $name = preg_replace( '@[/\-\.]@', '_', $name );
        $class = 'LF_Controller_' . LF_String::camelize( $name );

        $path = LF_File::get_class_file_path( $class );
        if ( !file_exists( $path ) || !method_exists( $class, $this->action ) ) {
            $this->controller_name = 'error';
            $this->controller_class = 'LF_Controller_Error';
            $this->action = 'e404';
        }
        else {
            $this->controller_class = $class;
        }
    }

    static function request_url() {
        $ssl = (  isset(  $_SERVER['HTTPS']  ) && $_SERVER['HTTPS'] == 'on'  ) ? 's' : '';
        $port = (  $_SERVER['SERVER_PORT'] != '80'  ) ? ':' . $_SERVER['SERVER_PORT'] : '';
        return sprintf(  'http%s://%s%s%s', $ssl, $_SERVER['SERVER_NAME'], $port, $_SERVER['REQUEST_URI']  );
    }

    static function redirect( $url, $status = 302 ) {
        header( 'Location: ' . $url, true, $status );
    }
}
