<?php

namespace Leeflets\Core\Library;

class ProductInfo extends DataFile {
	private $hook, $router, $filesystem;
	
	function __construct( Config $config, Hook $hook, Router $router, $filesystem ) {
		$this->config = $config;

		parent::__construct( $this->config->data_path . '/product-info.json.php', $this->config );

		$this->hook = $hook;
		$this->router = $router;
		$this->filesystem = $filesystem;

		$this->load();
	}

	function load() {
		$this->data = $this->read();
		if ( !$this->data ) {
			$this->data = array();
		}
	}

	function write() {
		parent::write( $this->data, $this->filesystem );
	}

	function get( $slug ) {
		if ( empty( $this->data['products'][$slug] ) ) {
			return false;
		}

		return $this->data['products'][$slug];
	}

	function refresh( $force = false ) {
		// How often to check for updates
		$interval = $this->hook->apply( 'product_info_refresh_interval', 60 * 60 * 12 );

		if ( !$force && isset( $this->data['last_refresh'] ) && $this->data['last_refresh'] > time() - $interval ) {
			return false;
		}

		$http = new Http( $this->config, $this->hook, $this->router );
		$response = $http->get( $this->config->leeflets_api_url . '/product-info?slugs=core' );

		if ( Error::is_a( $response ) ) {
			return $response;
		}

		if ( (int) $response['response']['code'] < 200 || (int) $response['response']['code'] > 399 ) {
			return new Error( 'product_info_refresh_fail_http_status', 'Failed to refresh product info from leeflets.com. Received response ' . $response['response']['code'] . ' ' . $response['response']['message'] . '.', $response );
		}

        if ( !( $response_data = json_decode( $response['body'], true ) ) ) {
            return new Error( 'product_info_refresh_fail_json_decode', 'Failed to refresh product info from leeflets.com. Error decoding the JSON response received from the server.', $response['body'] );
        }

        $this->data = array();
        $this->data['last_refresh'] = time();
        $this->data['products'] = $response_data;
        $this->write();

        $this->load();

		return true;
	}
}
