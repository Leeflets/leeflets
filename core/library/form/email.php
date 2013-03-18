<?php
namespace Leeflets\Form;

class Email extends Control {
    function __construct( $parent, $id, $args = array() ) {
    	if ( !isset( $args['validation'] ) ) {
			$args['validation'] = array();
    	}

    	array_unshift( $args['validation'], array(
			'callback' => array( $this, 'valid_email' ),
			'msg' => 'Sorry, that is not a valid email address.'
    	));

        parent::__construct( $parent, $id, $args );
    }
   
    function html_middle() {
        ?>
        <input type="email" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
        <?php
    }

    function valid_email( $value ) {
    	return LF_String::valid_email( $value );
    }
}
