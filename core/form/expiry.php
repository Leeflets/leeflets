<?php
class LF_Form_Expiry extends LF_Form_Element {
    function html($print = false) {
        if (!$print) ob_start();
        ?>
        
        <div class="field field-text field-expiry field-<?php echo $this->id; echo (!empty($this->errors)) ? ' field-error' : '' ?>">
            <label class="prime" for="<?php echo $this->id ?>"><?php echo $this->lbl; echo ($this->req) ? '<span class="req">*</span>' : '' ?></label>
            <select name="<?php echo $this->name; ?>[month]"  id="<?php echo $this->id; ?>">
            <option value="">- Month -</option>
            <?php
            for ( $i = 1; $i <= 12; $i++ ) {
                $selected = ( isset( $this->value['month'] ) && $this->value['month'] == $i ) ? ' selected="selected"' : '';
                printf( '<option value="%s"%s>%1$s</option>', substr( '0' . $i, -2 ), $selected );
            }
            ?>
            </select>
            <select name="<?php echo $this->name; ?>[year]"  id="<?php echo $this->id; ?>">
            <option value="">- Year -</option>
            <?php
            $year = date( 'Y' );
            for ( $i = $year; $i <= $year + 10; $i++ ) {
                $selected = ( isset( $this->value['year'] ) && $this->value['year'] == $i ) ? ' selected="selected"' : '';
                printf( '<option value="%s"%s>%1$s</option>', $i, $selected );
            }
            ?>
            </select>
            <?php $this->display_tip(); $this->display_errors(); ?>
        </div>
            
        <?php
        if (!$print) return ob_get_clean();
    }    
}
