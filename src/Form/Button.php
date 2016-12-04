<?php

namespace Leeflets\Core\Library\Form;

class Button extends Element {
    function __construct( $parent, $id, $args = array() ) {
    	$this->special_args( 'button-type', $args );
    	parent::__construct( $parent, $id, $args );
	}

	function html() {
		$innerHtml = $this->esc_html($this->value);
		$attributes = $this->atts_html();
		echo "<button {$attributes} >$innerHtml</button>";
	}
}