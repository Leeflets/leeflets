<?php

namespace Leeflets\Controller;

use Leeflets\Core\Response;
use Leeflets\Core\Session;
use Leeflets\Core\SessionInterface;
use Leeflets\View\View;
use Twig_Error;
use Twig_Error_Loader;

abstract class AbstractController {

    /**
     * @var array
     */
    protected $config;

    /** @var  SessionInterface */
    protected $session;

    public function __construct() {
        $this->session = Session::init($_SESSION);
    }

    /**
     * @param array $config
     */
    public function setConfig($config) {
        $this->config = $config;
    }

    /**
     * @return array
     */
    protected function getBasicContext() {
        $isLoggedIn = $this->session->exists('user');

        return [
            'loggedIn' => $isLoggedIn,
            'title' => $this->config['title'],
            'userTemplate' => $this->config['one_pager_template']
        ];
    }

    /**
     * @param string $templateName
     * @param array $data
     *
     * @return Response
     */
    protected function createHtmlResponse($templateName, $data) {
        try {
            $view = new View($this->config['one_pager_template_dir'], $templateName, $data);

            return new Response($view->toHtml());
        } catch(Twig_Error $e) {
            exit($e->getRawMessage());
        }
    }

    protected function redirect($location) {
        header("Location: $location");
        exit();
    }

}
