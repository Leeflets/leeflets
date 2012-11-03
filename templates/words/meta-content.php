<?php
$content = array(
	'body-content' => array(
		'type' => 'fieldset',
		'elements' => array(
			'page-title' => array(
				'type' => 'text',
				'label' => 'Page Title',
				'required' => true,
				'autofocus' => true,
				'tip' => 'This is the title displayed on your page.'
			),
			'intro-paragraph' => array(
				'type' => 'wysiwyg',
				'label' => 'Content',
				'required' => true,
				'tip' => 'This is the text displayed on your page.'
			)
		)
	),
	'left-button-content' => array(
		'type' => 'fieldset',
		'elements' => array(
			'left-button-text' => array(
				'type' => 'text',
				'label' => 'Left Button Text',
				'required' => true,
				'tip' => 'The text for the left hand button link.'
			),
			'left-button-url' => array(
				'type' => 'url',
				'label' => 'Left Button Link',
				'required' => true,
				'tip' => 'The link for the left hand button.'
			)
		)
	),
	'right-button-content' => array(
		'type' => 'fieldset',
		'elements' => array(
			'right-button-text' => array(
				'type' => 'text',
				'label' => 'Right Button Text',
				'required' => true,
				'tip' => 'The text for the right hand button link.'
			),
			'right-button-url' => array(
				'type' => 'url',
				'label' => 'Right Button Link',
				'required' => true,
				'tip' => 'The link for the right hand button.'
			)
		)
	)
);
