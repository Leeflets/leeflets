<?php
class LF_Form_Select extends LF_Form_Control {
    public $options;
    
    function __construct( $parent, $id, $args = array() ) {
        $this->special_args( 'options', $args );
        parent::__construct( $parent, $id, $args );
    }
    
    function html_middle() {
        ?>
        
        <select <?php echo $this->atts_html(); ?>">
            <?php
            foreach ( $this->options as $value => $label ) {
                if ( $value == $this->value ) {
                    printf( '<option value="%s" selected="selected">%s</option>', $value, $label );
                }
                else {
                    printf( '<option value="%s">%s</option>', $value, $label );
                }
            }
            ?>
        </select>
        
        <?php
    }
}
