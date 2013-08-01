<?php
namespace User\Template;

class Code extends \Leeflets\Template\Code {
	function setup_hooks() {
		$this->enqueue_style( 'koala-style', 'style.css' );
		$this->enqueue_style( 'koala-grid', 'subdiv.css' );
		$this->enqueue_style( 'googlefonts-Varela+Round', 'http://fonts.googleapis.com/css?family=Varela+Round', array(), null );
	}
}
