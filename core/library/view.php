<?php
namespace Leeflets;

class View {

    public $router, $config, $hook;
    public $enqueued_scripts, $enqueued_stylesheets;

    private $vars;

    function __construct( Config $config, Router $router, Hook $hook ) {
        $this->config = $config;
        $this->router = $router;
        $this->hook = $hook;
    }

    function enqueue_script( $script ) {
        $this->enqueued_scripts[] = $script;
    }

    function enqueue_stylesheet( $stylesheet ) {
        $this->enqueued_stylesheets[] = $stylesheet;
    }
    
    function render( $vars = array(), $file = '' ) {
        if ( !$file ) {
            $file = $this->router->controller_name . '/' . str_replace( '_', '-', $this->router->action );
        }

        if ( !isset( $vars['content'] ) ) {
            $vars['content'] = '';
        }

        if ( isset( $vars['layout'] ) ) {
            $layout = $vars['layout'];
        }
        elseif ( $this->router->is_ajax ) {
            $layout = 'ajax';
        }
        else {
            $layout = 'default';
        }

        $path = $this->config->view_path . '/' . $file . '.php';

        if ( file_exists( $path ) ) {
            $content = $this->get_content( $path, $vars );
            $vars['content'] .= $content;
        }

        if ( !isset( $vars['page-title'] ) || !$vars['page-title'] ) {
            $vars['page-title'] = 'Leeflets';
        }
        else {
            $vars['page-title'] = $vars['page-title'] . ' &laquo; Leeflets';
        }

        echo $this->get_content( $this->config->theme_path . '/' . $layout . '.php', $vars );
    }

    function get_content( $path, $vars ) {
        if ( is_array( $vars ) ) $this->vars = $vars;
        extract( $vars );
        ob_start();
        include $path;
        return ob_get_clean();
    }

    function get_partial( $name, $vars = array() ) {
        ob_start();
        $this->partial( $name, $vars );
        return ob_get_clean();
    }

    function partial( $name, $vars = array() ) {
        extract( $vars );
        include $this->config->view_path . '/partials/' . $name . '.php';
    }

    function out( $var ) {
        if ( isset( $this->vars[$var] ) ) {
            echo $this->vars[$var];
        }
    }

    function get( $var ) {
        if ( isset( $this->vars[$var] ) ) {
            return $this->vars[$var];
        }
        else {
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
        $nav = array(
            array(
                'text' => 'Home',
                'atts' => array(
                    'id' => 'nav-home',
                    'class' => 'home',
                    'href' => $this->router->admin_url()
                )
            ),
            array(
                'text' => 'Content',
                'atts' => array(
                    'id' => 'nav-content',
                    'class' => 'content',
                    'href' => $this->router->admin_url( '/content/edit/' ),
                    'container-name' => 'edit-content'
                )
            ),
            array(
                'text' => 'Store',
                'atts' => array(
                    'id' => 'nav-store',
                    'class' => 'store',
                    'href' => $this->router->admin_url( '/store/templates/' ),
                    'container-name' => 'store-templates'
                )
            ),
            array(
                'text' => 'Settings',
                'atts' => array(
                    'id' => 'nav-settings',
                    'class' => 'settings',
                    'href' => $this->router->admin_url( '/settings/edit/' ),
                    'container-name' => 'edit-settings'
                )
            ),
            array(
                'text' => 'Logout',
                'atts' => array(
                    'id' => 'nav-logout',
                    'class' => 'logout',
                    'href' => $this->router->admin_url( '/user/logout/' )
                )
            ),
            array(
                'text' => 'View',
                'atts' => array(
                    'id' => 'nav-view',
                    'class' => 'view',
                    'href' => $this->router->site_url(),
                    'target' => '_blank'
                )
            ),
            array(
                'text' => 'Publish',
                'atts' => array(
                    'id' => 'nav-publish',
                    'class' => 'publish',
                    'href' => $this->router->admin_url( '/content/publish/' )
                )
            ),
        );

        // Remove the content menu if we're not debugging
        if ( !$this->config->debug ) {
            unset( $nav[1] );
        }

        $nav = $this->hook->apply( 'admin_menu', $nav );

        return $nav;
    }
}
