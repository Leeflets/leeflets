<?php
class LF_Settings extends LF_Data_File {
	public $data;
	
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

		if ( !isset( $this->data['template']['active'] ) ) {
			$this->data['template']['active'] = 'koala';
		}
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
