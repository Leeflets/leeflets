<?php
class LF_Controller_Content extends LF_Controller {
	function edit() {
		$data_file = $this->template->get_content_data_file();
		$data = $data_file->read();

		$form = $this->template->get_form();

		$success = false;
		if ( $form->validate() ) {
			$values = $form->get_values();
			unset( $values['submit'] );

			$data_file->write( $values );

			$this->router->redirect( $this->router->admin_url( 'content/edit/?saved=1' ) );
			exit;
		}
		else {
			$form->set_values( $data );
		}

		return compact( 'form' );
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