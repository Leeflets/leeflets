<?php

namespace Leeflets\Core\Library\Form;

use Leeflets\Core\Library\String;

class Email extends Control {

	function __construct($parent, $id, $args = []) {
		if (!isset($args['validation'])) {
			$args['validation'] = [];
		}

		array_unshift($args['validation'], [
			'callback' => [$this, 'valid_email'],
			'msg' => 'Sorry, that is not a valid email address.'
		]);

		parent::__construct($parent, $id, $args);
	}

	function html_middle() {
		$attributes = $this->atts_html();
		$value = $this->value_att();
		echo "<input type='email' $attributes value='$value'/>";
	}

	function valid_email($value) {
		return String::valid_email($value);
	}
}
