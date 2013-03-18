<?php
namespace Leeflets\Library;

class LF_Hook {

    private $hooks = array();

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
