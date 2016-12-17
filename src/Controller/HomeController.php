<?php

namespace Leeflets\Controller;

use Leeflets\Core\Response;
use Leeflets\Core\ResponseInterface;
use Leeflets\Core\Session;
use Leeflets\View\View;
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
        $session = Session::init($_SESSION);

        $isLoggedIn = $session->exists('user');

        return new Response(
            (new View(''))->toHtml()
        );
	}

}
