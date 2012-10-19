<?php
class LF_Controller_User extends LF_Controller {
	function login() {
		
		$form = new LF_Form( 'login-form', array(
			'elements' => array(
				'credentials' => array(
					'type' => 'fieldset',
					'elements' => array(
						'username' => array(
							'type' => 'text',
							'label' => 'Username',
							'required' => true,
							'validation' => array(
								array(
									'callback' => array( $this, '_check_username' ),
									'msg' => 'Sorry, that is not the correct username.'
								)
							)
						),
						'password' => array(
							'type' => 'password',
							'label' => 'Password',
							'required' => true,
							'validation' => array(
								array(
									'callback' => array( $this, '_check_password' ),
									'msg' => 'Sorry, that is not the correct password.'
								)
							)
						)
					)
				),
				'buttons' => array(
					'type' => 'fieldset',
					'elements' => array(
						'submit' => array(
							'type' => 'button',
							'button-type' => 'submit',
							'value' => 'Submit'
						)
					)
				)
			)
		) );

		if ( $form->validate() ) {
			LF_Router::redirect( $this->router->admin_url() );
			exit;
		}
		else {
			$form->html();
		}
	}

	function _check_username( $value ) {
		return ( $this->config->username == $value );
	}

	function _check_password( $value ) {
		$hasher = new PasswordHash( 8, false );
		return $hasher->CheckPassword( $value, $this->config->password );
	}
}
