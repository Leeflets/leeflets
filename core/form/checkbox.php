<?php
class LF_Form_Checkbox extends LF_Form {
    function table($print = false) {
        if ($this->hide_in_table) return;
        if (!$print) ob_start();
        ?>
        
        <tr>
            <th style="text-align: left; vertical-align: top;"><?php echo $this->lbl; ?></th>
            <td><?php echo ($this->value) ? 'Yes' : 'No'; ?></td>
        </tr>
        
        <?php
        if (!$print) return ob_get_clean();
    }
    
    function html($print = false) {
        if (!$print) ob_start();
        ?>
        
        <div class="field field-checkbox field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <input type="checkbox" class="checkbox" name="<?php echo $this->name ?>"
                id="<?php echo $this->id; ?>" value="1"
                <?php echo ($this->value) ? 'checked="checked"' : '' ?> />
            <label class="prime" for="<?php echo $this->id ?>"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            <?php $this->display_tip(); $this->display_errors(); ?>
        </div>
        
        <?php
        if (!$print) return ob_get_clean();
    }    
}
