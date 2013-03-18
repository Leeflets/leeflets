<?php
namespace Leeflets\Form;

class Url extends LF_Form_Control {
	function html_middle() {
		?>
		<input type="url" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
		<?php
	}
}