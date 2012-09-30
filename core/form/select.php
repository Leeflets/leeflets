<?php
class LF_Form_Select extends LF_Form {
    var $options;
    
    function __construct($id, $attr = array()) {
        parent::__construct($id, $attr);

        $attr = Base_Form::override_defaults(array(
            'options' => array()
        ), $attr);
        
        foreach ($attr as $var => $val)
            $this->$var = $val;
    }
    
    function html($print = false) {
        if (!$print) ob_start();
        ?>
        
        <div class="field field-select field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <label class="prime" for="<?php echo $this->id ?>"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            <select name="<?php echo $this->name ?>" id="<?php echo $this->id ?>">
                <?php
                foreach ($this->options as $val => $txt) {
                    if ($val == $this->value) {
                        printf('<option value="%s" selected="selected">%s</option>', $val, $txt);
                    }
                    else {
                        printf('<option value="%s">%s</option>', $val, $txt);
                    }
                }
                ?>
            </select>
            <?php $this->display_tip(); $this->display_errors(); ?>
        </div>
        
        <?php
        if (!$print) return ob_get_clean();
    }
}
