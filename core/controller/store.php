<?php
class LF_Controller_Store extends LF_Controller {
	function templates() {
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
			'title' => 'Active Template',
			'type' => 'fieldset',
			'elements' => array(
				'active' => array(
					'type' => 'radiolist',
					'options' => $templates
				)
			)
		);

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

		$form = new LF_Form( 'templates-form', array(
			'action' => $this->router->admin_url( 'store/templates/' ),
			'elements' => $elements
		) );

		$error = '';
		if ( $form->validate() ) {
			$values = $_POST;
			unset( $values['submit'] );
			unset( $values['submission-templates-form'] );

			$values = array_merge( $this->settings->get_data(), $values );

			if ( !$this->settings->write( $values, $this->filesystem ) ) {
				$error = 'Error saving the settings.';
			}
		}
		elseif ( $form->is_submitted() ) {
			$error = 'Please correct the errors below.';
		}
		else {
			$form->set_values( $this->settings->get_data() );
		}

		return compact( 'form', 'error' );
	}
}