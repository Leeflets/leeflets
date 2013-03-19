<?php
namespace Leeflets;

class File {
	static function get_class_file_path( $config, $class ) {
		if ( preg_match( '/^Leeflets\\\Controller\\\(.*)/', $class, $matches ) ) {
			$path = $config->controller_path;
			$file = $matches[1];
		}
		elseif ( 
			preg_match( '/^Leeflets\\\External\\\(.*)/', $class, $matches )
			&& $_file = self::third_party_file( $matches[1] )
		) {
			$path = $config->third_party_path;
			$file = $_file;
		}
		elseif ( preg_match( '/^Leeflets\\\(.*)/', $class, $matches ) ) {
			$path = $config->library_path;
			$file = $matches[1];

			$pos = strrpos( $matches[1],  '\\' );
			if ( false !== $pos ) {
				$namespace = substr( $matches[1], 0, $pos );
				$path = $path . '/' . String::decamelize( $namespace );
				$file = substr( $matches[1], $pos + 1 );
			}
		}

		if ( !isset( $path ) ) return false;

		return $path . '/' . String::decamelize( $file ) . '.php';
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

	/**
	 * Sanitizes a filename replacing whitespace with dashes
	 *
	 * Removes special characters that are illegal in filenames on certain
	 * operating systems and special characters requiring special escaping
	 * to manipulate at the command line. Replaces spaces and consecutive
	 * dashes with a single dash. Trim period, dash and underscore from beginning
	 * and end of filename.
	 *
	 * @since 0.1
	 *
	 * @param string  $filename The filename to be sanitized
	 * @return string The sanitized filename
	 */
	function sanitize_file_name( $filename ) {
		$filename_raw = $filename;
		$special_chars = array( "?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr( 0 ) );
		$filename = str_replace( $special_chars, '', $filename );
		$filename = preg_replace( '/[\s-]+/', '-', $filename );
		$filename = trim( $filename, '.-_' );

		return $filename;
	}

	/**
	 * Appends a trailing slash.
	 *
	 * Will remove trailing slash if it exists already before adding a trailing
	 * slash. This prevents double slashing a string or path.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @since 0.1
	 * @uses untrailingslashit() Unslashes string if it was slashed already.
	 *
	 * @param string  $string What to add the trailing slash to.
	 * @return string String with trailing slash added.
	 */
	function trailingslashit( $string ) {
		return self::untrailingslashit( $string ) . '/';
	}

	/**
	 * Removes trailing slash if it exists.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @since 0.1
	 *
	 * @param string  $string What to remove the trailing slash from.
	 * @return string String without the trailing slash.
	 */
	function untrailingslashit( $string ) {
		return rtrim( $string, '/' );
	}

	/**
	 * Test if a give filesystem path is absolute ('/foo/bar', 'c:\windows').
	 *
	 * @since 0.1
	 *
	 * @param string  $path File path
	 * @return bool True if path is absolute, false is not absolute.
	 */
	function path_is_absolute( $path ) {
		// this is definitive if true but fails if $path does not exist or contains a symbolic link
		if ( realpath( $path ) == $path )
			return true;

		if ( strlen( $path ) == 0 || $path[0] == '.' )
			return false;

		// windows allows absolute paths like this
		if ( preg_match( '#^[a-zA-Z]:\\\\#', $path ) )
			return true;

		// a path starting with / or \ is absolute; anything else is relative
		return $path[0] == '/' || $path[0] == '\\';
	}

	/**
	 * Join two filesystem paths together (e.g. 'give me $path relative to $base').
	 *
	 * If the $path is absolute, then it the full path is returned.
	 *
	 * @since 0.1
	 *
	 * @param string  $base
	 * @param string  $path
	 * @return string The path with the base or absolute path.
	 */
	function path_join( $base, $path ) {
		if ( self::path_is_absolute( $path ) )
			return $path;

		return rtrim( $base, '/' ) . '/' . ltrim( $path, '/' );
	}

}
