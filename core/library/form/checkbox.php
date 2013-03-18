<?php
namespace Leeflets\Library\Form;

class LF_Form_Checkbox extends LF_Form_Control {

    function html() {
    	?>
        <div class="control-group <?php echo $this->class; if ( !empty( $this->errors ) ) echo ' error'; ?>" <?php echo $this->style_att(); ?>>
            <?php if ( $this->label != '' ) : ?>
            <label class="checkbox">
            <?php endif; ?>
	        
	        <input type="checkbox" value="1" 
	            <?php echo $this->atts_html(); ?>
	            <?php echo ( $this->value ) ? 'checked="checked"' : '' ?>
	        />
	        
	        <?php if ( $this->label != '' ) : ?>
	        <?php echo $this->label; echo ($this->required) ? '<span class="req">*</span>' : '' ?>
        	</label>
        	<?php endif; ?>
            
            <?php $this->errors_html(); $this->tip_html(); ?>
        </div>
        <?php
    }
}
