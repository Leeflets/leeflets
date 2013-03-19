<?php
namespace Leeflets;

class User {
	public $config, $router;

	function __construct( Config $config, Router $router ) {
		$this->config = $config;
		$this->router = $router;
	}

	function get_cookie_name() {
		return 'leeflets_' . md5( $this->router->admin_url() );
	}

	function get_cookie_value( $expiration ) {
		$pass_frag = substr( $this->config->password, 8, 4 );
		$hasher = new \Leeflets\External\PasswordHash( 8, false );
		$hash = $hasher->HashPassword( $this->config->username . '|' . $expiration . '|' . $pass_frag );
		return $this->config->username . '|' . $expiration . '|' . $hash;
	}

	function get_cookie_path() {
		return preg_replace( '|https?://[^/]+|i', '', $this->router->admin_url() );
	}

	function set_cookie( $remember = false ) {
		$path = $this->get_cookie_path();

		if ( $remember ) {
			$expiration = 1209600;
		}
		else {
			$expiration = 172800;
		}

		$expiration = time() + $expiration;
		$value = $this->get_cookie_value( $expiration );

		setcookie( $this->get_cookie_name(), $value, $expiration, $path, null, false, true );
	}

	function clear_cookie() {
		setcookie( $this->get_cookie_name(), ' ', time() - 31536000, $this->get_cookie_path() );
	}

	function validate_cookie( $cookie = null ) {
		$cookie_name = $this->get_cookie_name();
		if ( is_null( $cookie ) ) {
			if ( isset( $_COOKIE[$cookie_name] ) ) {
				$cookie = $_COOKIE[$cookie_name];
			}
			else {
				return false;
			}
		}

		$cookie_parts = explode( '|', $cookie );
		if ( count( $cookie_parts ) != 3 ) {
			return false;
		}

		list( $username, $expiration, $hash ) = $cookie_parts;

		if ( $expiration < time() ) {
			return false;
		}

		$pass_frag = substr( $this->config->password, 8, 4 );
		$value = $username . '|' . $expiration . '|' . $pass_frag;

		$hasher = new \Leeflets\External\PasswordHash( 8, false );
		return $hasher->CheckPassword( $value, $hash );
	}

	function is_logged_in() {
		return $this->validate_cookie();
	}
}
