<?php
class LF_Form_Textarea extends LF_Form {
    function html($print = false) {
        if (!$print) ob_start();
        ?>
        
        <div class="field field-textarea field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <label class="prime" for="<?php echo $this->id ?>"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            <textarea class="textarea" id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>"<?php $this->display_placeholder(); ?>><?php echo htmlentities($this->value, null, $this->fieldset->form->encoding) ?></textarea>
            <?php $this->display_tip(); $this->display_errors(); ?>
        </div>
        
        <?php
        if (!$print) return ob_get_clean();
    }    
}
