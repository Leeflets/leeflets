<?php
namespace Leeflets\Library\Form;

class Fieldset extends LF_Form_Element_Collection {
    public $title, $description;
    
    function __construct( $parent, $id, $args = array() ) {
        $this->special_args( 'title, description', $args );

        parent::__construct( $parent, $id, $args );
    }

    function header() {
        $header = '';
        if ( !is_null( $this->title ) ) {
            $header .= '<h1>' . $this->esc_html( $this->title ) . "</h1>\r\n";
        }

        if ( !is_null( $this->description ) ) {
            $header .= '<p>' . $this->esc_html( $this->description ) . "</p>\r\n";
        }

        if ( $header ) {
            echo "<header>\r\n" . $header . "</header>\r\n";
        }
    }

    function html() {
        ?>

        <fieldset <?php echo $this->atts_html(); ?>>
            <?php $this->header() ?>
            
            <?php
            foreach ( $this->elements as $el ) {
                $el->html();
            }
            ?>
            
        </fieldset>
        
        <?php
    }
}
