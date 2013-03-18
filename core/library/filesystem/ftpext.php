<?php
namespace Leeflets\Filesystem;

/**
 * Leeflets FTP Filesystem. (borrowed from WordPress)
 *
 * @package Leeflets
 * @subpackage Filesystem
 */

/**
 * Leeflets Filesystem Class for implementing FTP.
 *
 * @since 0.1
 * @package Leeflets
 * @subpackage Filesystem
 * @uses WP_Filesystem_Base Extends class
 */
class Ftpext extends LF_Filesystem {
	public $link;
	public $errors = null;
	public $options = array();

	function __construct( LF_Config $config, $opt ) {
		$this->config = $config;
		$this->method = 'ftpext';
		$this->errors = new LF_Error();

		// Check if possible to use ftp functions.
		if ( ! extension_loaded( 'ftp' ) ) {
			$this->errors->add( 'no_ftp_ext', 'The ftp PHP extension is not available' );
			return false;
		}

		// Set default options
		$this->options = array_merge( array(
			'port' => 21,
			'connect_timeout' => 30,
			'connection_timeout' => 30
		), $opt );

		if ( !isset( $this->options['hostname'] ) ) {
			$this->errors->add( 'no_hostname', 'FTP hostname is required' );
		}

		if ( !isset( $this->options['username'] ) ) {
			$this->errors->add( 'no_username', 'FTP username is required' );
		}

		if ( !isset( $this->options['password'] ) ) {
			$this->errors->add( 'no_password', 'FTP password is required' );
		}

		$this->options['ssl'] = false;
		if ( isset( $opt['connection_type'] ) && 'ftps' == $opt['connection_type'] )
			$this->options['ssl'] = true;
	}

	function connect() {
		if ( isset( $this->options['ssl'] ) && $this->options['ssl'] && function_exists( 'ftp_ssl_connect' ) )
			$this->link = @ftp_ssl_connect( $this->options['hostname'], $this->options['port'], $this->options['connect_timeout'] );
		else
			$this->link = @ftp_connect( $this->options['hostname'], $this->options['port'], $this->options['connect_timeout'] );

		if ( ! $this->link ) {
			$this->errors->add( 'connect', sprintf( 'Could not connect to FTP server %s:%s', $this->options['hostname'], $this->options['port'] ) );
			return false;
		}

		if ( ! @ftp_login( $this->link, $this->options['username'], $this->options['password'] ) ) {
			$this->errors->add( 'auth', 'The username and/or password is incorrect.' );
			return false;
		}

		//Set the Connection to use Passive FTP
		@ftp_pasv( $this->link, true );
		if ( @ftp_get_option( $this->link, FTP_TIMEOUT_SEC ) < $this->options['connection_timeout'] )
			@ftp_set_option( $this->link, FTP_TIMEOUT_SEC, $this->options['connection_timeout'] );

		return true;
	}

	function get_contents( $file, $type = '', $resumepos = 0 ) {
		if ( empty( $type ) )
			$type = FTP_BINARY;

		$tempfile = $this->create_tmp_file( $file );
		$temp = fopen( $tempfile, 'w+' );

		if ( ! $temp )
			return false;

		if ( ! @ftp_fget( $this->link, $temp, $file, $type, $resumepos ) )
			return false;

		fseek( $temp, 0 ); //Skip back to the start of the file being written to
		$contents = '';

		while ( ! feof( $temp ) )
			$contents .= fread( $temp, 8192 );

		fclose( $temp );
		unlink( $tempfile );
		return $contents;
	}
	
	function get_contents_array( $file ) {
		return explode( "\n", $this->get_contents( $file ) );
	}

	function put_contents( $file, $contents, $mode = false ) {
		$tempfile = $this->create_tmp_file( $file );
		$temp = fopen( $tempfile, 'w+' );
		if ( ! $temp )
			return false;

		fwrite( $temp, $contents );
		fseek( $temp, 0 ); //Skip back to the start of the file being written to

		$type = $this->is_binary( $contents ) ? FTP_BINARY : FTP_ASCII;
		$ret = @ftp_fput( $this->link, $file, $temp, $type );

		fclose( $temp );
		unlink( $tempfile );

		$this->chmod( $file, $mode );

		return $ret;
	}

	function cwd() {
		$cwd = @ftp_pwd( $this->link );
		if ( $cwd )
			$cwd = rtrim( $cwd, '/' ) . '/';
		return $cwd;
	}

	function chdir( $dir ) {
		return @ftp_chdir( $this->link, $dir );
	}

	function chgrp( $file, $group, $recursive = false ) {
		return false;
	}

	function chmod( $file, $mode = false, $recursive = false ) {
		if ( ! $mode ) {
			if ( $this->is_file( $file ) )
				$mode = $this->config->fs_chmod_file;
			elseif ( $this->is_dir( $file ) )
				$mode = $this->config->fs_chmod_dir;
			else
				return false;
		}

		// chmod any sub-objects if recursive.
		if ( $recursive && $this->is_dir( $file ) ) {
			$filelist = $this->dirlist( $file );
			foreach ( (array)$filelist as $filename => $filemeta )
				$this->chmod( $file . '/' . $filename, $mode, $recursive );
		}

		// chmod the file or directory
		if ( ! function_exists( 'ftp_chmod' ) )
			return (bool)@ftp_site( $this->link, sprintf( 'CHMOD %o %s', $mode, $file ) );
		return (bool)@ftp_chmod( $this->link, $mode, $file );
	}

	function chown( $file, $owner, $recursive = false ) {
		return false;
	}

	function owner( $file ) {
		$dir = $this->dirlist( $file );
		return $dir[$file]['owner'];
	}

	function getchmod( $file ) {
		$dir = $this->dirlist( $file );
		return $dir[$file]['permsn'];
	}

	function group( $file ) {
		$dir = $this->dirlist( $file );
		return $dir[$file]['group'];
	}

	function copy( $source, $destination, $overwrite = false, $mode = false ) {
		if ( ! $overwrite && $this->exists( $destination ) )
			return false;
		$content = $this->get_contents( $source );
		if ( false === $content )
			return false;
		return $this->put_contents( $destination, $content, $mode );
	}

	function move( $source, $destination, $overwrite = false ) {
		return ftp_rename( $this->link, $source, $destination );
	}

	function delete( $file, $recursive = false, $type = false ) {
		if ( empty( $file ) )
			return false;
		if ( 'f' == $type || $this->is_file( $file ) )
			return @ftp_delete( $this->link, $file );
		if ( !$recursive )
			return @ftp_rmdir( $this->link, $file );

		$filelist = $this->dirlist( LF_File::trailingslashit( $file ) );
		if ( !empty( $filelist ) )
			foreach ( $filelist as $delete_file )
				$this->delete( LF_File::trailingslashit( $file ) . $delete_file['name'], $recursive, $delete_file['type'] );
			return @ftp_rmdir( $this->link, $file );
	}

	function exists( $file ) {
		$list = @ftp_nlist( $this->link, $file );
		return !empty( $list ); //empty list = no file, so invert.
	}

	function is_file( $file ) {
		return $this->exists( $file ) && !$this->is_dir( $file );
	}

	function is_dir( $path ) {
		$cwd = $this->cwd();
		$result = @ftp_chdir( $this->link, LF_File::trailingslashit( $path ) );
		if ( $result && $path == $this->cwd() || $this->cwd() != $cwd ) {
			@ftp_chdir( $this->link, $cwd );
			return true;
		}
		return false;
	}

	function is_readable( $file ) {
		//Get dir list, Check if the file is readable by the current user??
		return true;
	}

	function is_writable( $file ) {
		//Get dir list, Check if the file is writable by the current user??
		return true;
	}

	function atime( $file ) {
		return false;
	}

	function mtime( $file ) {
		return ftp_mdtm( $this->link, $file );
	}

	function size( $file ) {
		return ftp_size( $this->link, $file );
	}

	function touch( $file, $time = 0, $atime = 0 ) {
		return false;
	}

	function mkdir( $path, $chmod = false, $chown = false, $chgrp = false ) {
		$path = LF_File::untrailingslashit( $path );
		if ( empty( $path ) )
			return false;

		if ( !@ftp_mkdir( $this->link, $path ) )
			return false;
		$this->chmod( $path, $chmod );
		if ( $chown )
			$this->chown( $path, $chown );
		if ( $chgrp )
			$this->chgrp( $path, $chgrp );
		return true;
	}

	function rmdir( $path, $recursive = false ) {
		return $this->delete( $path, $recursive );
	}

	function parselisting( $line ) {
		static $is_windows;
		if ( is_null( $is_windows ) )
			$is_windows = stripos( ftp_systype( $this->link ), 'win' ) !== false;

		if ( $is_windows && preg_match( '/([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)/', $line, $lucifer ) ) {
			$b = array();
			if ( $lucifer[3] < 70 )
				$lucifer[3] +=2000;
			else
				$lucifer[3] += 1900; // 4digit year fix
			$b['isdir'] = ( $lucifer[7] == '<DIR>' );
			if ( $b['isdir'] )
				$b['type'] = 'd';
			else
				$b['type'] = 'f';
			$b['size'] = $lucifer[7];
			$b['month'] = $lucifer[1];
			$b['day'] = $lucifer[2];
			$b['year'] = $lucifer[3];
			$b['hour'] = $lucifer[4];
			$b['minute'] = $lucifer[5];
			$b['time'] = @mktime( $lucifer[4] + ( strcasecmp( $lucifer[6], "PM" ) == 0 ? 12 : 0 ), $lucifer[5], 0, $lucifer[1], $lucifer[2], $lucifer[3] );
			$b['am/pm'] = $lucifer[6];
			$b['name'] = $lucifer[8];
		} elseif ( !$is_windows && $lucifer = preg_split( '/[ ]/', $line, 9, PREG_SPLIT_NO_EMPTY ) ) {
			//echo $line."\n";
			$lcount = count( $lucifer );
			if ( $lcount < 8 )
				return '';
			$b = array();
			$b['isdir'] = $lucifer[0]{0} === 'd';
			$b['islink'] = $lucifer[0]{0} === 'l';
			if ( $b['isdir'] )
				$b['type'] = 'd';
			elseif ( $b['islink'] )
				$b['type'] = 'l';
			else
				$b['type'] = 'f';
			$b['perms'] = $lucifer[0];
			$b['number'] = $lucifer[1];
			$b['owner'] = $lucifer[2];
			$b['group'] = $lucifer[3];
			$b['size'] = $lucifer[4];
			if ( $lcount == 8 ) {
				sscanf( $lucifer[5], '%d-%d-%d', $b['year'], $b['month'], $b['day'] );
				sscanf( $lucifer[6], '%d:%d', $b['hour'], $b['minute'] );
				$b['time'] = @mktime( $b['hour'], $b['minute'], 0, $b['month'], $b['day'], $b['year'] );
				$b['name'] = $lucifer[7];
			} else {
				$b['month'] = $lucifer[5];
				$b['day'] = $lucifer[6];
				if ( preg_match( '/([0-9]{2}):([0-9]{2})/', $lucifer[7], $l2 ) ) {
					$b['year'] = date( "Y" );
					$b['hour'] = $l2[1];
					$b['minute'] = $l2[2];
				} else {
					$b['year'] = $lucifer[7];
					$b['hour'] = 0;
					$b['minute'] = 0;
				}
				$b['time'] = strtotime( sprintf( '%d %s %d %02d:%02d', $b['day'], $b['month'], $b['year'], $b['hour'], $b['minute'] ) );
				$b['name'] = $lucifer[8];
			}
		}

		return $b;
	}

	function dirlist( $path = '.', $include_hidden = true, $recursive = false ) {
		if ( $this->is_file( $path ) ) {
			$limit_file = basename( $path );
			$path = dirname( $path ) . '/';
		} else {
			$limit_file = false;
		}

		$pwd = @ftp_pwd( $this->link );
		if ( ! @ftp_chdir( $this->link, $path ) ) // Cant change to folder = folder doesn't exist
			return false;
		$list = @ftp_rawlist( $this->link, '-a', false );
		@ftp_chdir( $this->link, $pwd );

		if ( empty( $list ) ) // Empty array = non-existent folder (real folder will show . at least)
			return false;

		$dirlist = array();
		foreach ( $list as $k => $v ) {
			$entry = $this->parselisting( $v );
			if ( empty( $entry ) )
				continue;

			if ( '.' == $entry['name'] || '..' == $entry['name'] )
				continue;

			if ( ! $include_hidden && '.' == $entry['name'][0] )
				continue;

			if ( $limit_file && $entry['name'] != $limit_file )
				continue;

			$dirlist[ $entry['name'] ] = $entry;
		}

		$ret = array();
		foreach ( (array)$dirlist as $struc ) {
			if ( 'd' == $struc['type'] ) {
				if ( $recursive )
					$struc['files'] = $this->dirlist( $path . '/' . $struc['name'], $include_hidden, $recursive );
				else
					$struc['files'] = array();
			}

			$ret[ $struc['name'] ] = $struc;
		}
		return $ret;
	}

	function __destruct() {
		if ( $this->link )
			ftp_close( $this->link );
	}
}
