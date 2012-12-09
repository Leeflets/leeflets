<?php
class LF_Controller_Content extends LF_Controller {
	function edit() {
		$data = $this->template->get_content_data();

		$form = $this->template->get_form();

		$success = false;
		if ( $form->validate() ) {
			$values = $_POST;
			unset( $values['submit'] );
			unset( $values['submission-edit-content'] );

			$this->template->set_content_data( $values );

			$this->router->redirect( $this->router->admin_url( 'content/edit/?saved=1' ) );
			exit;
		}
		elseif ( !$form->is_submitted() ) {
			$form->set_values( $data );
		}

		return compact( 'form', 'head' );
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