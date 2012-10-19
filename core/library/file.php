<?php
class LF_File {
	static function get_class_file_path( $config, $class ) {
		if ( preg_match('/^LF_Form(.*)/', $class, $matches ) ) {
			$path = $config->form_path;
			$file = ( isset( $matches[1] ) && $matches[1] ) ? trim( $matches[1], '_' ) : 'form';
		}
		elseif ( preg_match('/^LF_Controller_(.*)/', $class, $matches ) ) {
			$path = $config->controller_path;
			$file = $matches[1];
		}
		elseif ( preg_match('/^LF_(.*)/', $class, $matches ) ) {
			$path = $config->library_path;
			$file = $matches[1];
		}
		elseif ( $_file = self::third_party_file( $class ) ) {
			$path = $config->third_party_path;
			$file = $_file;
		}

		if ( !isset( $path ) ) return false;

		return $path . '/' . LF_String::decamelize( $file ) . '.php';
	}

	static function third_party_file( $class ) {
		$files = array(
			'PasswordHash' => 'phpass',
			'PclZip' => 'pclzip',
			'ftp_base' => 'ftp'
		);

		if ( isset( $files[$class] ) ) {
			return $files[$class];
		}

		return false;
	}

}