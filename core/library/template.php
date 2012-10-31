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

	function write() {
		$output = $this->render();
		$file = $this->config->root_path . '/index.html';
		return $this->filesystem->put_contents( $file, $output );
	}

	function render() {
		$this->content = $this->get_content_data();

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
		include $this->template_file_path( 'part-' . $file );
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

	public function set_content_data( $values ) {
		$file = new LF_Data_File( $this->get_content_data_file_path(), $this->config, $this->filesystem );
		$file->write( $values );
	}

	public function get_content_data() {
		$file = $this->get_content_data_file_path();
		if ( !file_exists( $file ) ) {
			$file = $this->config->templates_path . '/' . $this->active_template . '/sample.json.php';
		}

		if ( !file_exists( $file ) ) {
			return array();
		}

		$file = new LF_Data_File( $file, $this->config, $this->filesystem );

		return $file->read();
	}

	private function get_content_data_file_path() {
		return $this->config->data_path . '/content-' . $this->active_template . '.json.php';
	}

	private function template_file_path( $file ) {
		$path = $this->config->templates_path . '/' . $this->active_template . '/' . $file . '.php';
		if ( !file_exists( $path ) ) {
			return false;
		}
		return $path;
	}

	function get_form() {
		$content_file = $this->template_file_path( 'meta-content' );
		if ( !$content_file ) {
			die( "Can't load meta-content.php from active template." );
		}

		include $content_file;

		if ( !isset( $content ) ) {
			die( "Can't load $content variable in the active template's meta-content.php." );
		}

		$content['buttons'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'submit' => array(
					'type' => 'button',
					'button-type' => 'submit',
					'value' => 'Save'
				)
			)
		);

		return new LF_Form( 'edit-content', array( 'elements' => $content ) );
	}
}