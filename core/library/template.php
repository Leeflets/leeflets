<?php
class LF_Template {
	private $config, $filesystem, $router,
		$active_template, $content, $settings,
		$hook, $script, $style;

	function __construct(
		LF_Config $config, LF_Filesystem $filesystem, LF_Router $router, 
		LF_Settings $settings, LF_Hook $hook, LF_Template_Scripts $script, 
		LF_Template_Styles $style
	) {
		$this->config = $config;
		$this->filesystem = $filesystem;
		$this->router = $router;
		$this->settings = $settings;
		$this->hook = $hook;
		$this->script = $script;
		$this->style = $style;
		$this->active_template = $this->settings->data['template']['active'];

		$this->script->base_url = $this->style->base_url = $this->get_template_url();
	}

	function write() {
		$this->filesystem->connect();
		$output = $this->render( true );
		$file = $this->config->root_path . '/index.html';
		$file = $this->filesystem->translate_path( $file );
		return $this->filesystem->put_contents( $file, $output );
	}

	function render( $is_write = false ) {
		$this->include_code_file();

		if ( !$is_write ) {
			$url = $this->router->admin_url( '/core/theme/asset/js/frontend-edit.js' );
			$this->enqueue_script( 'lf-frontend-edit', $url, array( 'jquery' ) );
			$url = $this->router->admin_url( '/core/theme/asset/css/frontend-edit.css' );
			$this->enqueue_style( 'lf-frontend-edit', $url );
		}

		$this->content = $this->get_content_data();

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

	public function template_url( $url = '' ) {
		echo $this->get_template_url( $url );
	}

	public function get_template_url( $url = '' ) {
		return $this->router->admin_url( 'templates/' . $this->active_template . '/' . ltrim( $url, '/' ) );
	}

	public function uploads_url( $url = '' ) {
		echo $this->get_uploads_url( $url );
	}

	public function get_uploads_url( $url = '' ) {
		return $this->router->admin_url( 'uploads/' . ltrim( $url, '/' ) );
	}

	public function part( $file ) {
		echo $this->get_part( $file );
	}

	public function get_part( $file ) {
		ob_start();
		include $this->template_file_path( 'part-' . $file );
		return ob_get_clean();
	}

	public function setting() {
		echo $this->vget_setting( func_get_args() );
	}

	public function get_setting() {
		return $this->vget_setting( func_get_args() );
	}

	public function vget_setting( $keys ) {
		$settings = $this->settings->data;
		
		foreach ( $keys as $key ) {
			if ( !isset( $settings[$key] ) ) {
				return '';
			}

			$settings = $settings[$key];
		}

		return $settings;
	}

	public function content() {
		echo $this->vget_content( func_get_args() );
	}

	public function get_content() {
		return $this->vget_content( func_get_args() );
	}

	public function vget_content( $keys ) {
		$content = $this->content;
		
		foreach ( $keys as $key ) {
			if ( !isset( $content[$key] ) ) {
				return '';
			}

			$content = $content[$key];
		}

		return $content;
	}

	public function set_content_data( $values ) {
		$file = new LF_Data_File( $this->get_content_data_file_path(), $this->config );
		$file->write( $values, $this->filesystem );
	}

    function enqueue_script( $handle, $src, $deps = array(), $ver = false, $args = null ) {
        $this->script->add_enqueue( $handle, $src, $deps, $ver, $args );
    }

    function enqueue_style( $handle, $src, $deps = array(), $ver = false, $args = null ) {
        $this->style->add_enqueue( $handle, $src, $deps, $ver, $args );
    }

	public function include_code_file() {
		$file = $this->config->templates_path . '/' . $this->active_template . '/code.php';
		if ( !file_exists( $file ) ) {
			return false;
		}

		include $file;
		return true;
	}

	public function get_content_data() {
		$file = $this->get_content_data_file_path();
		if ( !file_exists( $file ) ) {
			$file = $this->config->templates_path . '/' . $this->active_template . '/sample.json.php';
		}

		if ( !file_exists( $file ) ) {
			return array();
		}

		$file = new LF_Data_File( $file, $this->config );

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

	function get_form( $fieldset_ids ) {
		$content_file = $this->template_file_path( 'meta-content' );
		if ( !$content_file ) {
			die( "Can't load meta-content.php from active template." );
		}

		include $content_file;

		if ( !isset( $content ) ) {
			die( "Can't load $content variable in the active template's meta-content.php." );
		}

		if ( $fieldset_ids ) {
			foreach ( $content as $id => $fieldset ) {
				if ( in_array( $id, $fieldset_ids ) ) continue;
				unset( $content[$id] );
			}
		}

		if ( !$content ) {
			die( "Cannot find those form fieldsets." );
		}

		$content['buttons'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'submit' => array(
					'type' => 'button',
					'class' => 'btn btn-primary',
					'button-type' => 'submit',
					'value' => 'Save Changes'
				)
			)
		);

		$url = '';
		foreach ( $fieldset_ids as $id ) {
			$url .= urlencode( $id ) . '/';
		}

		return new LF_Form( 'edit-content', array(
			'elements' => $content,
			'action' => $this->router->admin_url( '/content/edit/' . $url ),
			'data-upload-url' => $this->router->admin_url( '/content/upload/' )
		) );
	}
}