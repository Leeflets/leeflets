<?php
class LF_Form_Text extends LF_Form_Control {
	function html_middle() {
		?>
		<input type="text" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
		<?php
	}
}