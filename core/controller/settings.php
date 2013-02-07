<?php
class LF_Controller_Settings extends LF_Controller {
	function edit() {
		if ( isset( $_POST['connection']['type'] ) ) {
			if ( 'direct' == $_POST['connection']['type'] ) {
				$_POST['connection']['hostname'] = '';
				$_POST['connection']['username'] = '';
				$_POST['connection']['password'] = '';
			}
			elseif ( '' == $_POST['connection']['password'] ) {
				$_POST['connection']['password'] = $this->settings->data['connection']['password'];
			}
		}

		$elements['site-meta'] = array(
			'title' => 'Meta Information',
			'description' => 'Inserted into the <head> tag of the site. Used by search engines.',
			'type' => 'fieldset',
			'elements' => array(
				'author' => array(
					'type' => 'text',
					'label' => 'Site Author',
					'autofocus' => true,
					'class' => 'input-large'
				),
				'title' => array(
					'type' => 'text',
					'label' => 'Site Title',
					'class' => 'input-block-level'
				),
				'description' => array(
					'type' => 'text',
					'label' => 'Site Description',
					'class' => 'input-block-level',
					'tip' => 'Keep it short and sweet'
				),
			)
		);

		$elements['privacy'] = array(
			'title' => 'Privacy Settings',
			'type' => 'fieldset',
			'elements' => array(
				'visibility' => array(
					'type' => 'radiolist',
					'value' => '1',
					'options' => array(
						'1' => 'Allow search engines to index this site.', 
			            '2' => 'Don\'t allow search engines to index this site.'
					)
				)
			)
		);

		$elements['analytics'] = array(
			'title' => 'Analytics Settings',
			'type' => 'fieldset',
			'elements' => array(
				'code' => array(
					'type' => 'textarea',
					'label' => 'Tracking Code',
					'class' => 'input-block-level',
					'rows' => 5
				)
			)
		);

		$templates = array();
		$folders = glob( $this->config->templates_path . '/*' );
		foreach ( $folders as $folder ) {
			if ( !file_exists( $folder . '/meta-about.php' ) ) continue;
			include $folder . '/meta-about.php';
			if ( !isset( $about['name'] ) || !isset( $about['version'] ) ) continue;
			$folder = basename( $folder );
			$templates[$folder] = $about['name'] . ' ' . $about['version'];
			unset( $about );
		}

		$elements['template'] = array(
			'title' => 'Template',
			'type' => 'fieldset',
			'elements' => array(
				'active' => array(
					'type' => 'radiolist',
					'options' => $templates
				)
			)
		);

		$elements['connection'] = $this->filesystem->get_connection_fields(
			array( $this, '_check_connection' ), false
		);

		$elements['connection']['title'] = 'Filesystem Settings';
		$elements['connection']['elements']['hostname']['class'] = 'input-xlarge';
		$elements['connection']['elements']['username']['class'] = 'input-xlarge';
		$elements['connection']['elements']['password']['class'] = 'input-xlarge';

		if ( $this->config->debug ) {
			$elements['debug'] = array(
				'title' => 'Debug Options',
				'description' => 'These settings only have an effect when in debug mode.',
				'type' => 'fieldset',
				'elements' => array(
					'disable-overlays' => array(
						'type' => 'checkbox',
						'label' => 'Disable click-to-edit preview overlays'
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
					'class' => 'btn btn-primary',
					'value' => 'Save Changes'
				)
			)
		);

		$form = new LF_Form( 'settings-form', array(
			'action' => $this->router->admin_url( 'settings/edit/' ),
			'elements' => $elements
		) );

		$error = '';
		if ( $form->validate() ) {
			$values = $_POST;
			unset( $values['submit'] );
			unset( $values['submission-settings-form'] );

			if ( !isset( $values['debug'] ) ) {
				$values['debug'] = array();
			}

			$values = array_merge( $this->settings->data, $values );

			if ( !$this->settings->write( $values, $this->filesystem ) ) {
				$error = 'Error saving the settings.';
			}
		}
		elseif ( $form->is_submitted() ) {
			$error = 'Please correct the errors below.';
		}
		else {
			$form->set_values( $this->settings->data );
		}

		return compact( 'form', 'error' );
	}
}