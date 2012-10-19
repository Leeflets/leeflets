<?php
class LF_Router {

    public $request_url, $base_request_url, $request_path;
    public $config;
    public $admin_url, $site_url, $admin_dir_name;

    public $controller_name = '';
    public $action = '';
    public $params = array();
    public $controller_class = '';

    function __construct( $config, $base_request_url = null, $request_path = null ) {
        if ( is_null( $base_request_url ) ) $base_request_url = self::base_request_url();
        if ( is_null( $request_path ) ) $request_path = $_SERVER['REQUEST_URI'];

        $this->base_request_url = $base_request_url;
        $this->request_path = $request_path;
        $this->request_url = $base_request_url . $request_path;
        $this->config = $config;

        $this->set_urls();
        $this->parse_request_url();
        $this->set_controller_class();
    }

    private function set_urls() {
        $admin_path = str_replace( '/index.php', '', $_SERVER['SCRIPT_NAME'] );
        $root_path = substr( $admin_path, 0, strrpos( $admin_path, '/' ) );

        $this->admin_url = $this->base_request_url . $admin_path;
        $this->site_url = $this->base_request_url . $root_path;

        $this->admin_dir_name = substr( $admin_path, strrpos( $admin_path, '/' )+1 );
    }

    function admin_url( $path = '' ) {
        return $this->admin_url . '/' . ltrim( $path, '/' );
    }

    function site_url( $path = '' ) {
        return $this->site_url . '/' . ltrim( $path, '/' );
    }

    private function parse_request_url() {
        $admin_url = parse_url( $this->admin_url );
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

        $path = LF_File::get_class_file_path( $this->config, $class );
        if ( !file_exists( $path ) || !method_exists( $class, $this->action ) ) {
            $this->controller_name = 'error';
            $this->controller_class = 'LF_Controller_Error';
            $this->action = '404';
        }
        else {
            $this->controller_class = $class;
        }
    }

    static function base_request_url() {
        $ssl = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 's' : '';
        $port = ( $_SERVER['SERVER_PORT'] != '80'  ) ? ':' . $_SERVER['SERVER_PORT'] : '';
        return sprintf('http%s://%s%s', $ssl, $_SERVER['SERVER_NAME'], $port );
    }

    static function redirect( $url, $status = 302 ) {
        header( 'Location: ' . $url, true, $status );
    }
}
