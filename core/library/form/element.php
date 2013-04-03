<?php
namespace Leeflets\Form;

class Element {
	public $parent, $id, $atts;

    function __construct( $parent, $id, $args = array() ) {
        $this->id = $id;
        $this->parent = $parent;

        if ( !isset( $args['class'] ) ) {
            $args['class'] = '';
        }

        $id_class = preg_replace('@[^a-zA-Z0-9\-_]@', '-', $id );
        $id_class = trim( $id_class, '-' );
        $args['class'] = trim( $id_class . ' ' . $args['class'] );

        $this->special_args( 'value', $args );

        $this->atts = $args;
    }

    function get_name() {
        if ( null == $this->parent->parent ) {
            return $this->id;
        }

        return $this->parent->get_name() . '[' . $this->id . ']';
    }

    function get_form() {
        if ( null == $this->parent ) {
            return $this;
        }

        return $this->parent->get_form();
    }

    function get_value() {
        return null;
    }

    // Sets class variables in $list and unsets them from the $args array
    function special_args( $list, &$args, $unset = true ) {
        $vars = explode( ',', $list );
        foreach ( $vars as $var ) {
            $var = trim( $var );
            if ( isset( $args[$var] ) ) {
                $class_var = str_replace( '-', '_', $var );
                $this->$class_var = $args[$var];
                if ( $unset ) unset( $args[$var] );
            }
        }
    }

	function atts_html() {
		$out[] = '';
		foreach ( $this->atts as $key => $val ) {
			if ( $val == '' ) continue;
			if ( is_bool( $val ) ) {
                $val = ( $val ) ? 'true' : 'false';
            }
			$out[] = $key . '="' . $this->esc_att( $val ) . '"';
		}
		return implode( ' ', $out );
	}

	function esc_att( $value ) {
		return htmlspecialchars( $value, ENT_COMPAT, $this->get_encoding() );
	}

    function esc_html( $value ) {
        return htmlentities( $value, ENT_COMPAT, $this->get_encoding() );
    }

    function get_html() {
    	ob_start();
        $this->html();
        return ob_get_clean();
    }

    function validate() { return array(); }

    function get_encoding() {
        return $this->parent->get_encoding();
    }

    function get_value_from_array( $name, $array ) {
        if ( !preg_match( '@^([^\[]+)@', $name, $matches ) ) {
            return null;
        }

        $value = null;
        $var = $matches[1];
        if ( isset( $array[$var] ) ) {
            $value = $array[$var];
        }

        if ( !preg_match_all( '@\[([^\]]+)\]@', $name, $matches ) ) {
            return $value;
        }

        foreach ( $matches[1] as $var ) {
            if ( !isset( $value[$var] ) ) {
                return null;
            }

            $value = $value[$var];
        }

        return $value;
    }
}
