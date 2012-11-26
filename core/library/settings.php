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
	}

	function save_connection_info( $data, $filesystem ) {
		$settings = $this->read();

		$fields = array( 'hostname', 'username', 'password', 'type' );
		foreach ( $fields as $field ) {
			$settings['connection-' . $field] = $data['connection-' . $field];
		}

		$this->write( $settings, $filesystem );
	}
}
