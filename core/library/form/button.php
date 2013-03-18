<?php
namespace Leeflets\Form;

class Button extends LF_Form_Element {
    function __construct( $parent, $id, $args = array() ) {
    	$this->special_args( 'button-type', $args );

    	parent::__construct( $parent, $id, $args );
	}

	function html() {
		?>

		<button <?php echo $this->atts_html(); ?>><?php echo $this->esc_html( $this->value ); ?></button>
		
		<?php
	}
}