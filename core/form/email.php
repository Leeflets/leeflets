<?php
class LF_Form_Email extends LF_Form_Control {
    function html_middle() {
        ?>
        <input type="email" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
        <?php
    }    
}
