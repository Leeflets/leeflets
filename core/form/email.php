<?php
class LF_Form_Email extends LF_Form_Text {
    function html_middle() {
        ?>
        <input type="email" class="text email" id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>" value="<?php echo htmlentities($this->value, null, $this->form->encoding) ?>"<?php $this->display_placeholder(); ?> />
        <?php
    }    
}
