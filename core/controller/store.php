<?php
namespace Leeflets\Controller;

class Store extends \Leeflets\Controller {
	function templates() {
		$active_template_folder = $this->settings->get( 'template', 'active' );
		$active_template = array();

		$templates = array();
		$folders = glob( $this->config->templates_path . '/*', GLOB_ONLYDIR );
		foreach ( $folders as $folder ) {
			if ( !file_exists( $folder . '/meta-about.php' ) ) continue;
			
			$variables = \Leeflets\Inc::variables( $folder . '/meta-about.php', array( 'about' ) );
			if ( is_array( $variables ) ) {
				extract( $variables );
			}
			
			if ( !isset( $about['name'] ) || !isset( $about['version'] ) ) continue;

			// Add default array keys to avoid having to check if
			// indexes exist and array index errors
			$about = array_merge( array(
				'name' => '',
				'version' => '',
				'description' => '',
				'screenshot' => 'http://placehold.it/360x270',
				'author' => array(
					'name' => '',
					'url' => ''
				),
				'changelog' => array()
			), $about );
			
			$folder = basename( $folder );
			$about['slug'] = $folder;

			// Get screenshot URL
			$screenshots = glob( $this->config->templates_path . '/' . $folder . '/screenshot.{jpeg,jpg,gif,png}', GLOB_BRACE );
			if ( isset( $screenshots[0] ) ) {
				$about['screenshot'] = $this->router->get_template_url( $folder, basename( $screenshots[0] ) );
			}
			
			if ( $folder == $active_template_folder ) {
				$active_template = $about;
			}
			else {
				$templates[$folder] = $about;
			}

			unset( $about );
		}

		return compact( 'active_template', 'templates' );
	}

	function _get_products( $type ) {
		$http = new \Leeflets\Http( $this->config, $this->hook, $this->router );
		$response = $http->get( $this->config->leeflets_api_url . '/product-list?cat=' . $type );

		if ( \Leeflets\Error::is_a( $response ) ) {
			return $response;
		}

		if ( (int) $response['response']['code'] < 200 || (int) $response['response']['code'] > 399 ) {
			return new \Leeflets\Error( 'product_list_fail_http_status', 'Failed to get product list from leeflets.com. Received response ' . $response['response']['code'] . ' ' . $response['response']['message'] . '.', $response );
		}

        if ( !( $response_data = json_decode( $response['body'], true ) ) ) {
            return new \Leeflets\Error( 'product_list_fail_json_decode', 'Failed to get product list from leeflets.com. Error decoding the JSON response received from the server.', $response['body'] );
        }

        if ( !isset( $response_data['products'] ) ) {
            return new \Leeflets\Error( 'product_list_fail_array_index', 'Failed to get product list from leeflets.com. Missing "products" array index.', $response['body'] );
        }

        return $response_data['products'];
	}

	function buy_templates() {
		$templates = $this->_get_products( 'templates' );

		if ( \Leeflets\Error::is_a( $templates ) ) {
			echo $templates->get_error_message();
			return false;
		}

		return compact( 'templates' );
	}

	function activate_template( $slug ) {
		$template_path = $this->config->templates_path . '/' . $slug;
		if ( !is_dir( $template_path ) ) {
			exit;
		}

		$settings = $this->settings->get_data();
		$settings['template']['active'] = $slug;
		
		if ( !$this->settings->write( $settings, $this->filesystem ) ) {
			exit;
		}

		$url = $this->router->admin_url( '/store/templates/' );
		if ( $this->router->is_ajax ) {
			$url .= '?ajax=1';

			if ( isset( $_GET['slim'] ) ) {
				$url .= '&slim=1';
			}
		}

		$this->router->redirect( $url );
		exit;
	}
}