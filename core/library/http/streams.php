<?php
namespace Leeflets\Http;

/**
 * HTTP request method uses Streams to retrieve the url.
 *
 * Requires PHP 5.0+ and uses fopen with stream context. Requires that 'allow_url_fopen' PHP setting
 * to be enabled.
 *
 * Second preferred method for getting the URL, for PHP 5.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7.0
 */
class Streams {
	/**
	 * Send a HTTP request to a URI using streams with fopen().
	 *
	 * @access public
	 * @since 2.7.0
	 *
	 * @param string  $url
	 * @param str|array $args Optional. Override the defaults.
	 * @return array 'headers', 'body', 'response', 'cookies' and 'filename' keys.
	 */
	function request( $url, $args = array() ) {
		$defaults = array(
			'method' => 'GET', 'timeout' => 5,
			'redirection' => 5, 'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(), 'body' => null, 'cookies' => array()
		);

		$r = wp_parse_args( $args, $defaults );

		if ( isset( $r['headers']['User-Agent'] ) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset( $r['headers']['User-Agent'] );
		} else if ( isset( $r['headers']['user-agent'] ) ) {
				$r['user-agent'] = $r['headers']['user-agent'];
				unset( $r['headers']['user-agent'] );
			}

		// Construct Cookie: header if any cookies are set
		WP_Http::buildCookieHeader( $r );

		$arrURL = parse_url( $url );

		if ( false === $arrURL )
			return new WP_Error( 'http_request_failed', sprintf( __( 'Malformed URL: %s' ), $url ) );

		if ( 'http' != $arrURL['scheme'] && 'https' != $arrURL['scheme'] )
			$url = preg_replace( '|^' . preg_quote( $arrURL['scheme'], '|' ) . '|', 'http', $url );

		// Convert Header array to string.
		$strHeaders = '';
		if ( is_array( $r['headers'] ) )
			foreach ( $r['headers'] as $name => $value )
				$strHeaders .= "{$name}: $value\r\n";
		else if ( is_string( $r['headers'] ) )
				$strHeaders = $r['headers'];

			$is_local = isset( $args['local'] ) && $args['local'];
		$ssl_verify = isset( $args['sslverify'] ) && $args['sslverify'];
		if ( $is_local )
			$ssl_verify = apply_filters( 'https_local_ssl_verify', $ssl_verify );
		elseif ( ! $is_local )
			$ssl_verify = apply_filters( 'https_ssl_verify', $ssl_verify );

		$arrContext = array( 'http' =>
			array(
				'method' => strtoupper( $r['method'] ),
				'user_agent' => $r['user-agent'],
				'max_redirects' => $r['redirection'] + 1, // See #11557
				'protocol_version' => (float) $r['httpversion'],
				'header' => $strHeaders,
				'ignore_errors' => true, // Return non-200 requests.
				'timeout' => $r['timeout'],
				'ssl' => array(
					'verify_peer' => $ssl_verify,
					'verify_host' => $ssl_verify
				)
			)
		);

		$proxy = new WP_HTTP_Proxy();

		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {
			$arrContext['http']['proxy'] = 'tcp://' . $proxy->host() . ':' . $proxy->port();
			$arrContext['http']['request_fulluri'] = true;

			// We only support Basic authentication so this will only work if that is what your proxy supports.
			if ( $proxy->use_authentication() )
				$arrContext['http']['header'] .= $proxy->authentication_header() . "\r\n";
		}

		if ( ! is_null( $r['body'] ) )
			$arrContext['http']['content'] = $r['body'];

		$context = stream_context_create( $arrContext );

		if ( !WP_DEBUG )
			$handle = @fopen( $url, 'r', false, $context );
		else
			$handle = fopen( $url, 'r', false, $context );

		if ( ! $handle )
			return new WP_Error( 'http_request_failed', sprintf( __( 'Could not open handle for fopen() to %s' ), $url ) );

		$timeout = (int) floor( $r['timeout'] );
		$utimeout = $timeout == $r['timeout'] ? 0 : 1000000 * $r['timeout'] % 1000000;
		stream_set_timeout( $handle, $timeout, $utimeout );

		if ( ! $r['blocking'] ) {
			stream_set_blocking( $handle, 0 );
			fclose( $handle );
			return array( 'headers' => array(), 'body' => '', 'response' => array( 'code' => false, 'message' => false ), 'cookies' => array() );
		}

		if ( $r['stream'] ) {
			if ( ! WP_DEBUG )
				$stream_handle = @fopen( $r['filename'], 'w+' );
			else
				$stream_handle = fopen( $r['filename'], 'w+' );

			if ( ! $stream_handle )
				return new WP_Error( 'http_request_failed', sprintf( __( 'Could not open handle for fopen() to %s' ), $r['filename'] ) );

			stream_copy_to_stream( $handle, $stream_handle );

			fclose( $stream_handle );
			$strResponse = '';
		} else {
			$strResponse = stream_get_contents( $handle );
		}

		$meta = stream_get_meta_data( $handle );

		fclose( $handle );

		$processedHeaders = array();
		if ( isset( $meta['wrapper_data']['headers'] ) )
			$processedHeaders = WP_Http::processHeaders( $meta['wrapper_data']['headers'] );
		else
			$processedHeaders = WP_Http::processHeaders( $meta['wrapper_data'] );

		// Streams does not provide an error code which we can use to see why the request stream stopped.
		// We can however test to see if a location header is present and return based on that.
		if ( isset( $processedHeaders['headers']['location'] ) && 0 !== $args['_redirection'] )
			return new WP_Error( 'http_request_failed', __( 'Too many redirects.' ) );

		if ( ! empty( $strResponse ) && isset( $processedHeaders['headers']['transfer-encoding'] ) && 'chunked' == $processedHeaders['headers']['transfer-encoding'] )
			$strResponse = WP_Http::chunkTransferDecode( $strResponse );

		if ( true === $r['decompress'] && true === WP_Http_Encoding::should_decode( $processedHeaders['headers'] ) )
			$strResponse = WP_Http_Encoding::decompress( $strResponse );

		return array( 'headers' => $processedHeaders['headers'], 'body' => $strResponse, 'response' => $processedHeaders['response'], 'cookies' => $processedHeaders['cookies'], 'filename' => $r['filename'] );
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @access public
	 * @since 2.7.0
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	public static function test( $args = array() ) {
		if ( ! function_exists( 'fopen' ) )
			return false;

		if ( ! function_exists( 'ini_get' ) || true != ini_get( 'allow_url_fopen' ) )
			return false;

		$is_ssl = isset( $args['ssl'] ) && $args['ssl'];

		if ( $is_ssl && ! extension_loaded( 'openssl' ) )
			return false;

		return apply_filters( 'use_streams_transport', true, $args );
	}
}
