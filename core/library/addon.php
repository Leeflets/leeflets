<?php
class LF_Addon {
	private $config, $settings, $hook;

	function __construct( LF_Config $config, LF_Settings $settings, LF_Hook $hook ) {
		$this->config = $config;
		$this->settings = $settings;
		$this->hook = $hook;
	}

	function load_active() {
		if ( !isset( $this->settings->data['active_addons'] ) ) {
			return false;
		}

		foreach ( $this->settings->data['active_addons'] as $addon ) {
			include( $this->config->addons_path . '/' . $addon . '/' . $addon . '.php' );
		}
	}
}