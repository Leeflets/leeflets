<?php

namespace Leeflets\Core\Library\Form;

class Number extends Control {
	function html_middle() {
		?>
		<input type="number" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
		<?php
	}
}