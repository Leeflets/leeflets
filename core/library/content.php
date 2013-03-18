<?php
class LF_Content {
	private $config, $filesystem, $router,
		$content, $settings, $hook;

	function __construct(
		LF_Config $config, LF_Filesystem $filesystem, LF_Router $router, 
		LF_Settings $settings, LF_Hook $hook
	) {
		$this->config = $config;
		$this->filesystem = $filesystem;
		$this->router = $router;
		$this->settings = $settings;
		$this->hook = $hook;
	}

	public function set_data( $values ) {
		if ( !$values ) {
			return false;
		}

		$file = new LF_Data_File( $this->get_data_file_path(), $this->config );
		$file->write( $values, $this->filesystem );
	}

	public function get_data( $force_read_file = false ) {
		if ( $this->content && !$force_read_file ) {
			return $this->content;
		}

		$file = $this->get_data_file_path();
		if ( !file_exists( $file ) ) {
			$file = $this->config->templates_path . '/' . $this->settings->get( 'template', 'active' ) . '/sample.json.php';
		}

		if ( !file_exists( $file ) ) {
			return array();
		}

		$file = new LF_Data_File( $file, $this->config );

		$this->content = $file->read();

		return $this->content;
	}

	private function get_data_file_path() {
		return $this->config->data_path . '/content-' . $this->settings->get( 'template', 'active' ) . '.json.php';
	}

}