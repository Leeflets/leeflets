<?php
$content = array(
	'intro' => array(
		'type' => 'fieldset',
		'elements' => array(
			'title' => array(
				'type' => 'text',
				'label' => 'Page Title',
				'required' => true,
				'tip' => 'This is the title displayed on your page.'
			),
			'paragraph' => array(
				'type' => 'wysiwyg',
				'class' => 'input-block-level',
				'label' => 'Content',
				'rows' => 14,
				'required' => true,
				'tip' => 'This is the text displayed on your page.'
			),
		)
	),
	'button' => array(
		'type' => 'fieldset',
		'elements' => array(
			'text' => array(
				'type' => 'text',
				'label' => 'Button Text',
				'required' => true,
				'tip' => 'The text for the intro button.'
			),
			'url' => array(
				'type' => 'url',
				'label' => 'Button Link',
				'required' => true,
				'tip' => 'The link for the intro button.',
				'class' => 'input-block-level'
			)
		)
	),
    'features' => array(
    	'type' => 'repeatable',
    	'title' => 'Features',
    	'description' => 'Add features.',
    	'empty-to-show' => 4,
    	'elements' => array(
    		'icon' => array(
    			'type' => 'image',
    			'label' => 'Icon',
				'versions' => array(
					'icon@2x' => array(
						'width' => 202,
						'height' => 220,
						'crop' => array( 'center', 'center' )
					)
				)
    		),
    		'title' => array(
    			'type' => 'text',
    			'label' => 'Title'
    		),
    		'text' => array(
    			'type' => 'wysiwyg',
    			'label' => 'Text'
    		)
    	)
    ),
	'footer' => array(
		'type' => 'fieldset',
		'elements' => array(
			'copyright' => array(
				'type' => 'text',
				'label' => 'Copyright',
				'required' => true,
				'autofocus' => true,
				'tip' => 'Displayed within the footer.'
			),
			'twitter' => array(
				'type' => 'text',
				'label' => 'Twitter Link',
				'required' => false,
				'autofocus' => true,
				'tip' => 'Displayed within the footer.'
			),
			'facebook' => array(
				'type' => 'text',
				'label' => 'Facebook Link',
				'required' => false,
				'autofocus' => true,
				'tip' => 'Displayed within the footer.'
			)
		)
	)
);
