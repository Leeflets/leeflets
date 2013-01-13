<?php
class My_New_Menu_Item {
	function __construct( $hook ) {
		$hook->add( 'lf_primary_menu', array( $this, 'add_menu_item' ) );
	}

	function add_menu_item( $menu ) {
		$item = array(
			array(
				'text' => 'Backups',
				'atts' => array(
					'href' => ''
				)
			)
		);
		array_splice( $menu, 3, 0, $item );
		return $menu;
	}
}

new My_New_Menu_Item( $this->hook );
