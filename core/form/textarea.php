<?php
class LF_Form_Textarea extends LF_Form_Element {
    function html_middle() {
        ?>
        <textarea class="textarea" <?php echo $this->atts_html(); ?>><?php echo htmlentities($this->value, null, $this->form->encoding) ?></textarea>
        <?php
    }    
}
