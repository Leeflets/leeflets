<?php
namespace Leeflets\Template;

class Code {
	protected $is_publish, $config, $filesystem, $router,
		$content, $settings, $hook, $script, $style, $active_template;

	function __construct(
		$is_publish, \Leeflets\Config $config, \Leeflets\Filesystem $filesystem, \Leeflets\Router $router, 
		\Leeflets\Settings $settings, \Leeflets\Hook $hook, Scripts $script, 
		Styles $style, \Leeflets\Content $content
	) {
		$this->is_publish = $is_publish;
		$this->config = $config;
		$this->filesystem = $filesystem;
		$this->router = $router;
		$this->settings = $settings;
		$this->hook = $hook;
		$this->script = $script;
		$this->style = $style;
		$this->content = $content;

		$this->active_template = $this->settings->get( 'template', 'active' );

		$this->script->base_url = $this->style->base_url = $this->get_url();

		$this->setup_default_hooks();
		$this->setup_hooks();
	}

	function is_debug() {
		return $this->config;
	}

	// To be overriden by the template's class
	function setup_hooks() {}

	function setup_default_hooks() {
		if ( !$this->is_publish ) {
			$min = $this->config->debug ? '' : '.min';

			$url = $this->router->admin_url( '/core/theme/asset/js/frontend-edit' . $min . '.js' );
			$this->enqueue_script( 'lf-frontend-edit', $url, array( 'jquery' ) );

			if ( !$this->config->debug || !$this->settings->get( 'debug', 'disable-overlays' ) ) {
				$url = $this->router->admin_url( '/core/theme/asset/js/frontend-overlay' . $min . '.js' );
				$this->enqueue_script( 'lf-frontend-overlay', $url, array( 'jquery' ) );
				$url = $this->router->admin_url( '/core/theme/asset/css/frontend-overlay.css' );
				$this->enqueue_style( 'lf-frontend-overlay', $url );
			}
		}

		if ( trim( $this->settings->get( 'analytics', 'code' ) ) ) {
			$placement = $this->settings->get( 'analytics', 'placement' );
			$this->hook->add( $placement, array( $this, 'hook_insert_analytics' ) );
		}
	}

	public function hook_insert_analytics() {
		echo $this->settings->get( 'analytics', 'code' );
	}

	public function url( $url = '' ) {
		echo $this->get_url( $url );
	}

	public function get_url( $url = '' ) {
		return $this->router->get_template_url( $url );
	}

	public function file_path( $file ) {
		return $this->config->templates_path . '/' . $this->active_template . '/' . $file . '.php';
	}

	public function part( $file ) {
		return $this->file( 'part-' . $file );
	}

    function enqueue_script( $handle, $src, $deps = array(), $ver = false, $args = null ) {
        $this->script->add_enqueue( $handle, $src, $deps, $ver, $args );
    }

    function enqueue_style( $handle, $src, $deps = array(), $ver = false, $args = null ) {
        $this->style->add_enqueue( $handle, $src, $deps, $ver, $args );
    }
}