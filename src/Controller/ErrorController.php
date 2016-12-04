<?php

namespace Leeflets\Core\Controller;

use Leeflets\Core\Library\Controller;

class ErrorController extends Controller {
	function e404() {
        header('HTTP/1.0 404 Not Found');
		die( 'Not found.' );
	}
}
