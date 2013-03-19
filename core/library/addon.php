<?php
namespace Leeflets;

class Addon {
	private $config, $settings, $hook, $admin_script, $admin_style,
		$template_script, $template_style;

	function __construct( 
		Config $config, Settings $settings, Hook $hook,
		Admin\Scripts $admin_script, Admin\Styles $admin_style,
		Template\Scripts $template_script, Template\Styles $template_style
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