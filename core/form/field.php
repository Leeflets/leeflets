<?php
class LF_Form {
    var $id;
    var $req;
    var $validation;
    var $name;
    var $lbl;
    var $tip;
    var $autocomplete;
    var $errors;
    var $value;
    var $hide_in_table;
    var $placeholder;
    var $fieldset;
    
    function __construct($id, $attr = array()) {
        $this->id = $id;
        
        $attr = Base_Form::override_defaults(array(
            'req' => false,
            'validation' => null,
            'name' => $id,
            'lbl' => '',
            'tip' => '',
            'errors' => array(),
            'value' => '',
            'hide_in_table' => false,
            'autocomplete' => true,
            'placeholder' => ''
        ), $attr);
        
        foreach ($attr as $var => $val)
            $this->$var = $val;
    }
    
    function validate() {
        $this->errors = array();
        
        if ($this->req && $this->value == '') {
            $this->errors[] = $this->req;
            
            return $this->errors;
        }
        
        if ( $this->value != '' ) {
            $this->exec_validation_funcs();
        }
        
        return $this->errors;
    }
    
    function exec_validation_funcs() {
        if (empty($this->validation)) return;
            
        $keys = array_keys($this->validation);

        if (is_int($keys[0])) {
            foreach ($this->validation as $validation) {
                $this->call_validation_func($validation);
            }
        }
        else {
            $this->call_validation_func($this->validation);
        }
    }
    
    function call_validation_func($validation) {
        extract($validation);
        
        // This is a function call validation
        if (isset($func)) {
            if (!isset($args)) {
                $args = array($this->value);
            }
            else {
                array_unshift($args, $this->value);
            }

            if ( is_string( $func ) ) {
                if ( is_callable( array( $this, $func ) ) ) {
                    $func = array( $this, $func );
                }
                elseif ( is_callable( array( 'LF_Form_Validation', $func ) ) ) {
                    $func = array( 'LF_Form_Validation', $func );
                }
            }

            if ( !call_user_func_array($func, $args) ) {
                if ( isset( $msg ) ) $this->errors[] = $msg;
            }
        }
        elseif ( isset($regex) && !preg_match($regex, $this->value) ) {
            if ( isset( $msg ) ) $this->errors[] = $msg;
        }
    }
    
    function table($print = false) {
        if ($this->hide_in_table) return;
        if (!$print) ob_start();
        ?>
        
        <tr>
            <th style="text-align: left; vertical-align: top;"><?php echo $this->lbl; ?></th>
            <td><?php echo nl2br(htmlentities($this->value, null, $this->fieldset->form->encoding)); ?></td>
        </tr>
            
        <?php
        if (!$print) return ob_get_clean();
    }
    
    function display_table() {
        $this->html(true);
    }
    
    function html($print = false) {
        if (!$print) ob_start();
        ?>
        
        <div class="field field-text field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <label class="prime" for="<?php echo $this->id ?>"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            <input type="text" class="text" id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>"<?php echo ( !$this->autocomplete ) ? ' autocomplete="off"' : ''; ?> value="<?php echo htmlentities($this->value, null, $this->fieldset->form->encoding) ?>"<?php $this->display_placeholder(); ?> />
            <?php $this->display_tip(); $this->display_errors(); ?>
        </div>
            
        <?php
        if (!$print) return ob_get_clean();
    }
    
    function display() {
        $this->html(true);
    }
    
    function display_placeholder() {
        if ($this->placeholder) {
            echo ' placeholder="' . esc_attr($this->placeholder) . '"';
        }
    }
    
    function display_tip() {
        if ($this->tip) {
            echo '<p class="tip">', $this->tip, '</p>';
        }
    }
    
    function display_errors() {
        if (empty($this->errors))
            return '';
        
        foreach ($this->errors as $error) {
            ?>
            <p class="error"><?php echo $error; ?></p>
            <?php
        }
    }

    function slug($str) {
       return preg_replace('@[^a-z0-9-]+@', '-', strtolower($str));
    }
}
