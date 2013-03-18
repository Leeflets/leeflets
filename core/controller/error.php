<?php
namespace Leeflets\Controller;

class LF_Controller_Error extends LF_Controller {
	function e404() {
        header('HTTP/1.0 404 Not Found');
		die( 'Not found.' );
	}
}
