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
		// We don't want to modify the third-party UploadHandler class
		// right now, so we use this hacky way of getting the response 
		// out of it
		ob_start();
		$uh = new UploadHandler( array(
			'script_url' => $this->router->admin_url( '/content/upload/' ),
			'upload_url' => $this->router->admin_url( '/uploads/' ),
			'upload_dir' => $this->config->uploads_path . '/'
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
		
		$input_array = LF_String::convert_representation_to_array( $_REQUEST['input-name'], $filename );

		$data = $this->template->get_content_data();
		$data = array_replace_recursive( $data, $input_array );

		$this->template->set_content_data( $data );

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