<?php
namespace Leeflets\Template;

class Settings {
	private $settings;
	
	function __construct( \Leeflets\Settings $settings ) {
		$this->settings = $settings;
	}

	function out() {
		echo $this->settings->vget( func_get_args() );
	}

	function get() {
		return $this->settings->vget( func_get_args() );
	}
}
