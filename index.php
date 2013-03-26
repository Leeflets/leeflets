<?php
define( 'LEEFLETS_PHP_VERSION_REQUIRED', '5.3' );
if ( version_compare( PHP_VERSION, LEEFLETS_PHP_VERSION_REQUIRED, '<' ) ) {
	die( 'Leeflets requires that you run PHP version ' . LEEFLETS_PHP_VERSION_REQUIRED . ' or greater. You are currently running PHP ' . PHP_VERSION . '.' );
}

$admin_path = dirname( __FILE__ );
require $admin_path . '/core/library/leeflets.php';
