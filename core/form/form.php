<?php
class LF_Form extends LF_Form_Element_Collection {
    
    function __construct( $id, $args = array() ) {
        parent::__construct( null, $id, $args );

        $this->atts = array_merge( array(
            'method' => 'POST',
            'action' => '',
            'novalidate' => true,
            'accept-charset' => 'UTF-8',
            'class' => ''
        ), $this->atts );
    }

    function get_encoding() {
        if ( isset( $this->atts['accept-charset'] ) && '' != $this->atts['accept-charset'] ) {
            return $this->atts['accept-charset'];
        }
        else {
            return 'UTF-8';
        }
    }

    function html() {
        ?>

        <form id="<?php echo $this->id ?>" <?php echo $this->atts_html(); ?>>
            <input type="hidden" name="submission-<?php echo $this->id ?>" value="1" />
            
            <?php
            foreach ( $this->elements as $el ) {
                $el->html();
            }
            ?>
            
        </form>
        
        <?php
    }

    function values( $values = null ) {
        if ( is_null( $values ) ) {
            return $this->collection->values();
        }
        else {
            $this->collection->values( $values );
        }
    }
    
    function is_submitted() {
        return isset( $_REQUEST['submission-' . $this->id] );
    }

    function is_errors() {
        return !empty( $this->errors );
    }

    function validate() {
        if ( !$this->is_submitted() ) return false;
        parent::validate();
        return empty( $this->errors );
    }

    function errors() {
        return $this->errors;
    }
}
