<?php
class LF_Form_Radiolist extends LF_Form_Select {
   
    function html_middle() {
        ?>

        <ul>
            <?php
            foreach ( $this->options as $value => $label ) :
                $html_id = htmlspecialchars( $this->id . '-' . $this->slug( $value ) );
                ?>
                <li class="option-<?php echo $this->slug( $value ); ?>">
                    <input type="radio" <?php echo $this->atts_html(); ?>
                        id="<?php echo $html_id; ?>" value="<?php echo $value; ?>"
                        <?php echo ( ( $value == $this->value ) || ( is_array( $this->value ) && in_array( $value, $this->value ) ) ) ? 'checked="checked"' : '' ?> />
                    <label for="<?php echo $html_id; ?>"><?php echo htmlspecialchars( $label ); ?></label>
                </li>
                <?php
            endforeach;
            ?>
        </ul>
        
        <?php
    }
}
