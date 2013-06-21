<?php
namespace Leeflets\Addon;

class Base {
	protected $config, $settings, $hook, $admin_script, $admin_style,
		$template_script, $template_style, $filesystem, $router;

	function __construct( $file_path ) {
		$this->basename = basename( $file_path );
		$this->slug = preg_replace( '@\.php$@', '', $this->basename );
	}

	function get_url( $url = '' ) {
		return $this->router->get_addon_url( $this->slug, $url );
	}

	function load_objects() {
		list( $this->config, $this->settings, $this->hook, $this->admin_script, $this->admin_style, $this->template_script, $this->template_style, $this->filesystem, $this->router ) = func_get_args();
	}

	function init() {}
}