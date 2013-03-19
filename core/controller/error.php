<?php
namespace Leeflets\Controller;

class Error extends \Leeflets\Controller {
	function e404() {
        header('HTTP/1.0 404 Not Found');
		die( 'Not found.' );
	}
}
