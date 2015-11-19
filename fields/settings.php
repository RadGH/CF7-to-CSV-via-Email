<?php
if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array (
		'key' => 'group_5643a01e5d032',
		'title' => 'Settings',
		'fields' => array (
			array (
				'key' => 'field_5643a0c6b802b',
				'label' => 'Enable',
				'name' => 'ldcce_send_csv_as_email',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => 'Send a CSV export of every submission to email',
				'default_value' => 0,
			),
			array (
				'key' => 'field_5643a089b8029',
				'label' => 'Recipient Name',
				'name' => 'ldcce_recipient_name',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_5643a0c6b802b',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'readonly' => 0,
				'disabled' => 0,
			),
			array (
				'key' => 'field_5643a0abb802a',
				'label' => 'Recipient Email Address',
				'name' => 'ldcce_recipient_email',
				'type' => 'email',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_5643a0c6b802b',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array (
				'key' => 'field_564cfa8232a54',
				'label' => 'Specific Forms Only',
				'name' => 'ldcce_specific_form_ids',
				'type' => 'post_object',
				'instructions' => 'If blank, all forms will be used. Otherwise, only the specified forms will send a CSV.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'wpcf7_contact_form',
				),
				'taxonomy' => array (
				),
				'allow_null' => 1,
				'multiple' => 1,
				'return_format' => 'id',
				'ui' => 1,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'ld-cf7-csv-to-email',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'left',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));

endif;