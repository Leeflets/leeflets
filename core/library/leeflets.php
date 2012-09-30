<?php
class Leeflets {

	const PHP_VERSION_REQUIRED = '5.3';

	function __construct() {
		$this->check_php_version();
		$this->setup_error_reporting();

		define( 'LF_ROOT_PATH', realpath( LF_ADMIN_PATH . '/../' ) );
		define( 'LF_CORE_PATH', LF_ADMIN_PATH . '/core' );
		define( 'LF_CONTROLLER_PATH', LF_CORE_PATH . '/controller' );
		define( 'LF_LIBRARY_PATH', LF_CORE_PATH . '/library' );
		define( 'LF_VIEW_PATH', LF_CORE_PATH . '/view' );
		define( 'LF_THEME_PATH', LF_CORE_PATH . '/theme' );
		define( 'LF_FORM_PATH', LF_CORE_PATH . '/form' );

		require LF_LIBRARY_PATH . '/string.php';
		require LF_LIBRARY_PATH . '/file.php';

		spl_autoload_register( array( $this, 'autoload' ) );

		$config_path = LF_ADMIN_PATH . '/config.php';

		$is_install = preg_match( '@setup/install/@', $_SERVER['REQUEST_URI'] );

		if ( !file_exists( $config_path ) && !$is_install ) {
			LF_Router::redirect( 'setup/install/' );
			exit;
		}

		if ( $is_install ) {
			define( 'LF_ADMIN_URL', preg_replace( '@/setup/install/.*$@', '', LF_Router::request_url() ) );
		}
		else {
			require $config_path;
		}

		$user = new LF_User();

		$is_login = preg_match( '@user/login/@', $_SERVER['REQUEST_URI'] );

		if ( !$user->is_logged_in() && !( $is_install || $is_login ) ) {
			LF_Router::redirect( 'user/login/' );
			exit;
		}

		$router = new LF_Router();
        $view = new LF_View();

		$controller_class = $router->controller_class;
		$controller = new $controller_class( $router, $view );
		
		$view->controller = $controller;
		$view->router = $router;

        $controller->call_action();
	}

	function autoload( $class ) {
		$path = LF_File::get_class_file_path( $class );
		if ( !$path ) return;
		require $path;
	}

	function check_php_version() {
		if ( version_compare( PHP_VERSION, self::PHP_VERSION_REQUIRED, '>=' ) ) return;
		die( 'Leeflets requires that you run PHP version ' . self::PHP_VERSION_REQUIRED . ' or greater. You are currently running PHP ' . PHP_VERSION . '.' );
	}

	function setup_error_reporting() {
		if ( defined( 'LF_DEBUG' ) ) {
			error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );

			if ( defined( 'LF_DEBUG_DISPLAY' ) )
				ini_set( 'display_errors', 1 );
			else
				ini_set( 'display_errors', 0 );

			if ( defined( 'LF_DEBUG_LOG' ) ) {
				ini_set( 'log_errors', 1 );
				ini_set( 'error_log', LF_DEBUG_LOG );
			}
		} 
		else {
			error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
		}

	}
}

new Leeflets();
