<?php
class LF_Form_Wysiwyg extends LF_Form_Control {
    function html_middle() {
        ?>
        <textarea class="textarea redactor" <?php echo $this->atts_html(); ?>><?php echo $this->value_html(); ?></textarea>
        <?php
    }    
}
