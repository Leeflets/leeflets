<?php
class LF_Form_Element {
	public $form, $id, $atts;

    function __construct( $form, $id, $args = array() ) {
        $this->id = $id;
        $this->form = $form;

        if ( !isset( $args['class'] ) ) {
            $args['class'] = '';
        }

        $args['class'] = trim( $id . ' ' . $args['class'] );

        $this->special_args( 'value', $args );

        $this->atts = $args;
    }

    // Sets class variables in $list and unsets them from the $args array
    function special_args( $list, &$args, $unset = true ) {
        $vars = explode( ',', $list );
        foreach ( $vars as $var ) {
            $var = trim( $var );
            if ( isset( $args[$var] ) ) {
                $this->$var = $args[$var];
                if ( $unset ) unset( $args[$var] );
            }
        }
    }

	function atts_html() {
		$out[] = '';
		foreach ( $this->atts as $key => $val ) {
			if ( $val == '' ) continue;
			if ( is_bool( $val ) ) $val = ( $val ) ? 'true' : 'false';
			$out[] = $key . '="' . htmlspecialchars( $val ) . '"';
		}
		return implode( ' ', $out );
	}

	function esc_att( $value ) {
		return htmlspecialchars( $value, null, $this->form->get_encoding() );
	}

    function esc_html( $value ) {
        return htmlentities( $value, null, $this->form->get_encoding() );
    }

    function get_html() {
    	ob_start();
        $this->html();
        return ob_get_clean();
    }

    function validate() { return array(); }
}
