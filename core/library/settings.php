<?php
namespace Leeflets\Library;

class Settings extends LF_Data_File {
	private $data;
	
	function __construct( LF_Config $config ) {
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
}
