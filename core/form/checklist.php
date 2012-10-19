<?php
class LF_Form_Checklist extends LF_Form_Select {
    
    function html($print = false) {
        if (!$print) ob_start();
        ?>

        <div class="field field-checklist field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <label class="prime"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            <ul>
                <?php
                foreach ($this->options as $val => $txt) :
                    $html_id = htmlentities($this->id . '_' . $this->slug($val));
                    ?>
                    <li class="option-<?php echo $val; ?>">
                        <input type="checkbox" class="checkbox" name="<?php echo $this->name ?>[]"
                            id="<?php echo $html_id ?>" value="<?php echo $val ?>"
                            <?php echo ( ( $val == $this->value ) || ( is_array($this->value) && in_array($val, $this->value) ) ) ? 'checked="checked"' : '' ?> />
                        <label for="<?php echo $html_id ?>"><?php echo $txt ?></label>
                    </li>
                    <?php
                endforeach;
                ?>
            </ul>
            <?php $this->display_tip(); $this->display_errors(); ?>
        </div>
        
        <?php
        if (!$print) return ob_get_clean();
    }


    function table($print = false) {
        if ($this->hide_in_table) return;
        if (!$print) ob_start();
        ?>
        
        <tr>
            <th style="text-align: left; vertical-align: top;"><?php echo $this->lbl; ?>:</th>
            <td><?php echo ($this->value) ? nl2br(htmlentities(join(', ', $this->value), null, $this->form->encoding)) : ''; ?></td>
        </tr>
            
        <?php
        if (!$print) return ob_get_clean();
    }
}
