<?php
namespace Leeflets\Http;

/**
 * HTTP request method uses Curl extension to retrieve the url.
 *
 * Requires the Curl extension to be installed.
 *
 * @package WordPress
 * @subpackage HTTP
 * @since 2.7
 */
class Curl {
	private $config, $hook;

	function __construct( \Leeflets\Config $config, \Leeflets\Hook $hook ) {
		$this->config = $config;
		$this->hook = $hook;
	}

	/**
	 * Temporary header storage for use with streaming to a file.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var string
	 */
	private $headers = '';

	/**
	 * Send a HTTP request to a URI using cURL extension.
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

		$r = array_merge( $defaults, $args );

		if ( isset( $r['headers']['User-Agent'] ) ) {
			$r['user-agent'] = $r['headers']['User-Agent'];
			unset( $r['headers']['User-Agent'] );
		} else if ( isset( $r['headers']['user-agent'] ) ) {
				$r['user-agent'] = $r['headers']['user-agent'];
				unset( $r['headers']['user-agent'] );
			}

		// Construct Cookie: header if any cookies are set.
		\Leeflets\Http::buildCookieHeader( $r );

		$handle = curl_init();

		// cURL offers really easy proxy support.
		$proxy = new Proxy();

		if ( $proxy->is_enabled() && $proxy->send_through_proxy( $url ) ) {

			curl_setopt( $handle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP );
			curl_setopt( $handle, CURLOPT_PROXY, $proxy->host() );
			curl_setopt( $handle, CURLOPT_PROXYPORT, $proxy->port() );

			if ( $proxy->use_authentication() ) {
				curl_setopt( $handle, CURLOPT_PROXYAUTH, CURLAUTH_ANY );
				curl_setopt( $handle, CURLOPT_PROXYUSERPWD, $proxy->authentication() );
			}
		}

		$is_local = isset( $r['local'] ) && $r['local'];
		$ssl_verify = isset( $r['sslverify'] ) && $r['sslverify'];
		if ( $is_local )
			$ssl_verify = $this->hook->apply( 'https_local_ssl_verify', $ssl_verify );
		elseif ( ! $is_local )
			$ssl_verify = $this->hook->apply( 'https_ssl_verify', $ssl_verify );

		// CURLOPT_TIMEOUT and CURLOPT_CONNECTTIMEOUT expect integers. Have to use ceil since
		// a value of 0 will allow an unlimited timeout.
		$timeout = (int) ceil( $r['timeout'] );
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $handle, CURLOPT_TIMEOUT, $timeout );

		curl_setopt( $handle, CURLOPT_URL, $url );
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, ( $ssl_verify === true ) ? 2 : false );
		curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, $ssl_verify );
		curl_setopt( $handle, CURLOPT_USERAGENT, $r['user-agent'] );
		// The option doesn't work with safe mode or when open_basedir is set, and there's a
		// bug #17490 with redirected POST requests, so handle redirections outside Curl.
		curl_setopt( $handle, CURLOPT_FOLLOWLOCATION, false );

		switch ( $r['method'] ) {
		case 'HEAD':
			curl_setopt( $handle, CURLOPT_NOBODY, true );
			break;
		case 'POST':
			curl_setopt( $handle, CURLOPT_POST, true );
			curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
			break;
		case 'PUT':
			curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, 'PUT' );
			curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
			break;
		default:
			curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, $r['method'] );
			if ( ! is_null( $r['body'] ) )
				curl_setopt( $handle, CURLOPT_POSTFIELDS, $r['body'] );
			break;
		}

		if ( true === $r['blocking'] )
			curl_setopt( $handle, CURLOPT_HEADERFUNCTION, array( $this, 'stream_headers' ) );

		curl_setopt( $handle, CURLOPT_HEADER, false );

		// If streaming to a file open a file handle, and setup our curl streaming handler
		if ( $r['stream'] ) {
			if ( ! $this->config->debug )
				$stream_handle = @fopen( $r['filename'], 'w+' );
			else
				$stream_handle = fopen( $r['filename'], 'w+' );
			if ( ! $stream_handle )
				return new \Leeflets\Error( 'http_request_failed', sprintf( __( 'Could not open handle for fopen() to %s' ), $r['filename'] ) );
			curl_setopt( $handle, CURLOPT_FILE, $stream_handle );
		}

		if ( !empty( $r['headers'] ) ) {
			// cURL expects full header strings in each element
			$headers = array();
			foreach ( $r['headers'] as $name => $value ) {
				$headers[] = "{$name}: $value";
			}
			curl_setopt( $handle, CURLOPT_HTTPHEADER, $headers );
		}

		if ( $r['httpversion'] == '1.0' )
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		else
			curl_setopt( $handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

		// Cookies are not handled by the HTTP API currently. Allow for plugin authors to handle it
		// themselves... Although, it is somewhat pointless without some reference.
		//do_action_ref_array( 'http_api_curl', array( &$handle ) );

		// We don't need to return the body, so don't. Just execute request and return.
		if ( ! $r['blocking'] ) {
			curl_exec( $handle );
			curl_close( $handle );
			return array( 'headers' => array(), 'body' => '', 'response' => array( 'code' => false, 'message' => false ), 'cookies' => array() );
		}

		$theResponse = curl_exec( $handle );
		$theBody = '';
		$theHeaders = \Leeflets\Http::processHeaders( $this->headers );

		if ( strlen( $theResponse ) > 0 && ! is_bool( $theResponse ) ) // is_bool: when using $args['stream'], curl_exec will return (bool)true
			$theBody = $theResponse;

		// If no response
		if ( 0 == strlen( $theResponse ) && empty( $theHeaders['headers'] ) ) {
			if ( $curl_error = curl_error( $handle ) )
				return new \Leeflets\Error( 'http_request_failed', $curl_error );
			if ( in_array( curl_getinfo( $handle, CURLINFO_HTTP_CODE ), array( 301, 302 ) ) )
				return new \Leeflets\Error( 'http_request_failed', __( 'Too many redirects.' ) );
		}

		$this->headers = '';

		$response = array();
		$response['code'] = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
		$response['message'] = \Leeflets\Http::get_status_header_desc( $response['code'] );

		curl_close( $handle );

		if ( $r['stream'] )
			fclose( $stream_handle );

		// See #11305 - When running under safe mode, redirection is disabled above. Handle it manually.
		if ( ! empty( $theHeaders['headers']['location'] ) && 0 !== $r['_redirection'] ) { // _redirection: The requested number of redirections
			if ( $r['redirection']-- > 0 ) {
				return $this->request( \Leeflets\Http::make_absolute_url( $theHeaders['headers']['location'], $url ), $r );
			} else {
				return new \Leeflets\Error( 'http_request_failed', __( 'Too many redirects.' ) );
			}
		}

		if ( true === $r['decompress'] && true === Encoding::should_decode( $theHeaders['headers'] ) )
			$theBody = Encoding::decompress( $theBody );

		return array( 'headers' => $theHeaders['headers'], 'body' => $theBody, 'response' => $response, 'cookies' => $theHeaders['cookies'], 'filename' => $r['filename'] );
	}

	/**
	 * Grab the headers of the cURL request
	 *
	 * Each header is sent individually to this callback, so we append to the $header property for temporary storage
	 *
	 * @since 3.2.0
	 * @access private
	 * @return int
	 */
	private function stream_headers( $handle, $headers ) {
		$this->headers .= $headers;
		return strlen( $headers );
	}

	/**
	 * Whether this class can be used for retrieving an URL.
	 *
	 * @static
	 * @since 2.7.0
	 *
	 * @return boolean False means this class can not be used, true means it can.
	 */
	public static function test( \Leeflets\Hook $hook, $args = array() ) {
		if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) )
			return false;

		$is_ssl = isset( $args['ssl'] ) && $args['ssl'];

		if ( $is_ssl ) {
			$curl_version = curl_version();
			if ( ! ( CURL_VERSION_SSL & $curl_version['features'] ) ) // Does this cURL version support SSL requests?
				return false;
		}

		return $hook->apply( 'use_curl_transport', true, $args );
	}
}
