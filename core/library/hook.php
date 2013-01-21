<?php
class LF_Hook {

    private $hooks = array();
    private $styles, $scripts;

    function __construct( $styles, $scripts ) {
        $this->styles = $styles;
        $this->scripts = $scripts;
        $this->default_assets();
    }

    function enqueue_script( $id, $url = false, $dependencies = array(), $version = false, $in_footer = false ) {
        $this->scripts->add( $id, $url, $dependencies, $version, $in_footer );
        $this->scripts->enqueue( $id );
    }

    function dequeue_script( $id ) {
        $this->script->dequeue( $id );
    }

    function enqueue_style( $id, $url = false, $dependencies = array(), $version = false, $media = 'all' ) {
        $this->styles->add( $id, $url, $dependencies, $version, $media );
        $this->styles->enqueue( $id );
    }

    function dequeue_style( $id ) {
        $this->styles->dequeue( $id );
    }

    function default_assets() {
        $this->enqueue_script( 'wysihtml5', '/core/theme/asset/bootstrap/wysihtml5/js/wysihtml5.js', array(), '0.3.0' );
        $this->enqueue_script( 'jquery', '/core/theme/asset/js/jquery.js', array(), '1.8.2' );
        $this->enqueue_script( 'jquery-ui-widget', '/core/theme/asset/js/jquery.ui.widget.js', array( 'jquery' ), '1.9.1' );
        $this->enqueue_script( 'jquery-iframe-transport', '/core/theme/asset/js/jquery.iframe-transport.js', array( 'jquery' ), '1.6.1' );
        $this->enqueue_script( 'jquery-fileupload', '/core/theme/asset/js/jquery.fileupload.js', array( 'jquery' ), '5.19.8' );
        $this->enqueue_script( 'bootstrap', '/core/theme/asset/bootstrap/core/js/bootstrap.js', array(), '2.2.1' );
        $this->enqueue_script( 'bootstrap-datepicker', '/core/theme/asset/bootstrap/datepicker/js/bootstrap-datepicker.js', array( 'bootstrap' ) );
        $this->enqueue_script( 'bootstrap-wysihtml5', '/core/theme/asset/bootstrap/wysihtml5/js/bootstrap-wysihtml5.js', array( 'bootstrap' ) );
        $this->enqueue_script( 'lf-script', '/core/theme/asset/js/script.js' );

        $this->enqueue_style( 'bootstrap', '/core/theme/asset/bootstrap/core/css/bootstrap.css', array(), '2.2.1' );
        $this->enqueue_style( 'bootstrap-responsive', '/core/theme/asset/bootstrap/core/css/bootstrap-responsive.css', array( 'bootstrap' ), '2.2.1' );
        $this->enqueue_style( 'bootstrap-datepicker', '/core/theme/asset/bootstrap/datepicker/css/datepicker.css', array( 'bootstrap' ) );
        $this->enqueue_style( 'bootstrap-wysihtml5', '/core/theme/asset/bootstrap/wysihtml5/css/bootstrap-wysihtml5.css', array( 'bootstrap' ) );
        $this->enqueue_style( 'jquery-fileupload', '/core/theme/asset/css/jquery.fileupload-ui.css', array(), '6.10' );
        $this->enqueue_style( 'lf-style', '/core/theme/asset/css/style.css' );

        $this->add( 'lf_head', array( $this->styles, 'do_items' ), 0, 10 );
        $this->add( 'lf_head', array( $this->scripts, 'do_head_items' ), 0, 10 );
        $this->add( 'lf_footer', array( $this->scripts, 'do_footer_items' ), 0, 10 );
    }

    function add( $key, $callback, $num_args = 1, $priority = 10 ) {
        $callback_id = $this->get_callback_id( $key, $callback, $priority );
        $this->hooks[$key][$priority][$callback_id] = array( 'name' => $callback, 'num_args' => $num_args );
        return true;
    }

    function remove( $key, $callback, $num_args = 1, $priority = 10 ) {
        $callback_id = $this->get_callback_id( $key, $callback, $priority );
        
        $return = isset( $this->hooks[$key][$priority][$callback_id] );

        unset( $this->hooks[$key][$priority][$callback_id] );

        return $return;
    }

    function apply( $key, $value = '' ) {
        if ( !isset( $this->hooks[$key] ) ) {
            return $value;
        }

        $args = func_get_args();

        foreach ( $this->hooks[$key] as $priority => $callbacks ) {
            foreach ( $callbacks as $callback ) {
                if ( !is_null( $callback['name'] ) ) {
                    $args[1] = $value;
                    $value = call_user_func_array( $callback['name'], array_slice( $args, 1, (int) $callback['num_args'] ) );
                }
            }
        }

        return $value;
    }

    private function get_callback_id( $key, $callback, $priority ) {
        if ( is_string( $callback ) ) {
            return $callback;
        }

        if ( is_object( $callback ) ) {
            // Closures are currently implemented as objects
            $callback = array( $callback, '' );
        } else {
            $callback = (array) $callback;
        }

        if ( is_object( $callback[0] ) ) {
            return spl_object_hash( $callback[0] ) . $callback[1];
        } 

        if ( is_string( $callback[0] ) ) {
            return $callback[0].$callback[1];
        }
    }
}
