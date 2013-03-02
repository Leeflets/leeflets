<?php
class LF_Controller_Content extends LF_Controller {
	function edit() {
		$data = $this->template->get_content_data();

		$fieldset_ids = func_get_args();

		$form = $this->template->get_form( $fieldset_ids );

		$success = false;
		if ( $form->validate() ) {
			$values = $_POST;
			unset( $values['submit'] );
			unset( $values['submission-edit-content'] );
			unset( $values['_wysihtml5_mode'] );
			unset( $values['ajax'] );

			$data = array_merge( $data, $values );

			$this->template->set_content_data( $data );
		}
		elseif ( !$form->is_submitted() ) {
			$form->set_values( $data );
		}

		return compact( 'form', 'head' );
	}

	function upload() {
		$field_name = $_REQUEST['input-name'];
		$form = $this->template->get_single_field_form( $field_name );
		if ( !$form ) exit;

		$ids = LF_String::parse_array_representation( $field_name );
		$field = $form->vget_element( $ids );
		$files = $field->value;

		foreach ( $files as $i => $file ) {
			if ( !isset( $file['error'] ) ) continue;
			unset( $files[$i] );
		}

		$input_array = LF_String::convert_representation_to_array( $field_name, $files );

		$data = $this->template->get_content_data();
		$data = array_replace_recursive( $data, $input_array );

		$this->template->set_content_data( $data );

		$list = $field->get_file_list_html();
		echo json_encode( array( 'list' => $list ) );

		exit;

		// We don't want to modify the third-party UploadHandler class
		// right now, so we use this hacky way of getting the response 
		// out of it
		ob_start();
		$uh = new UploadHandler( array(
			'script_url' => $this->router->admin_url( '/content/upload/' ),
			'upload_url' => $this->router->admin_url( '/uploads/' ),
			'upload_dir' => $this->config->uploads_path . '/',
			'image_versions' => array()
		) );
		echo $response = ob_get_clean();

		$response = json_decode( $response );

		if ( 'DELETE' == $_SERVER['REQUEST_METHOD'] ) {
			if ( !isset( $response->success ) || !$response->success ) {
				exit;
			}

			$filename = '';
		}
		else {
			if ( !isset( $response->files[0] ) || isset( $response->files[0]->error ) ) {
				exit;
			}

			$filename = $response->files[0]->name;
		}

		$input_array = LF_String::convert_representation_to_array( $fieldname, $filename );

		$data = $this->template->get_content_data();
		$data = array_replace_recursive( $data, $input_array );

		$this->template->set_content_data( $data );

		exit;
	}

	function remove_upload( $field_name, $index ) {
		$ids = LF_String::parse_array_representation( $field_name );
		$files = $this->template->vget_content( $ids );

		if ( !isset( $files[$index] ) ) {
			echo json_encode( array( 'error' => 'File index not found.' ) );
			exit;
		}

		$file = $files[$index];

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
			unset( $files[$index] );
			$files_array = LF_String::convert_representation_to_array( $field_name, $files );
			$data = $this->template->get_content_data();
			$_data =& $data;
			foreach ( $ids as $id ) {
				$data =& $data[$id];
			}
			$data = $files;
			$this->template->set_content_data( $_data );

			echo json_encode( array( 'success' => true ) );
		}
		else {
			echo json_encode( array( 'error' => 'Some files could not be removed.' ) );
		}

		exit;
	}

	/*
	function test_resize() {
		$upload = new LF_Upload( $this->config, $this->router, $this->settings );
		
		$options = array( 'max_width' => 100, 'max_height' => 200, 'crop' => false );
		$upload->create_scaled_image( '17532_444925165251_3082814_n.jpg', 'thumbnail', $options );

		$options = array( 'max_width' => 300, 'max_height' => 100, 'crop' => false );
		$upload->create_scaled_image( '2674929469_e06797b5b6_b.jpg', 'button', $options );
		exit;
	}
	*/

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