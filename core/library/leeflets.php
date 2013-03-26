<?php
namespace Leeflets;

class Leeflets {

	public $config;

	function __construct( $admin_path ) {
		$this->check_magic_quotes();

		require $admin_path . '/core/library/config.php';

		$config = $this->config = new Config( $admin_path );

		require $config->library_path . '/string.php';
		require $config->library_path . '/file.php';

		spl_autoload_register( array( $this, 'autoload' ) );

		$is_config_loaded = $this->config->load();

		$this->setup_error_reporting();

		$is_login = preg_match( '@user/login/@', $_SERVER['REQUEST_URI'] );

		if ( !$is_config_loaded ) {
			$router = new Router( $config, null, '/setup/install/' );
			$is_install = true;
		}
		else {
			$router = new Router( $config );
			$is_install = false;
		}

		$hook = new Hook();

		$admin_script = new Admin\Scripts( $router->admin_url, $router );
		$admin_style = new Admin\Styles( $router->admin_url, $router );

        $hook->add( 'admin_head', array( $admin_style, 'do_items' ), 0, 10 );
        $hook->add( 'admin_head', array( $admin_script, 'do_head_items' ), 0, 10 );
        $hook->add( 'admin_footer', array( $admin_script, 'do_footer_items' ), 0, 10 );

		$template_script = new Template\Scripts( '', $router );
		$template_style = new Template\Styles( '', $router );

        $hook->add( 'head', array( $template_style, 'do_items' ), 0, 10 );
        $hook->add( 'head', array( $template_script, 'do_head_items' ), 0, 10 );
        $hook->add( 'footer', array( $template_script, 'do_footer_items' ), 0, 10 );

		$user = new User( $config, $router );

		if ( !$user->is_logged_in() && !( $is_install || $is_login ) ) {
			Router::redirect( $router->admin_url( '/user/login/' ) );
			exit;
		}

		$settings = new Settings( $config );
		
		$addon = new Addon( $config, $settings, $hook, $admin_script, $admin_style, $template_script, $template_style );
		$addon->load_active();
		
		$view = new View( $config, $router, $hook );

		if ( $settings->get( 'connection', 'type' ) && 'direct' != $settings->get( 'connection', 'type' ) ) {
			$class_name = Filesystem::get_class_name( $settings->get( 'connection', 'type' ) );
			$filesystem = new $class_name( $config, array(
					'connection_type' => $settings->get( 'connection', 'type' ),
					'hostname' => $settings->get( 'connection', 'hostname' ),
					'username' => $settings->get( 'connection', 'username' ),
					'password' => $settings->get( 'connection', 'password' )
				) );
		}
		else {
			$filesystem = new Filesystem\Direct( $config );
		}

		$content = new Content( $config, $filesystem, $router, $settings, $hook );
		$template = new Template( $config, $filesystem, $router, $settings, $hook, $template_script, $template_style, $content );

		$controller_class = $router->controller_class;
		$controller = new $controller_class( $router, $view, $filesystem, $config, $user, $template, $settings, $hook, $content );

		$controller->call_action();
	}

	function autoload( $class ) {
		$path = File::get_class_file_path( $this->config, $class );
		if ( !$path ) return;
		require $path;
	}

	function check_magic_quotes() {
		if ( !get_magic_quotes_gpc() ) {
			return;
		}

		$process = array( &$_GET, &$_POST, &$_COOKIE, &$_REQUEST );
		while ( list( $key, $val ) = each( $process ) ) {
			foreach ( $val as $k => $v ) {
				unset( $process[$key][$k] );
				if ( is_array( $v ) ) {
					$process[$key][stripslashes( $k )] = $v;
					$process[] = &$process[$key][stripslashes( $k )];
				} else {
					$process[$key][stripslashes( $k )] = stripslashes( $v );
				}
			}
		}
		unset( $process );
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
