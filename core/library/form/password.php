<?php
namespace Leeflets\Form;

class Password extends Control {
	function html_middle() {
		?>
		<input type="password" <?php echo $this->atts_html(); ?> />
		<?php
	}
}