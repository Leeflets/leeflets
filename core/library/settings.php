<?php
namespace Leeflets;

class Settings extends Data_File {
	private $data;
	
	function __construct( Config $config ) {
		$this->config = $config;

		$this->filepath = $this->config->data_path . '/settings.json.php';

		$this->load();
	}

	function load() {
		$this->data = $this->read();
		if ( !$this->data ) {
			$this->data = array();
		}

		// Defaults
		if ( !isset( $this->data['template']['active'] ) ) {
			$this->data['template']['active'] = 'koala';
		}

		if ( !isset( $this->data['analytics']['placement'] ) ) {
			$this->data['analytics']['placement'] = 'head';
		}
	}

	function get_data() {
		return $this->data;
	}

	function out() {
		echo $this->vget( func_get_args() );
	}

	function get() {
		return $this->vget( func_get_args() );
	}

	function vget( $keys ) {
		$settings = $this->data;
		
		foreach ( $keys as $key ) {
			if ( !isset( $settings[$key] ) ) {
				return '';
			}

			$settings = $settings[$key];
		}

		return $settings;
	}

	function save_connection_info( $data, $filesystem ) {
		$settings = $this->read();

		$fields = array( 'type', 'hostname', 'username', 'password' );
		foreach ( $fields as $field ) {
			$settings['connection'][$field] = $data['connection'][$field];
		}

		$this->write( $settings, $filesystem );
	}

	function get_template_about( $template = '' ) {
		if ( !$template ) {
			$template = $this->get( 'template', 'active' );
		}

		$path = $this->config->templates_path . '/' . $template;
		return $this->get_product_about( $path );
	}

	function get_addon_about( $addon ) {
		$path = $this->config->addons_path . '/' . $addon;
		return $this->get_product_about( $path );
	}

	function get_product_about( $path = '' ) {

		$path .= '/meta-about.php';

		if ( !file_exists( $path ) ) {
			return false;
		}

		$variables = \Leeflets\Inc::variables( $path, array( 'about' ) );
		if ( is_array( $variables ) ) {
			extract( $variables );
		}

		if ( !isset( $about['name'] ) || !isset( $about['version'] ) ) {
			return false;
		}

		$default_about = array(
			'name' => '',
			'version' => '',
			'description' => '',
			'screenshot' => '',
			'author' => array(
				'name' => '',
				'url' => ''
			),
			'changelog' => array()
		);

		// Add default array keys to avoid having to check if
		// indexes exist and array index errors
		$about = array_merge( $default_about, $about );

		return $about;
	}
}
