<?php
namespace Leeflets\Library\Form;

class LF_Form extends LF_Form_Element_Collection {
    public $config, $router, $settings;
    
    function __construct( LF_Config $config, LF_Router $router, LF_Settings $settings, $id, $args = array() ) {
        $this->config = $config;
        $this->router = $router;
        $this->settings = $settings;

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

    function is_submitted() {
        return isset( $_REQUEST['submission-' . $this->id] );
    }

    function is_errors() {
        return !empty( $this->errors );
    }

    function validate() {
        if ( !$this->is_submitted() ) {
            return false;
        }

        parent::validate();
        
        return empty( $this->errors );
    }

    function errors() {
        return $this->errors;
    }
}
