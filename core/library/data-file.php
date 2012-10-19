<?php
class LF_Data_File {

	public $data, $path;

	function __construct() {
	}

	function load() {
		if ( !file_exists( $this->path ) ) return false;

		$data = file_get_contents( $this->path );
		$after_first_line = strpos( $data, "\n" ) + 1;
		$before_last_line = strrpos( $data, "\n" );
		$data = substr( $data, $after_first_line, $before_last_line - $after_first_line );
		echo $data;
	}

}