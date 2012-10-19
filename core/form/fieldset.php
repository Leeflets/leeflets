<?php
class LF_Form_Fieldset extends LF_Form_Element_Collection {
    public $legend = '';
    
    function __construct( $form, $id, $args = array() ) {
        if ( isset( $args['legend'] ) ) {
            $this->legend = $args['legend'];
            unset( $args['legend'] );
        }

        parent::__construct( $form, $id, $args );
    }

    function html() {
        ?>

        <fieldset <?php echo $this->atts_html(); ?>>
            <?php if ($this->legend) : ?>
            <legend><?php echo $this->legend ?></legend>
            <?php endif; ?>
            
            <?php
            foreach ( $this->elements as $el ) {
                $el->html();
            }
            ?>
            
        </fieldset>
        
        <?php
    }
}
