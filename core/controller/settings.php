<?php
class LF_Controller_Settings extends LF_Controller {
	function edit() {
		$path = $this->config->data_path . '/settings.json.php';
		$file = new LF_Data_File( $path, $this->config, $this->filesystem );
		$data = $file->read();

		if ( false === $data ) {
			$data = array();
		}

		$form = new LF_Form( 'settings-form', array(
			'action' => $this->router->admin_url( 'settings/edit/' ),
			'elements' => array(
				'site-meta' => array(
					'type' => 'fieldset',
					'elements' => array(
						'site-title' => array(
							'type' => 'text',
							'label' => 'Site Title',
							'required' => true,
							'autofocus' => true,
						),
						'site-author' => array(
							'type' => 'text',
							'label' => 'Site Author'
						),
						'site-description' => array(
							'type' => 'text',
							'label' => 'Site Description'
						),
					)
				),
				'privacy' => array(
					'type' => 'fieldset',
					'elements' => array(
						'site-visibility' => array(
							'type' => 'radiolist',
							'label' => 'Would you like search engines to index this site?',
							'options' => array(
								'1' => 'Yes', 
					            '2' => 'No'
							)
						)
					)
				),
				'analytics' => array(
					'type' => 'fieldset',
					'elements' => array(
						'analytics-code' => array(
							'type' => 'textarea',
							'label' => 'Analytics Code'
						)
					)
				),
				'buttons' => array(
					'type' => 'fieldset',
					'elements' => array(
						'submit' => array(
							'type' => 'button',
							'button-type' => 'submit',
							'value' => 'Save'
						)
					)
				)
			)
		) );

		if ( $form->validate() ) {
			$values = $form->get_values();

			$file->write( $values );

			$this->router->redirect( $this->router->admin_url( 'settings/edit/?saved=1' ) );
			exit;
		}
		else {
			$form->set_values( $data );
		}

		return compact( 'form' );
	}
}