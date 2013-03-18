<?php
namespace Leeflets\Library\Template;

class LF_Template {
	private $config, $filesystem, $router,
		$active_template, $content, $settings,
		$hook, $script, $style;

	function __construct(
		LF_Config $config, LF_Filesystem $filesystem, LF_Router $router, 
		LF_Settings $settings, LF_Hook $hook, LF_Template_Scripts $script, 
		LF_Template_Styles $style, LF_Content $content
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

		// Strip out Leeflets data attributes
		$output = preg_replace( '@ data-lf-edit="(.*?)"@', '', $output );

		$file = $this->config->root_path . '/index.html';
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
				For details on this, please see <a href="">the post on our Google+ Community</a>.</div>
				' );
		}

		$code_path = $this->config->templates_path . '/' . $this->active_template . '/code.php';
		LF_Include::class_file( $code_path );

		$path = $this->config->templates_path . '/' . $this->active_template;
		if ( class_exists( 'LF_Template_Code', false ) ) {
			$template_class = 'LF_Template_Code';
		}
		else {
			$template_class = 'LF_Template_Base';
		}

		$template = new $template_class( $is_publish, $this->config, $this->filesystem, $this->router, $this->settings, $this->hook, $this->script, $this->style, $this->content );

		return LF_Include::content( $index_path, array(
			'template' => $template,
			'content' => new LF_Template_Content( $this->content->get_data() ),
			'upload' => new LF_Template_Upload( $this->router ),
			'image' => new LF_Template_Image( $this->router ),
			'router' => $this->router,
			'config' => $this->config,
			'settings' => $this->settings,
			'hook' => $this->hook
		) );
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
        
        foreach ( glob( dirname( $pattern ) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir ) {
            $files = array_merge( $files, $this->glob_recursive( $dir . '/' . basename( $pattern ), $flags ) );
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

		$vars = LF_Include::variables( $content_file, array( 'content' ) );

		if ( !isset( $vars['content'] ) ) {
			die( "Can't load $content variable in the active template's meta-content.php." );
		}

		return $vars['content'];
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
