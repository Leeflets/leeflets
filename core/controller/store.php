<?php
namespace Leeflets\Controller;

class Store extends \Leeflets\Controller {
	function templates() {
		$active_template_folder = $this->settings->get( 'template', 'active' );
		$active_addons = $this->settings->get( 'active_addons' );
		$active_template = array();

		$templates = array();
		$addons = array();
		$products = array(
			'templates' => $this->config->templates_path, 
			'addons' => $this->config->addons_path
		);
		foreach ( $products as $product_type => $path ) {
			$folders = glob( $path . '/*', GLOB_ONLYDIR );

			foreach ( $folders as $folder ) {
				$about = $this->settings->get_product_about( $folder );
				if ( !$about ) {
					return false;
				}

				$folder = basename( $folder );
				$about['slug'] = $folder;

				// Get screenshot URL
				$screenshots = glob( $path . '/' . $folder . '/screenshot.{jpeg,jpg,gif,png}', GLOB_BRACE );

				if ( 'templates' == $product_type ) {
					if ( isset( $screenshots[0] ) ) {
						$about['screenshot'] = $this->router->get_template_url( $folder, basename( $screenshots[0] ) );
					}
					else {
						$about['screenshot'] = 'http://placehold.it/360x270';
					}
					
					if ( $folder == $active_template_folder ) {
						$active_template = $about;
					}
					else {
						$templates[$folder] = $about;
					}
				}
				else {
					if ( isset( $screenshots[0] ) ) {
						$about['screenshot'] = $this->router->get_addon_url( $folder, basename( $screenshots[0] ) );
					}
					else {
						$about['screenshot'] = 'http://placehold.it/60x60';
					}

					$about['active'] = ( (bool)$active_addons && in_array( $folder, $active_addons ) );

					$addons[$folder] = $about;
				}

				unset( $about );
			}
		}

		return compact( 'active_template', 'templates', 'addons' );
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

	function products( $type ) {
		$products = $this->_get_products( $type );

		if ( \Leeflets\Error::is_a( $products ) ) {
			echo $products->get_error_message();
			return false;
		}

		echo $this->view->render( compact( 'products' ), 'store/buy-' . $type );
		return false;
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

	function addon( $slug, $switch ) {
		$addons_path = $this->config->addons_path . '/' . $slug;
		if ( !is_dir( $addons_path ) ) {
			echo "The addon '$slug' could not be found.";
			exit;
		}

		$result = $this->addon->toggle( $slug, ( 'on' == $switch ) );

		if ( \Leeflets\Error::is_a( $result ) ) {
			echo $result->get_error_message();
			exit;
		}

		exit;
	}
}