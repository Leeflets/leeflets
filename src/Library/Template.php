<?php

namespace Leeflets\Core\Library;

class Template {
	private $config, $filesystem, $router,
		$active_template, $content, $settings,
		$hook, $script, $style;

	function __construct(
		Config $config, Filesystem $filesystem, Router $router, 
		Settings $settings, Hook $hook, Template\Scripts $script, 
		Template\Styles $style, Content $content
	) {
		$this->config = $config;
		$this->filesystem = $filesystem;
		$this->router = $router;
		$this->settings = $settings;
		$this->hook = $hook;
		$this->script = $script;
		$this->style = $style;
		$this->content = $content;
		$this->active_template = $this->settings->get( 'template', 'active' );
	}

	function write() {
		$this->filesystem->connect();
		$output = $this->render( true );

		$output = $this->hook->apply( 'template_write_output', $output );

		// Strip out Leeflets data attributes
		$output = preg_replace( '@ data-lf-edit="(.*?)"@', '', $output );

		$filename = $this->hook->apply( 'template_write_filename', 'index.html' );
		$filepath = $this->hook->apply( 'template_write_filepath', $this->config->root_path );
		
		$file = $filepath . '/' . $filename;
		$file = $this->filesystem->translate_path( $file );
		return $this->filesystem->put_contents( $file, $output );
	}

	function render( $is_publish = false ) {
		$index_path = $this->template_file_path( 'index' );

		if ( !file_exists( $index_path ) ) {
			die( 'No index.php in the template.' );
			exit;
		}

		// Temporary notification of API change
		// We'll remove this before beta launch
		if ( $files = $this->old_api_detected() ) {
			die( '<div style="margin: 50px 100px; font-family: sans-serif; line-height: 1.4em;">
				Ooops, looks like you haven\'t converted your template to our new API.<br />
				We\'ve detected the following files making use of <code>$this-></code> which is now deprecated:<br /><ul>' . $files . '</ul>
				For details on this, please see <a href="https://plus.google.com/u/0/103798016457548612622/posts/BoMn41W1q6L" target="_blank">the post on our Google+ Community</a>.</div>
				' );
		}

		$code_path = $this->config->templates_path . '/' . $this->active_template . '/code.php';
		Inc::class_file( $code_path );

		$path = $this->config->templates_path . '/' . $this->active_template;
		if ( class_exists( '\User\Template\Code', false ) ) {
			$template_class = '\User\Template\Code';
		}
		else {
			$template_class = '\Leeflets\Template\Code';
		}

		$template = new $template_class( $is_publish, $this->config, $this->filesystem, $this->router, $this->settings, $this->hook, $this->script, $this->style, $this->content );

		$template_objects = array(
			'template' => $template,
			'content' => new Template\Content( $this->content->get_data() ),
			'upload' => new Template\Upload( $this->router ),
			'image' => new Template\Image( $this->router ),
			'settings' => new Template\Settings( $this->settings ),
			'hook' => $this->hook
		);

		$template_objects = $this->hook->apply( 'template_render_objects', $template_objects );

		return Inc::content( $index_path, $template_objects );
	}

	private function old_api_detected() {
		$php_files = $this->glob_recursive( $this->config->templates_path . '/' . $this->active_template . '/*.php' );
		$files = '';
		
		foreach ( $php_files as $file_path ) {
			if ( 'code.php' == basename( $file_path ) ) {
				continue;
			}

			$content = file_get_contents( $file_path );
			if ( preg_match( '@' . preg_quote( '$this->' ) . '@', $content ) ) {
				$files .= '<li>' . str_replace( $this->config->templates_path . '/', '', $file_path ) . '</li>';
			}
		}

		if ( empty( $files ) ) {
			return false;
		}

		return $files;
	}

	private function glob_recursive( $pattern, $flags = 0 ) {
        $files = glob( $pattern, $flags );

        // Need to be careful with the return value of glob()
        // There's a bug that returns false instead of empty array: https://bugs.php.net/bug.php?id=53460
        $dirs = glob( dirname( $pattern ) . '/*', GLOB_ONLYDIR|GLOB_NOSORT);
        if ( is_array( $dirs ) ) {
	        foreach ( $dirs as $dir ) {
	            $files = array_merge( $files, $this->glob_recursive( $dir . '/' . basename( $pattern ), $flags ) );
	        }
	    }
        
        return $files;
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
			die( "Active template's meta-content.php not found." );
		}

		$vars = Inc::variables( $content_file, array( 'content' ) );

		if ( !isset( $vars['content'] ) ) {
			die( "Can't load $content variable in the active template's meta-content.php." );
		}

		return $this->hook->apply( 'template_get_content_fields', $vars['content'] );
	}

	/**
	 * The form fields for this template and optionally only include
	 * certain fieldsets
	 *
	 * @param array $fieldset_ids Reduce the form down to only these fieldsets
	 * @return Form
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

		// Need this button hidden with CSS so that 
		// pressing 'Enter' in a textbox submits the form
		// Could use JS to detect the 'Enter' key, but apparently there's issues 
		// with autocomplete and form fillers
		$fields['leeflets-hidden-submit-button'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'submit' => array(
					'type' => 'button',
					'button-type' => 'submit',
					'value' => 'Save Changes',
					'tabindex' => '-1'
				)
			)
		);

		return new Form( $this->config, $this->router, $this->settings, 'edit-content', array(
			'elements' => $fields,
			'action' => $this->router->adminUrl( '/content/edit/' . $url )
		) );
	}
}
