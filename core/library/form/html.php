<?php
namespace Leeflets\Library\Form;

class LF_Form_Html extends LF_Form_Element {
	function html() {
		echo $this->value;
	}
}
