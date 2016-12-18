<?php

namespace Leeflets\Controller;

use Leeflets\Core\ResponseInterface;
use Widi\Components\Router\Request;

/**
 * The home controller is responsible for collecting the basic information for the one pager.
 *
 * @package Leeflets\Controller
 */
class HomeController extends AbstractController {

    /**
     * @param Request $request
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request) {

        return $this->createHtmlResponse(
            'home',
            array_merge($this->getBasicContext(), [])
        );
	}

}
