<?php

namespace Leeflets\Core\Library\Http;
use Leeflets\Core\Library\Error;
use Leeflets\Core\Library\Hook;
use Leeflets\Core\Library\Http;

/**
 * HTTP request method uses fsockopen function to retrieve the url.
 *
 * This would be the preferred method, but the fsockopen implementation has the most overhead of all
 * the HTTP transport implementations.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */
class Fsockopen {
	private $config, $hook;

	function __construct(Config $config, Hook $hook ) {
		$this->config = $config;
		$this->hook = $hook;
	}

	/**
	 * Send a HTTP request to a URI using fsockopen().
	 *
	 * Does not support non-blocking mode.
	 *
	 * @see Http::request For default options descriptions.
	 *
	 * @since 2.7
	 * @access public
	 * @param string  $url  URI resource.
	 * @param string|array $args Optional. Override the defaults.
	 * @return array 'headers', 'body', 'response', 'cookies' and 'filename' keys.
	 */
	function request( $url, $args = array() ) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null, 'cookies' => array()
		);

		$r = array_merge( $defaults, $args );

		if ( isset( $r['headers']['User-Agent'] ) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset( $r['headers']['User-Agent'] );
		} else if ( isset( $r['headers']['user-agent'] ) ) {
				$r['user-agent'] = $r['headers']['user-agent'];
				unset( $r['headers']['user-agent'] );
			}

		// Construct Cookie: header if any cookies are set
		Http::buildCookieHeader( $r );

		$iError = null; // Store error number
		$strError = null; // Store error string

		$arrURL = parse_url( $url );

		$fsockopen_host = $arrURL['host'];

		$secure_transport = false;

		if ( ! isset( $arrURL['port'] ) ) {
			if ( ( $arrURL['scheme'] == 'ssl' || $arrURL['scheme'] == 'https' ) && extension_loaded( 'openssl' ) ) {
				$fsockopen_host = "ssl://$fsockopen_host";
				$arrURL['port'] = 443;
				$secure_transport = true;
			} else {
				$arrURL['port'] = 80;
			}
		}

		//fsockopen has issues with 'localhost' with IPv6 with certain versions of PHP, It attempts to connect to ::1,
		// which fails when the server is not set up for it. For compatibility, always connect to the IPv4 address.
		if ( 'localhost' == strtolower( $fsockopen_host ) )
			$fsockopen_host = '127.0.0.1';

		// There are issues with the HTTPS and SSL protocols that cause errors that can be safely
		// ignored and should be ignored.
		if ( true === $secure_transport )
			$error_reporting = error_reporting( 0 );

		$startDelay = time();

		$proxy = new Proxy();

		if ( !$this->config->debug ) {
			if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) )
				$handle = @fsockopen( $proxy->host(), $proxy->port(), $iError, $strError, $r['timeout'] );
			else
				$handle = @fsockopen( $fsockopen_host, $arrURL['port'], $iError, $strError, $r['timeout'] );
		} else {
			if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) )
				$handle = fsockopen( $proxy->host(), $proxy->port(), $iError, $strError, $r['timeout'] );
			else
				$handle = fsockopen( $fsockopen_host, $arrURL['port'], $iError, $strError, $r['timeout'] );
		}

		$endDelay = time();

		// If the delay is greater than the timeout then fsockopen shouldn't be used, because it will
		// cause a long delay.
		$elapseDelay = ( $endDelay-$startDelay ) > $r['timeout'];
		if ( true === $elapseDelay )
			add_option( 'disable_fsockopen', $endDelay, null, true );

		if ( false === $handle )
			return new Error( 'http_request_failed', $iError . ': ' . $strError );

		$timeout = (int) floor( $r['timeout'] );
		$utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
		stream_set_timeout( $handle, $timeout, $utimeout );

		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) //Some proxies require full URL in this field.
			$requestPath = $url;
		else
			$requestPath = $arrURL['path'] . ( isset( $arrURL['query'] ) ? '?' . $arrURL['query'] : '' );

		if ( empty( $requestPath ) )
			$requestPath .= '/';

		$strHeaders = strtoupper( $r['method'] ) . ' ' . $requestPath . ' HTTP/' . $r['httpversion'] . "\r\n";

		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) )
			$strHeaders .= 'Host: ' . $arrURL['host'] . ':' . $arrURL['port'] . "\r\n";
		else
			$strHeaders .= 'Host: ' . $arrURL['host'] . "\r\n";

		if ( isset( $r['user-agent'] ) )
			$strHeaders .= 'User-agent: ' . $r['user-agent'] . "\r\n";

		if ( is_array( $r['headers'] ) ) {
			foreach ( (array) $r['headers'] as $header => $headerValue )
				$strHeaders .= $header . ': ' . $headerValue . "\r\n";
		} else {
			$strHeaders .= $r['headers'];
		}

		if ( $proxy->use_authentication() )
			$strHeaders .= $proxy->authentication_header() . "\r\n";

		$strHeaders .= "\r\n";

		if ( ! is_null( $r['body'] ) )
			$strHeaders .= $r['body'];

		fwrite( $handle, $strHeaders );

		if ( ! $r['blocking'] ) {
			fclose( $handle );
			return array( 'headers' => array(), 'body' => '', 'response' => array( 'code' => false, 'message' => false ), 'cookies' => array() );
		}

		$strResponse = '';
		$bodyStarted = false;

		// If streaming to a file setup the file handle
		if ( $r['stream'] ) {
			if ( ! $this->config->debug )
				$stream_handle = @fopen( $r['filename'], 'w+' );
			else
				$stream_handle = fopen( $r['filename'], 'w+' );
			if ( ! $stream_handle )
				return new Error( 'http_request_failed', sprintf( __( 'Could not open handle for fopen() to %s' ), $r['filename'] ) );

			while ( ! feof( $handle ) ) {
				$block = fread( $handle, 4096 );
				if ( $bodyStarted ) {
					fwrite( $stream_handle, $block );
				} else {
					$strResponse .= $block;
					if ( strpos( $strResponse, "\r\n\r\n" ) ) {
						$process = Http::processResponse( $strResponse );
						$bodyStarted = true;
						fwrite( $stream_handle, $process['body'] );
						unset( $strResponse );
						$process['body'] = '';
					}
				}
			}

			fclose( $stream_handle );

		} else {
			while ( ! feof( $handle ) )
				$strResponse .= fread( $handle, 4096 );

			$process = Http::processResponse( $strResponse );
			unset( $strResponse );
		}

		fclose( $handle );

		if ( true === $secure_transport )
			error_reporting( $error_reporting );

		$arrHeaders = Http::processHeaders( $process['headers'] );

		// If location is found, then assume redirect and redirect to location.
		if ( isset( $arrHeaders['headers']['location'] ) && 0 !== $r['_redirection'] ) {
			if ( $r['redirection']-- > 0 ) {
				return $this->request( Http::make_absolute_url( $arrHeaders['headers']['location'], $url ), $r );
			} else {
				return new Error( 'http_request_failed', __( 'Too many redirects.' ) );
			}
		}

		// If the body was chunk encoded, then decode it.
		if ( ! empty( $process['body'] ) && isset( $arrHeaders['headers']['transfer-encoding'] ) && 'chunked' == $arrHeaders['headers']['transfer-encoding'] )
			$process['body'] = Http::chunkTransferDecode( $process['body'] );

		if ( true === $r['decompress'] && true === Encoding::should_decode( $arrHeaders['headers'] ) )
			$process['body'] = Encoding::decompress( $process['body'] );

		return array( 'headers' => $arrHeaders['headers'], 'body' => $process['body'], 'response' => $arrHeaders['response'], 'cookies' => $arrHeaders['cookies'], 'filename' => $r['filename'] );
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @since 2.7.0
	 * @static
	 * @return boolean False means this class can not be used, true means it can.
	 */
	public static function test(Hook $hook, $args = array() ) {
		if ( ! function_exists( 'fsockopen' ) )
			return false;

		//if ( false !== ( $option = get_option( 'disable_fsockopen' ) ) && time() - $option < 12 * HOUR_IN_SECONDS )
			//return false;

		$is_ssl = isset( $args['ssl'] ) && $args['ssl'];

		if ( $is_ssl && ! extension_loaded( 'openssl' ) )
			return false;

		return $hook->apply( 'use_fsockopen_transport', true, $args );
	}
}
