<?php
class Leeflets {

	const PHP_VERSION_REQUIRED = '5.3';
	public $config;

	function __construct( $admin_path ) {
		$this->check_php_version();
		$this->check_magic_quotes();

		require $admin_path . '/core/library/config.php';

		$config = $this->config = new LF_Config( $admin_path );

		require $config->library_path . '/string.php';
		require $config->library_path . '/file.php';

		spl_autoload_register( array( $this, 'autoload' ) );

		$is_config_loaded = $this->config->load();

		$this->setup_error_reporting();

		$is_login = preg_match( '@user/login/@', $_SERVER['REQUEST_URI'] );

		if ( !$is_config_loaded ) {
			$router = new LF_Router( $config, null, '/setup/install/' );
			$is_install = true;
		}
		else {
			$router = new LF_Router( $config );
			$is_install = false;
		}

		$user = new LF_User( $config, $router );

		if ( !$user->is_logged_in() && !( $is_install || $is_login ) ) {
			LF_Router::redirect( $router->admin_url( '/user/login/' ) );
			exit;
		}

        $view = new LF_View( $config, $router );
        $settings = new LF_Settings( $config );
        
        if ( isset( $settings->data['connection-type'] ) && 'direct' != $settings->data['connection-type'] ) {
			$class_name = LF_Filesystem::get_class_name( $settings->data['connection-type'] );
			$filesystem = new $class_name( $config, array(
				'connection_type' => $settings->data['connection-type'],
				'hostname' => $settings->data['connection-hostname'],
				'username' => $settings->data['connection-username'],
				'password' => $settings->data['connection-password']
			));
        }
        else {
	        $filesystem = new LF_Filesystem_Direct( $config );
        }

        $template = new LF_Template( $config, $filesystem, $router, $settings );

		$controller_class = $router->controller_class;
		$controller = new $controller_class( $router, $view, $filesystem, $config, $user, $template, $settings );
		
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

	function check_magic_quotes() {
		if ( !get_magic_quotes_gpc() ) return;
		die( 'The PHP setting <a href="http://www.php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc">magic_quotes_gpc</a> is currently on. Leeflets requires that you have it turned off.' );
	}

	function setup_error_reporting() {
		if ( $this->config->debug ) {
			error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );

			if ( $this->config->debug_display )
				ini_set( 'display_errors', 1 );
			else
				ini_set( 'display_errors', 0 );

			if ( $this->config->debug_log ) {
				ini_set( 'log_errors', 1 );
				ini_set( 'error_log', LF_DEBUG_LOG );
			}
		} 
		else {
			error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
		}

	}
}

