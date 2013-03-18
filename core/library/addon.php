<?php
namespace Leeflets;

class Addon {
	private $config, $settings, $hook, $admin_script, $admin_style,
		$template_script, $template_style;

	function __construct( 
		LF_Config $config, LF_Settings $settings, LF_Hook $hook,
		LF_Admin_Scripts $admin_script, LF_Admin_Styles $admin_style,
		LF_Template_Scripts $template_script, LF_Template_Styles $template_style
	) {
		$this->config = $config;
		$this->settings = $settings;
		$this->hook = $hook;
		$this->admin_script = $admin_script;
		$this->admin_style = $admin_style;
		$this->template_script = $template_script;
		$this->template_style = $template_style;
	}

	function load_active() {
		if ( !$this->settings->get( 'active_addons' ) ) {
			return false;
		}

		foreach ( $this->settings->get( 'active_addons' ) as $addon ) {
			include( $this->config->addons_path . '/' . $addon . '/' . $addon . '.php' );
		}
	}
}