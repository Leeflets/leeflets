<?php
namespace Leeflets\Controller;

class Content extends \Leeflets\Controller {
	function edit() {
		$data = $this->content->get_data();

		$fieldset_ids = func_get_args();

		$form = $this->template->get_form( $fieldset_ids );
		
		// Set saved values, then overwite with post values
		// We do it this way to ensure that saved files meta data 
		// that is not submitted is saved forward
		$form->set_values( $data );
		if ( $form->is_submitted() ) {
			$form->set_values( $_POST );
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
		$ids = \Leeflets\String::parse_array_representation( $field_name );

		if ( !isset( $ids[0] ) ) {
			echo json_encode( array( 'error' => 'Could not parse form field name.' ) );
			exit;
		}

		$fieldset_id = array( $ids[0] );
		$form = $this->template->get_form( $fieldset_id );
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

		$input_array = \Leeflets\String::convert_representation_to_array( $field_name, $files );

		$data = $this->content->get_data();
		$data = array_replace_recursive( $data, $input_array );

		$this->content->set_data( $data );

		$list = $field->get_file_list_html();
		echo json_encode( array( 'list' => $list ) );

		exit;
	}

	function remove_upload( $field_name, $index ) {
		$field = $this->_get_single_field( $field_name );

		$ids = \Leeflets\String::parse_array_representation( $field_name );
		$files = $this->template->vget_content( $ids );

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

		if ( $all_removed ) {
			// Remove file from data and save
			if ( $field->has_multiple_values ) {
				unset( $files[$index] );
			}
			else {
				$files = array();
			}

			$files_array = \Leeflets\String::convert_representation_to_array( $field_name, $files );
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
		$this->template->write();
		$this->router->redirect( $this->router->admin_url( '?published=1' ) );
		exit;
	}
}