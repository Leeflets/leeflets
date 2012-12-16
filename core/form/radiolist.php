<?php
class LF_Form_Radiolist extends LF_Form_Select {
    public $label_class = '';

    function __construct( $parent, $id, $args = array() ) {
        $this->special_args( 'label-class', $args );

        $this->label_class = trim( 'radio ' . $this->label_class );

        parent::__construct( $parent, $id, $args );
    }
   
    function html_middle() {
        ?>

        <?php
        foreach ( $this->options as $value => $label ) :
            $html_id = htmlspecialchars( $this->id . '-' . $this->slug( $value ) );
            ?>
            <label for="<?php echo $html_id; ?>" class="<?php echo $this->label_class; ?>">
                <input type="radio" <?php echo $this->atts_html(); ?>
                    id="<?php echo $html_id; ?>" value="<?php echo $value; ?>"
                    <?php echo ( ( $value == $this->value ) || ( is_array( $this->value ) && in_array( $value, $this->value ) ) ) ? 'checked="checked"' : '' ?> />
                <?php echo htmlspecialchars( $label ); ?>
            </label>
            <?php
        endforeach;
        ?>
        
        <?php
    }
}
