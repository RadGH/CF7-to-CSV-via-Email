<?php
function ldcce_cf7_get_uploaded_files() {
	$submission = WPCF7_Submission::get_instance();

	return $submission->uploaded_files();
}

function ldcce_cf7_mail( $contact_form ) {
	if ( !ldcce_csv_email_enabled() ) return $contact_form;

	// If "Specific Forms Only" is set, and the ID isn't allowed, skip sending a CSV email.
	$allowed_forms = get_field( 'ldcce_specific_form_ids', 'options' );
	if ( !empty($allowed_forms) && !in_array($contact_form->id(), $allowed_forms, true) ) return $contact_form;

	// Define the templates of CF7 which we will search through (they must be enabled)
	$properties = $contact_form->get_properties();

	$field_templates = array();
	$attachment_templates = array();

	$field_templates[] = $properties['mail']['subject'];
	$field_templates[] = $properties['mail']['body'];
	$attachment_templates[] = $properties['mail']['attachments'];

	if ( $properties['mail_2']['active'] ) {
		$field_templates[] = $properties['mail_2']['subject'];
		$field_templates[] = $properties['mail_2']['body'];
		$attachment_templates[] = $properties['mail_2']['attachments'];
	}

	// Search for field usage like [your-name], then grab the values from $_POST
	$fields = array();

	foreach( $field_templates as $template ) {
		// Find text wrapped in [], where the content is not a whitespace character or an angle bracket character.
		if ( preg_match_all( '/\[([^\s\]\[]+)\]/', $template, $matches ) ) {
			foreach( $matches[1] as $key ) {
				$fields[$key] = isset($_REQUEST[$key]) ? stripslashes($_REQUEST[$key]) : null;
			}
		}
	}

	// Search for attachments, add the $_FILES entry if it exists and didn't give an error
	$attachments = array();
	$uploads = ldcce_cf7_get_uploaded_files();

	foreach( $attachment_templates as $template ) {
		// Find text wrapped in [], where the content is not a whitespace character or an angle bracket character.
		if ( preg_match_all( '/\[([^\s\]\[]+)\]/', $template, $matches ) ) {
			foreach( $matches[1] as $key ) {
				$file = isset($_FILES[$key]) ? $_FILES[$key] : null;

				if ( $file && isset($uploads[$key]) ) {
					$attachments[$key] = $uploads[$key];
				}else{
					$attachments[$key] = null;
				}
			}
		}
	}

	$id = $contact_form->id();
	$name = $contact_form->name();
	$title = $contact_form->title();

	ldcce_contact_form_submit( $id, $name, $title, $fields, $attachments );

	return $contact_form;
}
add_filter( 'wpcf7_before_send_mail', 'ldcce_cf7_mail' );