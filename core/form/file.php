<?php
class LF_Form_File extends LF_Form_Element {
    var $upload_errors;
    var $on_validate_complete;
    var $upload_path;
    var $multiple;
    
    function __construct($id, $attr = array()) {
        parent::__construct($id, $attr);

        if (!isset($attr['upload_errors'])) {
            $attr['upload_errors'] = array();
        }

        $this->upload_errors = Base_Form::override_defaults(
            $this->default_upload_errors(),
            $attr['upload_errors']
        );

        $attr = Base_Form::override_defaults(array(
            'on_validate_complete' => '',
            'upload_path' => wp_upload_dir(),
            'multiple' => false
        ), $attr);

        foreach ($attr as $var => $val)
            $this->$var = $val;
    }

    function table($print = false) {
        if ($this->hide_in_table) return;
        if (!$print) ob_start();
        ?>
        
        <tr>
            <th style="text-align: left; vertical-align: top;"><?php echo $this->lbl; ?>:</th>
            <td><?php echo nl2br(htmlentities($this->value, null, $this->form->encoding)); ?></td>
        </tr>
            
        <?php
        if (!$print) return ob_get_clean();
    }
    
    function html($print = false) {
        if (!$print) ob_start();
        ?>

        <div class="field field-file field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <label for="<?php echo $this->id ?>" class="prime"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            
            <?php if ($this->multiple) : ?>
            
                <input type="file" id="<?php echo $this->id ?>" size="36" name="<?php echo $this->name ?>[]" multiple="multiple" class="file" />
                
                <?php
                if ($this->value) :
                    foreach ($this->value as $file) :
                        ?>
                      
                        <input type="hidden" name="<?php echo $this->name ?>_tmp[]" value="<?php echo $file; ?>" />
                      
                        <?php
                    endforeach;
                    ?>
                    
                    <p class="tip current-file"><span>Current Files:</span> <?php echo implode(', ', $this->value); ?></p>
                    
                    <?php
                endif;
                ?>
            
            <?php else: ?>
            
                <input type="file" id="<?php echo $this->id ?>" size="36" name="<?php echo $this->name ?>" class="file" />
                
                <?php if ($this->value) : ?>

                    <input type="hidden" name="<?php echo $this->name ?>_tmp" value="<?php echo $this->value; ?>" />
                    <p class="tip current-file"><span>Current File:</span> <?php echo $this->value; ?></p>

                <?php endif; ?>
            
            <?php endif; ?>
            
            <?php $this->display_tip(); $this->display_errors(); ?>
        </div>
        
        <?php
        if (!$print) return ob_get_clean();
    }
    
    function validate() {
        $this->errors = array();
        
        if ($this->multiple) {
            $is_no_file = (!isset($_FILES[$this->id]) || $_FILES[$this->id]['error'][0] == UPLOAD_ERR_NO_FILE);
        }
        else {
            $is_no_file = (!isset($_FILES[$this->id]) || $_FILES[$this->id]['error'] == UPLOAD_ERR_NO_FILE);
        }

        if ($this->req && $is_no_file) {
            $this->validate_complete_callback();
            return $this->errors;
        }
        
        $files = array();
        if ($this->multiple) {
            $vars = array_keys($_FILES[$this->id]);
            foreach ($_FILES[$this->id]['name'] as $i => $data) {
                foreach ($vars as $var) {
                    $files[$i][$var] = $_FILES[$this->id][$var][$i];
                }
            }
            $this->value = $files;
        }
        else {
            $files = array($_FILES[$this->id]);
            $this->value = $_FILES[$this->id];
        }

        if ($is_no_file) {
            $this->validate_complete_callback();
            return $this->errors;
        }
        
        foreach ($files as $file) {
            $errno = $file['error'];
            if (isset($this->upload_errors[$errno])) {
                $this->errors[] = $this->upload_errors[$errno];
            }
        }
        
        $this->exec_validation_funcs();
        
        $this->validate_complete_callback();
        
        return $this->errors;
    }

    function save_uploaded_file() {
        if ( !empty( $this->errors ) ) {
            $this->value = ($this->multiple) ? array() : '';
            return;
        }

        if ($this->multiple) {
            $files = $this->value;
        }
        else {
            $files = array($this->value);
        }
        
        foreach ($files as $file) {
            
            if ( !$file || $file['error'] == UPLOAD_ERR_NO_FILE ) {
                if (!isset($_POST[$this->id . '_tmp'])) {
                    if ( $this->req ) $this->errors[] = $this->req;
                    $this->value = ($this->multiple) ? array() : '';
                    return;
                }
                else {
                    $this->value = $_POST[$this->id . '_tmp'];
                    return;
                }
            }
            elseif (isset($_POST[$this->id . '_tmp'])) {
                if ($this->multiple) {
                    $_files = $_POST[$this->id . '_tmp'];
                }
                else {
                    $_files = array($_POST[$this->id . '_tmp']);
                }
                
                foreach ($_files as $_file) {
                    @unlink($this->upload_path . '/' . $_file);
                }
                
                unset($_POST[$this->id . '_tmp']);
            }
    
            $new_file = wp_unique_filename($this->upload_path, sanitize_file_name($file['name']));
            $new_path = $this->upload_path . '/' . $new_file;
    
            if (!move_uploaded_file($file['tmp_name'], $new_path)) {
                $this->errors[] = $this->upload_errors['moving'];
                foreach ($new_files as $file) {
                    @unlink($this->upload_path . '/' . $file);
                }
                return;
            }
            
            $new_files[] = $new_file;
        }
        
        if ($this->multiple) {
            $this->value = $new_files;
        }
        else {
            $this->value = array_pop($new_files);
        }
    }
    
    function validate_complete_callback() {
        $this->save_uploaded_file();
        
        if (is_callable($this->on_validate_complete)) {
            call_user_func($this->on_validate_complete, $this);
        }
    }
    
    function default_upload_errors() {
        $uperr = array();
        
        // The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>.
        $uperr[UPLOAD_ERR_INI_SIZE] = 'Sorry, your file exceeds the maximum allowable size of ' . ini_get('upload_max_filesize') . '.';
        
        // The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form.
        $uperr[UPLOAD_ERR_FORM_SIZE] = 'Sorry, your form submission exceeds the maximum allowable size of ' . ini_get('post_max_size') . '.';
        
        // The uploaded file was only partially uploaded.
        $uperr[UPLOAD_ERR_PARTIAL] = 'Sorry, your file was only partially uploaded. Please try again.';
        
        // Missing a temporary folder
        $uperr[UPLOAD_ERR_NO_TMP_DIR] = 'Sorry, there was a technical problem uploading your file (no tmp directory).';
        
        // Failed to write file to disk
        $uperr[UPLOAD_ERR_CANT_WRITE] = 'Sorry, there was a technical problem uploading your file (can\'t write).';
        
        // File upload stopped by extension
        $uperr[UPLOAD_ERR_EXTENSION] = 'Sorry, there was a technical problem uploading your file (extension).';

        // Moving file to another directory failed
        $uperr['moving'] = 'Sorry, there was a problem moving your uploaded your file.';
        
        return $uperr;
    }

    function parse_filename($filename) {
        $pos = strrpos($filename, '.');
        $name = substr($filename, 0, $pos);
        $ext = substr($filename, $pos+1);
        return array($name,$ext);
    }
        
    function validate_type($value, $types) {
        if ($this->multiple) {
            $files = $value;
        }
        else {
            $files = array($value);
        }
        
        foreach ($files as $file) {
            list($name, $ext) = $this->parse_filename($file['name']);
            if (!in_array($ext, $types)) return false;
        }
        
        return true;
    }
    
    function validate_file_size($value, $max_size) {
        if ($this->multiple) {
            $files = $value;
        }
        else {
            $files = array($value);
        }
        
        foreach ($files as $file) {
            $max_size = $max_size * 1024 * 1024;
            if ($file['size'] > $max_size) {
                return false;
            }
        }
        
        return true;
    }
}
