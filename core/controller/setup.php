<?php
class LF_Controller_Setup extends LF_Controller {
	function install() {
		$form = new LF_Form( 'install-form' );

		$elements['credentials'] = array(
			'type' => 'fieldset',
			'legend' => 'Credentials',
			'elements' => array(
				'username' => array(
					'type' => 'text'
				),
				'password1' => array(
					'type' => 'password'
				),
				'password2' => array(
					'type' => 'password'
				)
			)
		);

		if ( $noaccess || true ) {
			$elements['ftp'] = array(
				'type' => 'fieldset',
				'legend' => 'FTP Details',
				'elements' => array(
					'ftp-hostname' => array(
						'type' => 'text'
					),
					'ftp-username' => array(
						'type' => 'text'
					),
					'ftp-password' => array(
						'type' => 'password'
					),
					'ftp-connection' => array(
						'type' => 'radiolist'
					)
				)
			);
		}

		$elements['buttons'] = array(
			'type' => 'fieldset',
			'elements' => array(
				'submit' => array(
					'type' => 'submit-button'
				)
			)
		);

		$form->add( $elements );

		if ( $form->is_submitted() ) {

		}

		echo "Boo";
	}
}