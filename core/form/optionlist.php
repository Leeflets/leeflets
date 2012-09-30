<?php
class LF_Form_Optionlist extends LF_Form_Select {
    var $hide_lbl;
   
    function html($print = false) {
        if (!$print) ob_start();
        ?>

        <div class="field field-optionlist field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <?php if (!$this->hide_lbl) : ?>
            <label class="prime"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            <?php endif; ?>
            <ul>
                <?php
                foreach ($this->options as $val => $txt) :
                    $html_id = htmlentities($this->id . '_' . $this->slug($val));
                    ?>
                    <li class="option-<?php echo $val; ?>">
                        <input type="radio" class="radio" name="<?php echo $this->name ?>"
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
}
