<?php
namespace Leeflets\Form;

class Text extends Control {
	function html_middle() {
		?>
		<input type="text" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
		<?php
	}
}