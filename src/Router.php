<?php

namespace Leeflets\Core\Library;

use Leeflets\Core\Controller\ContentController;
use Leeflets\Core\Controller\ErrorController;
use Leeflets\Core\Controller\HomeController;
use Leeflets\Core\Controller\SettingsController;
use Leeflets\Core\Controller\SetupController;
use Leeflets\Core\Controller\UserController;

class Router {

    public $request_url, $baseRequestUrl, $request_path, $is_ajax;

    public $config, $settings;

    public $adminUrl, $siteUrl, $admin_dir_name;

    public $controller_name = '';

    public $action = '';

    public $params = [];

    public $controllerClass = '';

    function __construct($config, $baseRequestUrl = null, $requestPath = null, $isAjax = null) {
        if (is_null($baseRequestUrl)) {
            $baseRequestUrl = self::base_request_url();
        }
        if (is_null($requestPath)) {
            $requestPath = $_SERVER['REQUEST_URI'];
        }
        if (is_null($isAjax)) {
            $isAjax = isset($_REQUEST['ajax']);
        }

        $this->baseRequestUrl = $baseRequestUrl;
        $this->request_path = $requestPath;
        $this->request_url = $baseRequestUrl . $requestPath;
        $this->config = $config;
        $this->is_ajax = $isAjax;

        $this->setUrls();
    }

    public function setUrls() {
        $adminPath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $rootPath = substr($adminPath, 0, strrpos($adminPath, '/'));

        $this->adminUrl = $this->baseRequestUrl . $adminPath;
        $this->siteUrl = $this->baseRequestUrl . $rootPath;

        if ($this->adminUrl == $this->siteUrl) {
            die('Leeflets must be installed in a subfolder.');
        }

        $this->admin_dir_name = substr($adminPath, strrpos($adminPath, '/') + 1);
    }

    public function adminUrl($path = '') {
        return $this->adminUrl . '/' . ltrim($path, '/');
    }

    public function siteUrl($path = '') {
        return $this->siteUrl . '/' . ltrim($path, '/');
    }

    public function getTemplateUrl($url = '', $template = '') {
        if (!$template) {
            $template = $this->settings->get('template', 'active');
        }

        return $this->adminUrl('templates/' . rawurlencode($template) . '/' . ltrim($url, '/'));
    }

    public function getAddonUrl($addon, $url = '') {
        return $this->adminUrl('addons/' . rawurlencode($addon) . '/' . ltrim($url, '/'));
    }

    public function getUploadsUrl($url = '') {
        return $this->adminUrl('uploads/' . ltrim($url, '/'));
    }

    public function parseRequestUrl() {
        $admin_url = parse_url($this->adminUrl);
        $request_url = parse_url($this->request_url);

        $path = preg_replace('@^' . $admin_url['path'] . '@', '', $request_url['path']);
        $path = strtolower(trim($path, '/'));
        $path = str_replace('..', '', $path);

        if (!$path) {
            return;
        }

        $segments = explode('/', $path);

        $this->controller_name = array_shift($segments);
        $this->controller_name = preg_replace('@[^a-z0-9/\-_\.]@', '', $this->controller_name);

        if (!$segments) {
            return;
        }

        $this->action = array_shift($segments);
        $this->action = preg_replace('@[^a-z0-9/\-_\.]@', '', $this->action);
        $this->action = preg_replace('@[/\-\.]@', '_', $this->action);

        // if the function starts with an underscore, 
        // it can't be called as a controller action
        if ('_' == substr($this->action, 0, 1)) {
            $this->action = '';
        }

        $this->params = $segments;
        foreach ($this->params as $i => $param) {
            $this->params[$i] = urldecode($param);
        }
    }

    public function setControllerClass() {
        $name = $this->controller_name;

        if (!$name && !$this->action) {
            $this->controller_name = 'HomeController';
            $this->controllerClass = HomeController::class;
            $this->action = 'index';
            return;
        }

        $registeredControllers = [
            'content' => ContentController::class,
            'settings' => SettingsController::class,
            'setup' => SetupController::class,
            'user' => UserController::class
        ];

        $name = preg_replace('@[/\-\.]@', '_', $name);

        if (!isset($registeredControllers[$name]) || !class_exists($registeredControllers[$name])) {
            $this->controller_name = 'ErrorController';
            $this->controllerClass = ErrorController::class;
            $this->action = 'e404';
            return;
        }

        $this->controllerClass = $registeredControllers[$name];
    }

    public static function base_request_url() {
        $ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '';
        $port = ($_SERVER['SERVER_PORT'] != '80') ? ':' . $_SERVER['SERVER_PORT'] : '';
        return sprintf('http%s://%s%s', $ssl, $_SERVER['SERVER_NAME'], $port);
    }

    public static function redirect($url, $status = 302) {
        header('Location: ' . $url, true, $status);
    }
}
