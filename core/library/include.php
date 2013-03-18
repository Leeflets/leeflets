<?php
namespace Leeflets\Library;

/**
 * These static functions are used to capture content, classes, and variables
 * from included files while restricting their impact on the rest of the app.
 */
class Include {
	// Include a file and keep it's scope contained
	static function content( $path, $vars = array() ) {
		extract( $vars );
		ob_start();
		include $path;
		$content = ob_get_clean();
		return $content;
	}

	static function variables( $path, $filter, $vars = array() ) {
		extract( $vars );
		include $path;
		$new = get_defined_vars();
		$filter = array_fill_keys( $filter, 1 );
		$new = array_intersect_key( $new, $filter );
		return $new;
	}

	static function class_file( $path ) {
		if ( !file_exists( $path ) ) {
			return false;
		}

		include $path;
		return true;
	}
}
