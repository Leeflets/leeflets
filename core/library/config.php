<?php
namespace Leeflets;

class Config {
	public $path, $username, $password;
	public $debug, $debug_display, $debug_log;
	public $tmp_path, $fs_chmod_dir, $fs_chmod_file;
	public $admin_path, $root_path, $core_path, $controller_path, $library_path, $view_path, $theme_path, $form_path;
	public $is_loaded = false;

	function __construct( $admin_path ) {
		$this->admin_path = $admin_path;
		$this->root_path = realpath( $this->admin_path . '/../' );
		$this->core_path = $this->admin_path . '/core';
		$this->controller_path = $this->core_path . '/controller';
		$this->library_path = $this->core_path . '/library';
		$this->view_path = $this->core_path . '/view';
		$this->theme_path = $this->core_path . '/theme';
		$this->form_path = $this->core_path . '/form';
		$this->third_party_path = $this->core_path . '/third-party';
		$this->include_path = $this->core_path . '/include';

		$this->templates_path = $this->admin_path . '/templates';
		$this->addons_path = $this->admin_path . '/addons';
		$this->data_path = $this->admin_path . '/data';
		$this->uploads_path = $this->admin_path . '/uploads';

		$this->path = $this->admin_path . '/config.php';

		$this->debug = false;
		$this->debug_display = false;
		$this->debug_log = false;
		$this->fs_chmod_dir = 0755;
		$this->fs_chmod_file = 0644;
	}

	function load() {
		if ( !file_exists( $this->path ) ) return false;

		$required = array( 'username', 'password' );
		$optional = array( 'debug', 'debug_display', 'debug_log', 'fs_chmod_dir', 'fs_chmod_file' );
		$both = array_merge( $required, $optional );
		
		$vars = Inc::variables( $this->path, $both );

		foreach ( $required as $var ) {
			if ( !isset( $vars[$var] ) ) die( 'Missing ' . $var . ' from config.php.' );
		}

		foreach ( $both as $var ) {
			if ( !isset( $vars[$var] ) ) continue;
			$this->$var = $vars[$var];
		}

		$this->is_loaded = true;

		return true;
	}

	function write( $filesystem, $data ) {
		$filesystem->connect();
		$path = $filesystem->translate_path( $this->path );

		if ( $filesystem->exists( $path ) ) return false;

		$out = "<?php\n";

		foreach ( $data as $key => $value ) {
			$out .= "\$" . $key . " = '" . addslashes( $value ) . "';\n";
		}

		return $filesystem->put_contents( $path, $out );
	}

}
