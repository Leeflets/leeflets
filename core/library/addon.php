<?php
namespace Leeflets;

class Addon {
	private $config, $settings, $hook, $admin_script, $admin_style,
		$template_script, $template_style, $filesystem;

	function __construct( 
		Config $config, Settings $settings, Hook $hook,
		Admin\Scripts $admin_script, Admin\Styles $admin_style,
		Template\Scripts $template_script, Template\Styles $template_style,
		Filesystem $filesystem
	) {
		$this->config = $config;
		$this->settings = $settings;
		$this->hook = $hook;
		$this->admin_script = $admin_script;
		$this->admin_style = $admin_style;
		$this->template_script = $template_script;
		$this->template_style = $template_style;
		$this->filesystem = $filesystem;
	}

	function load_active() {
		if ( !$this->settings->get( 'active_addons' ) ) {
			return false;
		}

		foreach ( $this->settings->get( 'active_addons' ) as $addon ) {
			include( $this->config->addons_path . '/' . $addon . '/' . $addon . '.php' );
		}
	}

	function toggle( $addons, $activate ) {
		if ( !is_array( $addons ) ) {
			$addons = array( $addons );
		}

		$active_addons = $this->settings->get( 'active_addons' );

		if ( !is_array( $active_addons ) ) {
			$active_addons = array();
		}

		$active_addons = array_flip( $active_addons );

		foreach ( $addons as $slug ) {
			if ( $activate ) {
				$active_addons[$slug] = 1;
			}
			else {
				unset( $active_addons[$slug] );
			}
		}

		$active_addons = array_keys( $active_addons );

		$settings = $this->settings->get_data();
		$settings['active_addons'] = $active_addons;
		
		if ( !$this->settings->write( $settings, $this->filesystem ) ) {
			return new Error( 'toggle_addon_fail', 'Saving settings failed. Could not toggle addon activation.' );
		}

		return true;
	}

	function activate( $addons ) {
		return $this->toggle( $addons, true );
	}

	function deactivate( $addons ) {
		return $this->toggle( $addons, false );
	}
}