<?php

namespace Leeflets\Core\Library\Form;

class Url extends Control {
	function html_middle() {
		?>
		<input type="url" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
		<?php
	}
}