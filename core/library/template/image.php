<?php
namespace Leeflets\Template;

class Image {
	private $router;

	function __construct( \Leeflets\Router $router ) {
		$this->router = $router;
	}

	public function get_atts() {
		return $this->vget_atts( func_get_args() );
	}

	public function vget_atts( $args ) {
		$version = array_shift( $args );

		switch ( count( $args ) ) {
			case 0:
				return false;
			case 1:
				if ( !is_array( $args[0] ) ) {
					return false;
				}
				$image = $args[0];
				break;
			default:
				$image = $this->vget_content( $args );
		}
		
		if ( isset( $image['versions'][$version] ) ) {
			$image = $image['versions'][$version];
		}

		if ( !isset( $image['path'] ) || !isset( $image['width'] ) || !isset( $image['height'] ) ) {
			return false;
		}

		return array( $this->get_uploads_url( $image['path'] ), $image['width'], $image['height'] );
	}

	public function out() {
		$tag = $this->vget( func_get_args() );
		if ( $tag ) {
			echo $tag;
		}
	}

	public function get() {
		return $this->vget( func_get_args() );
	}

	public function vget( $args ) {
		$atts = $this->vget_atts( $args );
		if ( !$atts ) {
			return false;
		}

		list( $src, $w, $h ) = $atts;
		return sprintf( '<img src="%s" width="%s" height="%s" alt="" />', $src, $w, $h );
	}

}