<?php
class LF_Template {
	private $config, $filesystem, $router,
		$active_template, $content, $settings;

	function __construct( LF_Config $config, LF_Filesystem $filesystem, LF_Router $router ) {
		$this->config = $config;
		$this->filesystem = $filesystem;
		$this->router = $router;
		$this->active_template = 'words';
	}

	function render() {
		$content_file = $this->get_content_data_file();
		$this->content = $content_file->read();

		$settings_file = new LF_Data_File( 'settings', $this->config, $this->filesystem );
		$this->settings = $settings_file->read();

		$index_path = $this->template_file_path( 'index' );

		if ( !file_exists( $index_path ) ) {
			die( 'No index.php in the template.' );
			exit;
		}

		return $this->include_index();
	}

	private function include_index() {
		ob_start();
		include $this->template_file_path( 'index' );
		return ob_get_clean();
	}

	public function template_url( $url ) {
		echo $this->get_template_url( $url );
	}

	public function get_template_url( $url ) {
		return $this->router->admin_url() . 'templates/' . $this->active_template . '/' . ltrim( $url, '/' );
	}

	public function part( $file ) {
		echo $this->get_part( $file );
	}

	public function get_part( $file ) {
		ob_start();
		include $this->template_file_path( $file );
		return ob_get_clean();
	}

	public function setting( $key ) {
		echo $this->get_setting( $key );
	}

	public function get_setting( $key ) {
		if ( isset( $this->settings[$key] ) ) {
			return $this->settings[$key];
		}

		return '';
	}

	public function content( $key ) {
		echo $this->get_content( $key );
	}

	public function get_content( $key ) {
		if ( isset( $this->content[$key] ) ) {
			return $this->content[$key];
		}

		return '';
	}

	private function get_content_filename() {
		return 'content-' . $this->active_template;
	}

	public function get_content_data_file() {
		return new LF_Data_File( $this->get_content_filename(), $this->config, $this->filesystem );
	}

	private function template_file_path( $file ) {
		$path = $this->config->templates_path . '/' . $this->active_template . '/' . $file . '.php';
		if ( !file_exists( $path ) ) {
			return false;
		}
		return $path;
	}

	function get_form() {
		$content_file = $this->template_file_path( 'content' );
		if ( !$content_file ) {
			return false;
		}

		include $content_file;

		if ( !isset( $content ) ) {
			return false;
		}

		$content['buttons'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'submit' => array(
					'type' => 'button',
					'button-type' => 'submit',
					'value' => 'Submit'
				)
			)
		);

		return new LF_Form( 'edit-content', array( 'elements' => $content ) );
	}
}