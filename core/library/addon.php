<?php
namespace Leeflets;

class Addon {
	private $config, $settings, $hook, $admin_script, $admin_style,
		$template_script, $template_style, $filesystem, $router, $instances;

	function __construct( 
		Config $config, Settings $settings, Hook $hook,
		Admin\Scripts $admin_script, Admin\Styles $admin_style,
		Template\Scripts $template_script, Template\Styles $template_style,
		Filesystem $filesystem, Router $router
	) {
		$this->config = $config;
		$this->settings = $settings;
		$this->hook = $hook;
		$this->admin_script = $admin_script;
		$this->admin_style = $admin_style;
		$this->template_script = $template_script;
		$this->template_style = $template_style;
		$this->filesystem = $filesystem;
		$this->router = $router;
	}

	function load_active() {
		$active_addons = $this->settings->get( 'active_addons' );
		
		if ( !$active_addons ) {
			return false;
		}

		$deactivate = array();

		foreach ( $active_addons as $addon ) {
			$path = $this->config->addons_path . '/' . $addon . '/' . $addon . '.php';
			if ( !file_exists( $path ) ) {
				$deactivate[] = $addon;
			}
			else {
				Inc::class_file( $path );
				$class_name = \Leeflets\String::camelize( $addon );
				$class_name = '\Leeflets\User\Addon\\' . $class_name;
				$this->instances[$addon] = $obj = new $class_name();
				$obj->load_objects( $this->config, $this->settings, $this->hook, $this->admin_script, $this->admin_style, $this->template_script, $this->template_style, $this->filesystem, $this->router );
				$obj->init();
			}
		}

		if ( $deactivate ) {
			$this->deactivate( $deactivate );
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

	function get_instance( $slug ) {
		if ( !empty( $this->instances[$slug] ) ) {
			return $this->instances[$slug];
		}
		return false;
	}

	function activate( $addons ) {
		return $this->toggle( $addons, true );
	}

	function deactivate( $addons ) {
		return $this->toggle( $addons, false );
	}
}