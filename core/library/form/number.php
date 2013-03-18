<?php
namespace Leeflets\Library\Form;

class LF_Form_Number extends LF_Form_Control {
	function html_middle() {
		?>
		<input type="number" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
		<?php
	}
}