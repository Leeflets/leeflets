<?php

namespace Leeflets\Core\Library;

class View {

    public $router, $config, $hook;

    public $enqueued_scripts, $enqueued_stylesheets;

    private $vars;

    function __construct(Config $config, Router $router, Hook $hook) {
        $this->config = $config;
        $this->router = $router;
        $this->hook = $hook;
    }

    function enqueue_script($script) {
        $this->enqueued_scripts[] = $script;
    }

    function enqueue_stylesheet($stylesheet) {
        $this->enqueued_stylesheets[] = $stylesheet;
    }

    function render($vars = [], $file = '') {
        if (!$file) {
            $file = $this->router->controller_name . '/' . str_replace('_', '-', $this->router->action);
        }

        if (!isset($vars['content'])) {
            $vars['content'] = '';
        }

        if (isset($vars['layout'])) {
            $layout = $vars['layout'];
        } elseif ($this->router->is_ajax) {
            $layout = 'ajax';
        } else {
            $layout = 'default';
        }

        $path = $this->config->view_path . '/' . $file . '.php';

        if (file_exists($path)) {
            $content = $this->get_content($path, $vars);
            $vars['content'] .= $content;
        }

        if (!isset($vars['page-title']) || !$vars['page-title']) {
            $vars['page-title'] = 'Leeflets';
        } else {
            $vars['page-title'] = $vars['page-title'] . ' &laquo; Leeflets';
        }

        echo $this->get_content($this->config->theme_path . '/' . $layout . '.php', $vars);
    }

    function get_content($path, $vars) {
        if (is_array($vars)) {
            $this->vars = $vars;
        }
        extract($vars);
        ob_start();
        include $path;
        return ob_get_clean();
    }

    function get_partial($name, $vars = []) {
        ob_start();
        $this->partial($name, $vars);
        return ob_get_clean();
    }

    function partial($name, $vars = []) {
        extract($vars);
        include $this->config->view_path . '/partials/' . $name . '.php';
    }

    function out($var) {
        if (isset($this->vars[$var])) {
            echo $this->vars[$var];
        }
    }

    function get($var) {
        if (isset($this->vars[$var])) {
            return $this->vars[$var];
        } else {
            return '';
        }
    }

    function header() {
        include $this->config->theme_path . '/header.php';
    }

    function footer() {
        include $this->config->theme_path . '/footer.php';
    }

    function get_primary_nav() {
        $nav = [
            [
                'text' => 'Home',
                'atts' => [
                    'id' => 'nav-home',
                    'class' => 'home',
                    'href' => $this->router->adminUrl()
                ]
            ],
            [
                'text' => 'Content',
                'atts' => [
                    'id' => 'nav-content',
                    'class' => 'content',
                    'href' => $this->router->adminUrl('/content/edit/'),
                    'container-name' => 'edit-content'
                ]
            ],
            [
                'text' => 'Store',
                'atts' => [
                    'id' => 'nav-store',
                    'class' => 'store',
                    'href' => $this->router->adminUrl('/store/templates/'),
                    'container-name' => 'store-templates'
                ]
            ],
            [
                'text' => 'Settings',
                'atts' => [
                    'id' => 'nav-settings',
                    'class' => 'settings',
                    'href' => $this->router->adminUrl('/settings/edit/'),
                    'container-name' => 'edit-settings'
                ]
            ],
            [
                'text' => 'Logout',
                'atts' => [
                    'id' => 'nav-logout',
                    'class' => 'logout',
                    'href' => $this->router->adminUrl('/user/logout/')
                ]
            ],
            [
                'text' => 'View',
                'atts' => [
                    'id' => 'nav-view',
                    'class' => 'view',
                    'href' => $this->router->siteUrl(),
                    'target' => '_blank'
                ]
            ],
            [
                'text' => 'Publish',
                'atts' => [
                    'id' => 'nav-publish',
                    'class' => 'publish',
                    'href' => $this->router->adminUrl('/content/publish/')
                ]
            ],
        ];

        // Remove the content menu if we're not debugging
        if (!$this->config->debug) {
            unset($nav[1]);
        }

        $nav = $this->hook->apply('admin_menu', $nav);

        return $nav;
    }
}
