<?php
class LF_Controller_Setup extends LF_Controller {
	function matching_passwords( $password2, $password1 ) {
		return ( $_POST['password1'] == $_POST['password2'] );
	}

	function install() {
		if ( $this->config->is_loaded ) {
			die( "Oops, there's already a config.php file. You'll need to remove it to run this installer." );
		}

		$password_min_length = 5;

		$form = new LF_Form( 'install-form', array(
			'elements' => array(
				'credentials' => array(
					'type' => 'fieldset',
					'elements' => array(
						'username' => array(
							'type' => 'text',
							'label' => 'Username',
							'pattern' => array( '/^[a-z0-9\.]+$/', 'Lowercase letters, numbers, and periods only.' ),
							'required' => true
						),
						'password1' => array(
							'type' => 'password',
							'label' => 'Password',
							'required' => true,
							'validation' => array(
								array(
									'callback' => 'min_length',
									'msg' => 'Sorry, your password must be at least ' . $password_min_length . ' characters in length.',
									'args' => array( $password_min_length )
								)
							)
						),
						'password2' => array(
							'type' => 'password',
							'label' => 'Confirm Password',
							'required' => true,
							'validation' => array(
								array(
									'callback' => array( $this, 'matching_passwords' ),
									'msg' => 'Your passwords do not match. Please enter matching passwords.',
									'args' => array( $_POST['password2'] )
								)
							)
						)
					)
				)
			)
		) );

		if ( $noaccess ) {
			$elements['ftp'] = array(
				'type' => 'fieldset',
				'legend' => 'FTP Details',
				'elements' => array(
					'ftp-hostname' => array(
						'type' => 'text',
						'label' => 'FTP Hostname'
					),
					'ftp-username' => array(
						'type' => 'text',
						'label' => 'Username'
					),
					'ftp-password' => array(
						'type' => 'password',
						'label' => 'Password'
					),
					'ftp-connection' => array(
						'type' => 'radiolist',
						'options' => array(
							'ftp' => 'FTP',
							'sftp' => 'sFTP'
						),
						'value' => 'ftp'
					)
				)
			);
		}

		$elements['buttons'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'submit' => array(
					'type' => 'button',
					'button-type' => 'submit',
					'value' => 'Submit'
				)
			)
		);

		$form->add_elements( $elements );

		if ( $form->is_submitted() && $form->validate() ) {
			$data = array(
				'username' => $_POST['username'],
				'password' => $_POST['password1']
			);

			$this->config->write( $this->filesystem, $data );

			LF_Router::redirect( $this->router->admin_url( '/user/login/' ) );
		}
		else {
			$form->html();
		}

		//print_r( $form->elements );

		//print_r( $form->errors );
	}
}