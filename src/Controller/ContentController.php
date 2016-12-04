<?php

namespace Leeflets\Core\Controller;

use Leeflets\Core\Library\Content;
use Leeflets\Core\Library\Controller;
use Leeflets\Core\Library\String;

class ContentController extends Controller {
	function edit() {
		$data = $this->content->get_data();

		$fieldset_ids = func_get_args();

		$form = $this->template->get_form( $fieldset_ids );
		
		if ( $form->is_submitted() ) {
			$form->set_values( $_POST );
		}
		else {
			$form->set_values( $data );
		}

		$success = false;
		if ( $form->validate() ) {
			$values = $form->get_values();
			$data = array_merge( $data, $values );

			$this->content->set_data( $data );
		}

		$args = compact( 'form', 'head' );
		$args['page-title'] = 'Edit Content';

		return $args;
	}

	function _get_single_field( $field_name ) {
		$ids = String::parse_array_representation( $field_name );

		if ( !isset( $ids[0] ) ) {
			echo json_encode( array( 'error' => 'Could not parse form field name.' ) );
			exit;
		}

		$form = $this->template->get_form();
		if ( !$form ) {
			echo json_encode( array( 'error' => 'Form field not found (#1).' ) );
			exit;
		}

		$field = $form->vget_element( $ids );

		if ( !$field ) {
			echo json_encode( array( 'error' => 'Form field not found (#2).' ) );
			exit;
		}

		return $field;
	}

	function upload() {
		$field_name = $_REQUEST['input-name'];
		$field = $this->_get_single_field( $field_name );

		if ( !$field->has_multiple_values ) {
			$files = array( $field->value );
		}
		else {
			$files = $field->value;
		}

		foreach ( $files as $i => $file ) {
			if ( !isset( $file['error'] ) ) continue;
			unset( $files[$i] );
		}

		// If it's not a multi-file upload field, just store the single file
		if ( !$field->has_multiple_values && isset( $files[0] ) ) {
			$files = $files[0];
		}

		$input_array = String::convert_representation_to_array( $field_name, $files );

		$data = $this->content->get_data();
		$data = array_replace_recursive( $data, $input_array );

		$this->content->set_data( $data );

		$list = $field->get_file_list_html();
		echo json_encode( array( 'list' => $list ) );

		exit;
	}

	function remove_upload( $field_name, $index ) {
		$field = $this->_get_single_field( $field_name );

		$ids = String::parse_array_representation( $field_name );
		$data = $this->content->get_data();
		$template_content = new Content($data);
		$files = $template_content->vget( $ids );

		if ( $field->has_multiple_values ) {
			if ( !isset( $files[$index] ) ) {
				echo json_encode( array( 'error' => 'File index not found.' ) );
				exit;
			}

			$file = $files[$index];
		}
		else {
			$file = $files;
		}

		// If this is a sample image in the template, don't try remove the file
		if ( !isset( $file['in_template'] ) || !$file['in_template'] ) {
			// Try removing the files
			@unlink( $this->config->uploads_path . '/' . $file['path'] );
			if ( isset( $file['versions'] ) ) {
				foreach ( $file['versions'] as $version ) {
					@unlink( $this->config->uploads_path . '/' . $version['path'] );
				}
			}

			// Check if any of the files are still there
			$all_removed = true;
			if ( is_file( $this->config->uploads_path . '/' . $file['path'] ) ) {
				$all_removed = false;
			}
			elseif ( isset( $file['versions'] ) ) {
				foreach ( $file['versions'] as $version ) {
					if ( is_file( $this->config->uploads_path . '/' . $version['path'] ) ) {
						$all_removed = false;
					}
				}
			}
		}
		else {
			$all_removed = true;
		}

		if ( $all_removed ) {
			// Remove file from data and save
			if ( $field->has_multiple_values ) {
				unset( $files[$index] );
			}
			else {
				$files = array();
			}

			$files_array = String::convert_representation_to_array( $field_name, $files );
			$data = $this->content->get_data();
			$_data =& $data;
			foreach ( $ids as $id ) {
				$data =& $data[$id];
			}
			$data = $files;
			$this->content->set_data( $_data );

			echo json_encode( array( 'success' => true ) );
		}
		else {
			echo json_encode( array( 'error' => 'Some files could not be removed.' ) );
		}

		exit;
	}

	function view() {
		echo $this->template->render();
		exit;
	}

	function publish() {
		$result = $this->template->write();
		if ( $this->router->is_ajax ) {
			echo $result;
		}
		else {
			$this->router->redirect( $this->router->adminUrl( '?published=1' ) );
		}
		exit;
	}
}