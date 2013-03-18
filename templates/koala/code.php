<?php
class LF_Template_Code extends LF_Template_Code_Base {
	function setup_hooks() {
		$this->enqueue_style( 'koala-style', 'style.css' );
		$this->enqueue_style( 'koala-grid', 'subdiv.css' );
		$this->enqueue_style( 'googlefonts-Varela+Round', 'http://fonts.googleapis.com/css?family=Varela+Round' );
	}
}
