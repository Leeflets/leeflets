<?php
namespace Leeflets\Controller;

class Home extends \Leeflets\Controller {
	function index() {
		$core_update_check_enabled = $this->hook->apply( 'core_update_check_enabled', true );
		if ( $core_update_check_enabled ) {
			//$this->hook->add( 'admin_footer', array( $this, '_update_check' ) );
		}
	}

	function _update_check() {
		$product_info = new \Leeflets\Product_Info( $this->config, $this->hook, $this->router, $this->filesystem );

		$msg = '';
		$problem = 'There was a problem (#%s) checking for a core update. You might want to check <a href="http://leeflets.com">leeflets.com</a> to see if there\'s a new version.';

		$result = $product_info->refresh();

		if ( \Leeflets\Error::is_a( $result ) ) {
			$msg = sprintf( $problem, '1' );
		}
		elseif ( !$core = $product_info->get( 'core' ) ) {
			$msg = sprintf( $problem, '2' );
		}
		elseif ( empty( $core['version'] ) ) {
			$msg = sprintf( $problem, '3' );
		}
		elseif ( version_compare( $this->config->version, $core['version'], '<' ) ) {
			$msg = 'There is a new version of Leeflets available. You are currently on version ' . htmlspecialchars( $this->config->version ) . '. Visit <a href="http://leeflets.com">leeflets.com</a> to download version ' . htmlspecialchars( $core['version'] ) . '.';
		}

		if ( $msg ) {
			$this->view->partial( 'core-update-msg', compact( 'msg' ) );
		}
	}
}
