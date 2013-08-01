<?php
namespace Leeflets\Controller;

class Setup extends \Leeflets\Controller {
	protected $no_auth_actions = array( 'install' );

	function matching_passwords( $password2, $password1 ) {
		return ( $_POST['password1'] == $_POST['password2'] );
	}

	function install() {
		if ( $this->config->is_loaded ) {
			die( "Oops, there's already a config.php file. You'll need to remove it to run this installer." );
		}

		$password_min_length = 5;
		$password_max_length = 72;

		$form = new \Leeflets\Form( $this->config, $this->router, $this->settings, 'install-form', array(
			'elements' => array(
				'credentials' => array(
					'type' => 'fieldset',
					'elements' => array(
						'username' => array(
							'type' => 'email',
							'placeholder' => 'Email Address',
							'class' => 'input-block-level',
							'required' => true
						),
						'password1' => array(
							'type' => 'password',
							'placeholder' => 'Password',
							'class' => 'input-block-level',
							'required' => true,
							'validation' => array(
								array(
									'callback' => 'min_length',
									'msg' => 'Sorry, your password must be at least ' . $password_min_length . ' characters in length.',
									'args' => array( $password_min_length )
								),
								array(
									'callback' => 'max_length',
									'msg' => 'Sorry, your password can be no longer than ' . $password_max_length . ' characters in length.',
									'args' => array( $password_max_length )
								)
							)
						),
						'password2' => array(
							'type' => 'password',
							'placeholder' => 'Confirm Password',
							'class' => 'input-block-level',
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

		if ( !$this->filesystem->have_direct_access() ) {
			$elements['warning'] = array(
				'type' => 'html',
				'value' => $this->view->get_partial( 'ftp-warning' )
			);

			$elements['connection'] = $this->filesystem->get_connection_fields(
				array( $this, '_check_connection' ), true
			);
		}

		$elements['buttons'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'submit' => array(
					'type' => 'button',
					'button-type' => 'submit',
					'class' => 'btn btn-primary',
					'value' => 'Install Leeflets'
				)
			)
		);

		$form->add_elements( $elements );

		if ( $form->validate() ) {
			$hasher = new \PasswordHash( 8, false );

			$data = array(
				'username' => $_POST['credentials']['username'],
				'password' => $hasher->HashPassword( $_POST['credentials']['password1'] )
			);
			
			$this->config->write( $this->filesystem, $data );

			$htaccess = new \Leeflets\Htaccess( $this->filesystem, $this->router, $this->config );
			$htaccess->write();

			if ( isset( $_POST['connection']['type'] ) ) {
				$this->settings->save_connection_info( $_POST, $this->filesystem );
			}

			\Leeflets\Router::redirect( $this->router->admin_url( '/user/login/' ) );
			exit;
		}

		$args = compact( 'form' );

		$args['page-title'] = 'Install';
		$args['layout'] = 'logged-out';

		return $args;
	}
}