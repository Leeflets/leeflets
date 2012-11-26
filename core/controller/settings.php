<?php
class LF_Controller_Settings extends LF_Controller {
	function edit() {
		if ( isset( $_POST['connection-type'] ) ) {
			if ( 'direct' == $_POST['connection-type'] ) {
				$_POST['connection-hostname'] = '';
				$_POST['connection-username'] = '';
				$_POST['connection-password'] = '';
			}
			elseif ( '' == $_POST['connection-password'] ) {
				$_POST['connection-password'] = $this->settings->data['connection-password'];
			}
		}

		$elements['site-meta'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'site-title' => array(
					'type' => 'text',
					'label' => 'Site Title',
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
		);

		$elements['privacy'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'site-visibility' => array(
					'type' => 'radiolist',
					'label' => 'Would you like search engines to index this site?',
					'value' => '1',
					'options' => array(
						'1' => 'Yes', 
			            '2' => 'No'
					)
				)
			)
		);

		$elements['analytics'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'analytics-code' => array(
					'type' => 'textarea',
					'label' => 'Analytics Code'
				)
			)
		);

		$elements['connection'] = $this->filesystem->get_connection_fields(
			array( $this, '_check_connection' ), false
		);

		$elements['buttons'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'submit' => array(
					'type' => 'button',
					'button-type' => 'submit',
					'value' => 'Save'
				)
			)
		);

		$form = new LF_Form( 'settings-form', array(
			'action' => $this->router->admin_url( 'settings/edit/' ),
			'elements' => $elements
		) );

		$error = '';
		if ( $form->validate() ) {
			$values = array_merge( $this->settings->data, $form->get_values() );
			unset( $values['submit'] );

			if ( $this->settings->write( $values, $this->filesystem ) ) {
				$this->router->redirect( $this->router->admin_url( 'settings/edit/?saved=1' ) );
				exit;
			}
			else {
				$error = 'Error saving the settings.';
			}
		}
		elseif ( !$form->is_submitted() ) {
			$form->set_values( $this->settings->data );
		}

		return compact( 'form', 'error' );
	}

	function _check_connection() {
		$class_name = $this->filesystem->get_class_name( $_POST['connection-type'] );
		$this->filesystem = new $class_name( $this->config, array(
			'connection_type' => $_POST['connection-type'],
			'hostname' => $_POST['connection-hostname'],
			'username' => $_POST['connection-username'],
			'password' => $_POST['connection-password']
		));

		return $this->filesystem->connect();
	}
}