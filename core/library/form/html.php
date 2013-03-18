<?php
namespace Leeflets\Library\Form;

class Html extends LF_Form_Element {
	function html() {
		echo $this->value;
	}
}
