<?php
/**
 * Base Leeflets Filesystem (borrowed from WordPress)
 *
 * @package Leeflets
 * @subpackage Filesystem
 */

/**
 * Base Leeflets Filesystem class for which Filesystem implementations extend
 *
 * @since 0.1
 */
class LF_Filesystem {
	/**
	 * Whether to display debug data for the connection.
	 *
	 * @since 0.1
	 * @access public
	 * @public bool
	 */
	public $verbose = false;
	/**
	 * Cached list of local filepaths to mapped remote filepaths.
	 *
	 * @since 0.1
	 * @access private
	 * @public array
	 */
	public $cache = array();

	/**
	 * The Access method of the current connection, Set automatically.
	 *
	 * @since 0.1
	 * @access public
	 * @public string
	 */
	public $method = '';

	protected $config;

	private $have_direct_access;

	function have_direct_access() {
		if ( !is_null( $this->have_direct_access ) ) {
			return $this->have_direct_access;
		}

		$this->have_direct_access = false;

		if ( !function_exists( 'getmyuid' ) || !function_exists( 'fileowner' ) ) {
			return $this->have_direct_access;
		}

		$temp_file_name = $this->config->root_path . '/temp-write-test-' . time();
		$temp_handle = @fopen( $temp_file_name, 'w' );
		if ( $temp_handle ) {
			if ( getmyuid() == @fileowner( $temp_file_name ) ) {
				$this->have_direct_access = true;
			}
			@fclose( $temp_handle );
			@unlink( $temp_file_name );
		}

		return $this->have_direct_access;
	}

	function get_supported_methods() {
		$methods = array();

		if ( $this->have_direct_access() ) {
			$methods['direct'] = 'Direct';
		}

		if ( extension_loaded( 'ftp' ) || extension_loaded( 'sockets' ) || function_exists( 'fsockopen' ) ) {
			$methods[ 'ftp' ] = 'FTP';
		}

		if ( extension_loaded( 'ftp' ) ) { //Only this supports FTPS
			$methods[ 'ftps' ] = 'FTPS (SSL)';
		}

		/* Haven't tested this sucka yet.
		if ( extension_loaded( 'ssh2' ) && function_exists( 'stream_get_contents' ) ) {
			$methods[ 'ssh' ] = 'SSH2';
		}
		*/

		return $methods;
	}

	function get_class_name( $method ) {
		$name = '';
		switch ( $method ) {
		case 'direct':
			$name = 'direct';
			break;
		case 'ssh':
			$name = 'ssh2';
			break;
		case 'ftps':
			$name = 'ftpext';
			break;
		case 'ftp':
			if ( extension_loaded( 'ftp' ) ) {
				$name = 'ftpext';
			}
			else {
				$name = 'ftpsockets';
			}
			break;
		}

		if ( $name ) {
			return 'LF_Filesystem_' . ucfirst( $name );
		}

		return false;
	}

	function get_connection_fields( $connection_callback, $required ) {
		$methods = $this->get_supported_methods();
		$label = 'FTP';
		$ssh_support = false;

		if ( isset( $methods['ssh'] ) ) {
			$label = '/SSH';
			$ssh_support = true;
		}

		if ( isset ( $methods['direct'] ) ) {
			$default_method = 'direct';
		}
		else {
			$default_method = 'ftp';
		}

		return array(
			'type' => 'fieldset',
			'elements' => array(
				'type' => array(
					'required' => true,
					'type' => 'select',
					'options' => $methods,
					'value' => $default_method,
					'validation' => array(
						array(
							'callback' => $connection_callback,
							'msg' => 'Sorry, the connection to the server failed. 
								Please verify the connection details and try again.'
						)
					)
				),
				'port' => array(
					'required' => $required,
					'type' => 'text',
					'label' => 'Port:',
					'placeholder' => 'Default',
					'class' => 'input-mini'
				),
				'hostname' => array(
					'required' => $required,
					'type' => 'text',
					'placeholder' => 'Hostname',
					'class' => 'input-block-level'
				),
				'username' => array(
					'required' => $required,
					'type' => 'text',
					'placeholder' => 'Username',
					'class' => 'input-block-level'
				),
				'password' => array(
					'required' => $required,
					'type' => 'password',
					'placeholder' => 'Password',
					'class' => 'input-block-level'
				)
			)
		);
	}

	/**
	 * Inspired by WordPress
	 * Determines a writable directory for temporary files.
	 *
	 * In the event that this function does not find a writable location, It may be overridden by the <code>WP_TEMP_DIR</code> constant in your <code>wp-config.php</code> file.
	 *
	 * @since 0.1
	 *
	 * @return string Writable temporary directory
	 */
	function get_tmp_path() {
		if ( !is_null( $this->config->tmp_path ) ) {
			return rtrim( $this->config->tmp_path, '/' ) . '/';
		}

		if  ( function_exists( 'sys_get_temp_dir' ) ) {
			$temp = sys_get_temp_dir();
			if ( @is_writable( $temp ) ) {
				return rtrim( $temp, '/' ) . '/';
			}
		}

		$temp = ini_get( 'upload_tmp_dir' );
		if ( is_dir( $temp ) && @is_writable( $temp ) ) {
			return rtrim( $temp, '/' ) . '/';
		}

		return '/tmp/';
	}

	/**
	 * Returns a filename of a Temporary unique file.
	 * Please note that the calling function must unlink() this itself.
	 *
	 * The filename is based off the passed parameter or defaults to the current unix timestamp,
	 * while the directory can either be passed as well, or by leaving it blank, default to a writable temporary directory.
	 *
	 * @since 0.1
	 *
	 * @param string  $filename (optional) Filename to base the Unique file off
	 * @param string  $dir      (optional) Directory to store the file in
	 * @return string a writable filename
	 */
	function create_tmp_file( $filename = '', $dir = '' ) {
		if ( !$dir ) {
			$dir = $this->get_tmp_path();
		}

		$filename = basename( $filename );
		if ( !$filename ) {
			$filename = time();
		}

		$filename = substr( $filename, strrpos( $filename, '.' ) ) . '.tmp';
		$filename = $dir . $this->unique_filename( $dir, $filename );
		touch( $filename );
		return $filename;
	}

	/**
	 * Get a filename that is sanitized and unique for the given directory.
	 *
	 * If the filename is not unique, then a number will be added to the filename
	 * before the extension, and will continue adding numbers until the filename is
	 * unique.
	 *
	 * @since 0.1
	 *
	 * @param string  $dir
	 * @param string  $filename
	 * @return string New filename, if given wasn't unique.
	 */
	function unique_filename( $dir, $filename ) {
		// sanitize the file name before we begin processing
		$filename = LF_File::sanitize_file_name( $filename );

		// separate the filename into a name and extension
		$info = pathinfo( $filename );
		$ext = !empty( $info['extension'] ) ? '.' . $info['extension'] : '';
		$name = basename( $filename, $ext );

		// edge case: if file is named '.ext', treat as an empty name
		if ( $name === $ext )
			$name = '';

		// Increment the file number until we have a unique file to save in $dir.
		$number = '';

		// change '.ext' to lower case
		if ( $ext && strtolower( $ext ) != $ext ) {
			$ext2 = strtolower( $ext );
			$filename2 = preg_replace( '|' . preg_quote( $ext ) . '$|', $ext2, $filename );

			// check for both lower and upper case extension or image sub-sizes may be overwritten
			while ( file_exists( $dir . "/$filename" ) || file_exists( $dir . "/$filename2" ) ) {
				$new_number = $number + 1;
				$filename = str_replace( "$number$ext", "$new_number$ext", $filename );
				$filename2 = str_replace( "$number$ext2", "$new_number$ext2", $filename2 );
				$number = $new_number;
			}
			return $filename2;
		}

		while ( file_exists( $dir . "/$filename" ) ) {
			if ( '' == "$number$ext" )
				$filename = $filename . ++$number . $ext;
			else
				$filename = str_replace( "$number$ext", ++$number . $ext, $filename );
		}

		return $filename;
	}

	/**
	 * Translate full local path to remote filesystem path
	 *
	 * @since 0.1
	 * @access public
	 * @return string The location of the remote path.
	 */
	function translate_path( $file ) {
		if ( $this->is_dir( $file ) ) {
			$filename = '';
			$dir = $file;
		}
		else {
			$filename = basename( $file );
			$dir = dirname( $file );
		}

		$folder = $this->find_folder( $dir );

		return $folder . $filename;
	}

	/**
	 * Locates a folder on the remote filesystem.
	 *
	 * Assumes that on Windows systems, Stripping off the Drive letter is OK
	 * Sanitizes \\ to / in windows filepaths.
	 *
	 * @since 2.7
	 * @access public
	 *
	 * @param string  $folder the folder to locate
	 * @return string The location of the remote path.
	 */
	function find_folder( $folder ) {
		if ( 'direct' == $this->method ) {
			$folder = str_replace( '\\', '/', $folder ); //Windows path sanitisation
			return LF_File::trailingslashit( $folder );
		}

		$folder = preg_replace( '|^([a-z]{1}):|i', '', $folder ); //Strip out windows drive letter if it's there.
		$folder = str_replace( '\\', '/', $folder ); //Windows path sanitisation

		if ( isset( $this->cache[ $folder ] ) )
			return $this->cache[ $folder ];

		if ( $this->exists( $folder ) ) { //Folder exists at that absolute path.
			$folder = LF_File::trailingslashit( $folder );
			$this->cache[ $folder ] = $folder;
			return $folder;
		}
		if ( $return = $this->search_for_folder( $folder ) )
			$this->cache[ $folder ] = $return;
		return $return;
	}

	/**
	 * Locates a folder on the remote filesystem.
	 *
	 * Expects Windows sanitized path
	 *
	 * @since 2.7
	 * @access private
	 *
	 * @param string  $folder the folder to locate
	 * @param string  $base   the folder to start searching from
	 * @param bool    $loop   if the function has recursed, Internal use only
	 * @return string The location of the remote path.
	 */
	function search_for_folder( $folder, $base = '.', $loop = false ) {
		if ( empty( $base ) || '.' == $base )
			$base = LF_File::trailingslashit( $this->cwd() );

		$folder = LF_File::untrailingslashit( $folder );

		$folder_parts = explode( '/', $folder );
		$last_path = $folder_parts[ count( $folder_parts ) - 1 ];

		$files = $this->dirlist( $base );

		foreach ( $folder_parts as $key ) {
			if ( $key == $last_path )
				continue; //We want this to be caught by the next code block.

			//Working from /home/ to /user/ to /wordpress/ see if that file exists within the current folder,
			// If its found, change into it and follow through looking for it.
			// If it cant find WordPress down that route, it'll continue onto the next folder level, and see if that matches, and so on.
			// If it reaches the end, and still cant find it, it'll return false for the entire function.
			if ( isset( $files[ $key ] ) ) {
				//Lets try that folder:
				$newdir = LF_File::trailingslashit( LF_File::path_join( $base, $key ) );
				if ( $this->verbose )
					printf( __( 'Changing to %s' ) . '<br/>', $newdir );
				if ( $ret = $this->search_for_folder( $folder, $newdir, $loop ) )
					return $ret;
			}
		}

		//Only check this as a last resort, to prevent locating the incorrect install. All above procedures will fail quickly if this is the right branch to take.
		if ( isset( $files[ $last_path ] ) ) {
			if ( $this->verbose )
				printf( __( 'Found %s' ) . '<br/>',  $base . $last_path );
			return LF_File::trailingslashit( $base . $last_path );
		}
		if ( $loop )
			return false; //Prevent this function from looping again.
		//As an extra last resort, Change back to / if the folder wasn't found. This comes into effect when the CWD is /home/user/ but WP is at /var/www/.... mainly dedicated setups.
		return $this->search_for_folder( $folder, '/', true );

	}

	/**
	 * Returns the *nix style file permissions for a file
	 *
	 * From the PHP documentation page for fileperms()
	 *
	 * @link http://docs.php.net/fileperms
	 * @since 2.5
	 * @access public
	 *
	 * @param string  $file string filename
	 * @return int octal representation of permissions
	 */
	function gethchmod( $file ) {
		$perms = $this->getchmod( $file );
		if ( ( $perms & 0xC000 ) == 0xC000 ) // Socket
			$info = 's';
		elseif ( ( $perms & 0xA000 ) == 0xA000 ) // Symbolic Link
			$info = 'l';
		elseif ( ( $perms & 0x8000 ) == 0x8000 ) // Regular
			$info = '-';
		elseif ( ( $perms & 0x6000 ) == 0x6000 ) // Block special
			$info = 'b';
		elseif ( ( $perms & 0x4000 ) == 0x4000 ) // Directory
			$info = 'd';
		elseif ( ( $perms & 0x2000 ) == 0x2000 ) // Character special
			$info = 'c';
		elseif ( ( $perms & 0x1000 ) == 0x1000 ) // FIFO pipe
			$info = 'p';
		else // Unknown
			$info = 'u';

		// Owner
		$info .= ( ( $perms & 0x0100 ) ? 'r' : '-' );
		$info .= ( ( $perms & 0x0080 ) ? 'w' : '-' );
		$info .= ( ( $perms & 0x0040 ) ?
			( ( $perms & 0x0800 ) ? 's' : 'x' ) :
			( ( $perms & 0x0800 ) ? 'S' : '-' ) );

		// Group
		$info .= ( ( $perms & 0x0020 ) ? 'r' : '-' );
		$info .= ( ( $perms & 0x0010 ) ? 'w' : '-' );
		$info .= ( ( $perms & 0x0008 ) ?
			( ( $perms & 0x0400 ) ? 's' : 'x' ) :
			( ( $perms & 0x0400 ) ? 'S' : '-' ) );

		// World
		$info .= ( ( $perms & 0x0004 ) ? 'r' : '-' );
		$info .= ( ( $perms & 0x0002 ) ? 'w' : '-' );
		$info .= ( ( $perms & 0x0001 ) ?
			( ( $perms & 0x0200 ) ? 't' : 'x' ) :
			( ( $perms & 0x0200 ) ? 'T' : '-' ) );
		return $info;
	}

	/**
	 * Converts *nix style file permissions to a octal number.
	 *
	 * Converts '-rw-r--r--' to 0644
	 * From "info at rvgate dot nl"'s comment on the PHP documentation for chmod()
	 *
	 * @link http://docs.php.net/manual/en/function.chmod.php#49614
	 * @since 2.5
	 * @access public
	 *
	 * @param string  $mode string *nix style file permission
	 * @return int octal representation
	 */
	function getnumchmodfromh( $mode ) {
		$realmode = '';
		$legal =  array( '', 'w', 'r', 'x', '-' );
		$attarray = preg_split( '//', $mode );

		for ( $i=0; $i < count( $attarray ); $i++ )
			if ( $key = array_search( $attarray[$i], $legal ) )
				$realmode .= $legal[$key];

			$mode = str_pad( $realmode, 9, '-' );
		$trans = array( '-'=>'0', 'r'=>'4', 'w'=>'2', 'x'=>'1' );
		$mode = strtr( $mode, $trans );

		$newmode = '';
		$newmode .= $mode[0] + $mode[1] + $mode[2];
		$newmode .= $mode[3] + $mode[4] + $mode[5];
		$newmode .= $mode[6] + $mode[7] + $mode[8];
		return $newmode;
	}

	/**
	 * Determines if the string provided contains binary characters.
	 *
	 * @since 2.7
	 * @access private
	 *
	 * @param string  $text String to test against
	 * @return bool true if string is binary, false otherwise
	 */
	function is_binary( $text ) {
		return (bool) preg_match( '|[^\x20-\x7E]|', $text ); //chr(32)..chr(127)
	}
}
