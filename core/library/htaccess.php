<?php
class LF_Htaccess {
	private $filesystem, $router, $config;

	function __construct( $filesystem, LF_Router $router, LF_Config $config ) {
		$this->filesystem = $filesystem;
		$this->router = $router;
		$this->config = $config;
	}
	
	function write() {
		$rewrite_base = parse_url( $this->router->admin_url() );
		$rewrite_base = $rewrite_base['path'];

		ob_start();
		include $this->config->include_path . '/htaccess.php';
		$out = ob_get_clean();

		return $this->filesystem->put_contents( $this->config->admin_path . '/.htaccess', $out );
	}

}
