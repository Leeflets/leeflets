<?php
$colors = array(
	'Blue',
	'Green',
	'Black',
	'Purple',
	'Yellow'
);

$content = array(
	'test-fields' => array(
		'title' => 'Testing Fields',
		'type' => 'fieldset',
		'elements' => array(
			'cover-letter' => array(
				'label' => 'Cover Letter',
				'type' => 'file',
				'data-url' => $this->router->admin_url( '/content/upload/' )
			),
			'fav-color' => array(
				'type' => 'checklist',
				'label' => 'Favorite Color',
				'options' => array_combine( $colors, $colors )
			),
			'birthday' => array(
				'type' => 'date',
				'label' => 'Birthday',
				'data-date-format' => 'yy/mm/dd'
			),
			'background-image' => array(
				'label' => 'Background Image',
				'type' => 'file',
				'data-url' => $this->router->admin_url( '/content/upload/' )
			)
		)
	),
	'page' => array(
		'type' => 'fieldset',
		'elements' => array(
			'title' => array(
				'type' => 'text',
				'label' => 'Page Title',
				'required' => true,
				'autofocus' => true,
				'tip' => 'This is the title displayed on your page.'
			),
			'intro-paragraph' => array(
				'type' => 'wysiwyg',
				'class' => 'input-block-level',
				'label' => 'Content',
				'rows' => 14,
				'required' => true,
				'tip' => 'This is the text displayed on your page.'
			)
		)
	),
	'movies' => array(
		'type' => 'repeatable',
		'title' => 'Movies',
		'description' => 'Consume all the Sam Jackson movies!',
		'empty-to-show' => 3,
		'elements' => array(
			'show' => array(
				'type' => 'checkbox',
				'column-width' => '10%',
				'title' => 'Check to show on site'
			),
			'title' => array(
				'type' => 'text',
				'required' => true,
				'column-width' => '40%',
				'placeholder' => 'Title'
			),
			'url' => array(
				'type' => 'url',
				'column-width' => '30%',
				'placeholder' => 'IMDB URL'
			),
			'stars' => array(
				'type' => 'select',
				'column-width' => '20%',
				'options' => array(
					'' => 'Star Rating',
					'1' => '1/5',
					'2' => '2/5',
					'3' => '3/5',
					'4' => '4/5',
					'5' => '5/5'
				)
			)
		)
	),
	'left-button' => array(
		'type' => 'fieldset',
		'elements' => array(
			'text' => array(
				'type' => 'text',
				'label' => 'Left Button Text',
				'required' => true,
				'tip' => 'The text for the left hand button link.'
			),
			'url' => array(
				'type' => 'url',
				'label' => 'Left Button Link',
				'required' => true,
				'tip' => 'The link for the left hand button.',
				'class' => 'input-block-level'
			)
		)
	),
	'right-button' => array(
		'type' => 'fieldset',
		'elements' => array(
			'text' => array(
				'type' => 'text',
				'label' => 'Right Button Text',
				'required' => true,
				'tip' => 'The text for the right hand button link.'
			),
			'url' => array(
				'type' => 'url',
				'label' => 'Right Button Link',
				'required' => true,
				'tip' => 'The link for the right hand button.',
				'class' => 'input-block-level'
			)
		)
	)
);
