<?php
namespace Leeflets\Form;

class Date extends Control {
    public $date_format = 'mm/dd/yyyy';

    function __construct( $parent, $id, $args = array() ) {
        /*
        TODO: Validate date when submitted
        if ( !isset( $args['data-date-format'] ) ) {
            $this->date_format = $args['data-date-format'];
        }

    	if ( !isset( $args['validation'] ) ) {
			$args['validation'] = array();
    	}

    	array_unshift( $args['validation'], array(
			'callback' => array( $this, 'valid_date' ),
			'msg' => 'Sorry, that date is invalid.'
    	));
        */

        if ( !isset( $args['class'] ) ) {
            $args['class'] = '';
        }

        $args['class'] = trim( $args['class'] . ' datepicker' );

        parent::__construct( $parent, $id, $args );
    }
   
    function html_middle() {
        ?>
        <input type="text" <?php echo $this->atts_html(); ?> value="<?php echo $this->value_att(); ?>" />
        <?php
    }

    function valid_date( $value ) {
    }
}
