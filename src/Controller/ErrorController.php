<?php

namespace Leeflets\Controller;

class ErrorController extends AbstractController {

    public function e404() {
        header('HTTP/1.0 404 Not Found');
		die('Not found.');
	}
}
