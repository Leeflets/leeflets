<?php
class LF_Form_Checkbox extends LF_Form_Control {
    function html_middle() {
        ?>
        <input type="checkbox" value="1" 
            <?php echo $this->atts_html(); ?>
            <?php echo ( $this->value ) ? 'checked="checked"' : '' ?>
        />
        <?php
    }
}
