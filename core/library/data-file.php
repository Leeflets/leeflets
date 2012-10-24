<?php
class LF_Data_File {

	public $filepath, $config, $filesystem;

	function __construct( $filepath, LF_Config $config, LF_Filesystem $filesystem ) {
		$this->config = $config;
		$this->filesystem = $filesystem;

		$this->filepath = $filepath;
	}

	function write( $data ) {
		$out = "<?php exit; // No public access. ?>\n";
		$out .= LF_String::json_prettify( json_encode( $data ) );
		return $this->filesystem->put_contents( $this->filepath, $out );
	}

	function read() {
		if ( !$this->filesystem->exists( $this->filepath ) ) {
			return false;
		}

		$json = $this->filesystem->get_contents( $this->filepath );
		$after_first_line = strpos( $json, "\n" ) + 1;
		$json = substr( $json, $after_first_line );

		$data = json_decode( $json, true );

		if ( is_null( $data ) ) {
			return false;
		}

		return $data;
	}

}