<?php
class Leeflets {

	const PHP_VERSION_REQUIRED = '5.3';
	public $config;

	function __construct( $admin_path ) {
		$this->check_php_version();

		require $admin_path . '/core/library/config.php';

		$config = $this->config = new LF_Config( $admin_path );

		require $config->library_path . '/string.php';
		require $config->library_path . '/file.php';

		spl_autoload_register( array( $this, 'autoload' ) );

		$is_config_loaded = $this->config->load();

		$this->setup_error_reporting();

		$is_install = preg_match( '@setup/install/$@', $_SERVER['REQUEST_URI'] );

		$router = new LF_Router( $config );

		if ( !$is_config_loaded && !$is_install ) {
			LF_Router::redirect( $router->admin_url( '/setup/install/' ) );
			exit;
		}

		$user = new LF_User( $config, $router );

		$is_login = preg_match( '@user/login/@', $_SERVER['REQUEST_URI'] );

		if ( !$user->is_logged_in() && !( $is_install || $is_login ) ) {
			LF_Router::redirect( $router->admin_url( '/user/login/' ) );
			exit;
		}

        $view = new LF_View( $config, $router );
        $filesystem = new LF_Filesystem_Direct(array());

		$controller_class = $router->controller_class;
		$controller = new $controller_class( $router, $view, $filesystem, $config, $user );
		
		//$view->controller = $controller;

        $controller->call_action();
	}

	function autoload( $class ) {
		$path = LF_File::get_class_file_path( $this->config, $class );
		if ( !$path ) return;
		require $path;
	}

	function check_php_version() {
		if ( version_compare( PHP_VERSION, self::PHP_VERSION_REQUIRED, '>=' ) ) return;
		die( 'Leeflets requires that you run PHP version ' . self::PHP_VERSION_REQUIRED . ' or greater. You are currently running PHP ' . PHP_VERSION . '.' );
	}

	function setup_error_reporting( $debug ) {
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

