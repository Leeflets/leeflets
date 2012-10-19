<?php
class LF_Form_Password extends LF_Form_Control {
	function html_middle() {
		?>
		<input type="password" <?php echo $this->atts_html(); ?> />
		<?php
	}
}