<?php
class LF_Form_Literal extends LF_Form_Element {
    function html($print = false) {
        if (!$print) ob_start();
        ?>
        
        <div class="field field-literal field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <label class="prime"><?php echo $this->lbl; ?></label>
            <div id="<?php echo $this->id; ?>" class="value">
                <?php echo htmlentities($this->value, null, $this->form->encoding) ?>
            </div>
        </div>
            
        <?php
        if (!$print) return ob_get_clean();
    }    
}
