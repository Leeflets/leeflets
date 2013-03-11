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
		$this->active_template = $this->settings->get ( 'template', 'active' );

		$this->script->base_url = $this->style->base_url = $this->get_template_url();

		$this->setup_default_hooks();
	}

	function setup_default_hooks() {
		if ( trim( $this->settings->get( 'analytics', 'code' ) ) ) {
			$placement = $this->settings->get( 'analytics', 'placement' );
			$this->hook->add( $placement, array( $this, 'insert_analytics' ) );
		}
	}

	function insert_analytics() {
		echo $this->settings->get( 'analytics', 'code' );
	}

	function write() {
		$this->filesystem->connect();
		$output = $this->render( true );

		// Strip out Leeflets data attributes
		$output = preg_replace( '@ data-lf-edit="(.*?)"@', '', $output );

		$file = $this->config->root_path . '/index.html';
		$file = $this->filesystem->translate_path( $file );
		return $this->filesystem->put_contents( $file, $output );
	}

	function render( $is_write = false ) {
		$this->include_code_file();

		if ( !$is_write ) {
			$url = $this->router->admin_url( '/core/theme/asset/js/frontend-edit.js' );
			$this->enqueue_script( 'lf-frontend-edit', $url, array( 'jquery' ) );

			if ( !$this->config->debug || !$this->settings->get( 'debug', 'disable-overlays' ) ) {
				$url = $this->router->admin_url( '/core/theme/asset/js/frontend-overlay.js' );
				$this->enqueue_script( 'lf-frontend-overlay', $url, array( 'jquery' ) );
				$url = $this->router->admin_url( '/core/theme/asset/css/frontend-overlay.css' );
				$this->enqueue_style( 'lf-frontend-overlay', $url );
			}
		}

		$this->get_content_data();

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
		return $this->router->get_template_url( $this->active_template, $url );
	}

	public function uploads_url( $url = '' ) {
		echo $this->get_uploads_url( $url );
	}

	public function get_uploads_url( $url = '' ) {
		return $this->router->get_uploads_url( $url );
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
		echo $this->settings->vget( func_get_args() );
	}

	public function get_setting() {
		return $this->settings->vget( func_get_args() );
	}

	public function content() {
		echo $this->vget_content( func_get_args() );
	}

	public function get_content() {
		return $this->vget_content( func_get_args() );
	}

	public function vget_content( $keys ) {
		$content = $this->get_content_data();
		
		foreach ( $keys as $key ) {
			if ( !isset( $content[$key] ) ) {
				return '';
			}

			$content = $content[$key];
		}

		return $content;
	}

	public function get_image_atts() {
		return $this->vget_image_atts( func_get_args() );
	}

	public function vget_image_atts( $args ) {
		$version = array_shift( $args );

		switch ( count( $args ) ) {
			case 0:
				return false;
			case 1:
				if ( !is_array( $args[0] ) ) {
					return false;
				}
				$image = $args[0];
				break;
			default:
				$image = $this->vget_content( $args );
		}
		
		if ( isset( $image['versions'][$version] ) ) {
			$image = $image['versions'][$version];
		}

		if ( !isset( $image['path'] ) || !isset( $image['width'] ) || !isset( $image['height'] ) ) {
			return false;
		}

		return array( $this->get_uploads_url( $image['path'] ), $image['width'], $image['height'] );
	}

	public function image() {
		$tag = $this->vget_image( func_get_args() );
		if ( $tag ) {
			echo $tag;
		}
	}

	public function get_image() {
		return $this->vget_image( func_get_args() );
	}

	public function vget_image( $args ) {
		$atts = $this->vget_image_atts( $args );
		if ( !$atts ) {
			return false;
		}

		list( $src, $w, $h ) = $atts;
		return sprintf( '<img src="%s" width="%s" height="%s" alt="" />', $src, $w, $h );
	}

	public function set_content_data( $values ) {
		if ( !$values ) {
			return false;
		}

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

	public function get_content_data( $force_read_file = false ) {
		if ( $this->content && !$force_read_file ) {
			return $this->content;
		}

		$file = $this->get_content_data_file_path();
		if ( !file_exists( $file ) ) {
			$file = $this->config->templates_path . '/' . $this->active_template . '/sample.json.php';
		}

		if ( !file_exists( $file ) ) {
			return array();
		}

		$file = new LF_Data_File( $file, $this->config );

		$this->content = $file->read();

		return $this->content;
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

	function get_content_fields() {
		$content_file = $this->template_file_path( 'meta-content' );
		if ( !$content_file ) {
			die( "Can't load meta-content.php from active template." );
		}

		include $content_file;

		if ( !isset( $content ) ) {
			die( "Can't load $content variable in the active template's meta-content.php." );
		}

		return $content;
	}

	/**
	 * The form fields for this template and optionally only include
	 * certain fieldsets
	 *
	 * @param array $fieldset_ids Reduce the form down to only these fieldsets
	 * @return array|bool the file contents in an array or false on failure.
	 */
	function get_form( $fieldset_ids = array() ) {
		$fields = $this->get_content_fields();

		if ( $fieldset_ids ) {
			foreach ( $fields as $id => $fieldset ) {
				if ( in_array( $id, $fieldset_ids ) ) continue;
				unset( $fields[$id] );
			}
		}

		if ( !$fields ) {
			die( "Cannot find those form fieldsets." );
		}

		$url = '';
		foreach ( $fieldset_ids as $id ) {
			$url .= urlencode( $id ) . '/';
		}

		return new LF_Form( $this->config, $this->router, $this->settings, 'edit-content', array(
			'elements' => $fields,
			'action' => $this->router->admin_url( '/content/edit/' . $url )
		) );
	}
}
