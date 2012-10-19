<?php
class LF_Form_Links extends LF_Form {
    function html($print = false) {
        if (!$print) ob_start();
        
        for ($i = 0; $i <= 2; $i++) {
            foreach (array('title', 'url') as $var) {
                if (!isset($this->value[$i][$var])) {
                    $this->value[$i][$var] = '';
                }
            }
        }
        ?>
        
        <div class="field field-link field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <label class="prime" for="<?php echo $this->id ?>"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            <?php for ($i = 0; $i <= 2; $i++) : ?>
            <div class="field-group">
                <input type="text" class="text title" id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>[<?php echo $i; ?>][title]"<?php echo ( !$this->autocomplete ) ? ' autocomplete="off"' : ''; ?> value="<?php echo htmlentities($this->value[$i]['title'], null, $this->form->encoding) ?>" placeholder="Link Title" />
                <input type="text" class="text url" id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>[<?php echo $i; ?>][url]"<?php echo ( !$this->autocomplete ) ? ' autocomplete="off"' : ''; ?> value="<?php echo htmlentities($this->value[$i]['url'], null, $this->form->encoding) ?>" placeholder="Link Address" />
            </div>
            <?php endfor; ?>
            <?php $this->display_tip(); $this->display_errors(); ?>
        </div>
            
        <?php
        if (!$print) return ob_get_clean();
    }    
}
