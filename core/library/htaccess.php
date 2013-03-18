<?php
namespace Leeflets\Library;

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

		$this->filesystem->connect();

		ob_start();
		include $this->config->include_path . '/htaccess.php';
		$out = ob_get_clean();

		$filepath = $this->config->admin_path . '/.htaccess';
		$filepath = $this->filesystem->translate_path( $filepath );
		
		return $this->filesystem->put_contents( $filepath, $out );
	}

}
