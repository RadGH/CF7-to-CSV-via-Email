<?php
function ldcce_csv_email_enabled() {
	return get_field( 'ldcce_send_csv_as_email', 'options' ) && get_field( 'ldcce_recipient_email', 'options' );
}

function ldcce_contact_form_submit( $id, $name, $title, $fields, $attachments ) {
	$date = date( 'Y-m-d H:i:s', current_time('timestamp') );
	$time = time();

	$row = array(
		'_id' => $id,
		'_name' => $name,
		'_title' => $title,
		'_date' => $date,
		'_time' => $time,
		'_ip' => $_SERVER['REMOTE_ADDR'],
		'_fields' => count($fields) + ( empty($attachments) ? 0 : count($attachments) ),
	);

	// Add each field as a column
	foreach( $fields as $k => $v ) {
		if ( isset($row[$k]) ) $k = 'field' . $k; // Don't let a form overwrite the row defaults. Eg, if your contact form has the field "id", rename it to "_id".
		$row[$k] = $v;
	}

	// Add an attachment field, comma separated filenames
	if ( !empty($attachments) ) {
		foreach( $attachments as $k => $file ) {
			$filenamename = $file['name'];
			$filenamename = str_replace('"', '\"', $filenamename); // Escape a double quote if it exists.

			$row['attachment-' . $k] = $filenamename;
		}
	}

	// Build a CSV file using a temporary file
	$csv = tmpfile();
	$csv_meta = stream_get_meta_data( $csv );
	$csv_uri = $csv_meta['uri'];

	global $ldcce_csv_content;

	fputcsv($csv, array_keys($row));
	fputcsv($csv, array_values($row));

	rewind($csv);

	$ldcce_csv_content = stream_get_contents($csv);

	fclose($csv);
	@unlink($csv_uri);

	ldcce_email_send( $id, $name, $title, $csv, $attachments );
}

function ldcce_email_send( $form_id, $form_name, $form_title, $csv, $attachments ) {
	if ( !get_field( 'ldcce_send_csv_as_email', 'options' ) ) return false;

	$to_name = get_field( 'ldcce_recipient_name', 'options' );
	$to_email = get_field( 'ldcce_recipient_email', 'options' );

	$to = $to_email;
	if ( $to_name ) $to = $to_name . ' <' . $to_email . '>';

	$subject = 'Contact Form Submitted - '. $form_title .' (#'. $form_id .')';

	ob_start();

	?>
	<table>
		<tr>
			<th style="width:120px">Form ID</th>
			<td><?php echo $form_id; ?></td>
		</tr>
		<tr>
			<th style="width:120px">Form Name:</th>
			<td><?php echo $form_title; ?></td>
		</tr>
		<tr>
			<th style="width:120px">Attachment<?php echo $c == 1 ? '' : 's'; ?>:</th>
			<td><?php
				foreach( $attachments as $i => $f ) {
					echo esc_html(basename($f));

					if ( $i < count($attachments) ) echo ", ";
				}
			?></td>
		</tr>
	</table>
	<?php
	$body = ob_get_clean();

	$headers = array('Content-Type: text/html; charset=UTF-8');

	// Put attachments in a global, we'll add these using another filter.. not in wp_mail's attachment parameter.
	global $ldcce_csv_filename, $ldcce_csv_attachments;
	$ldcce_csv_filename = $form_id . '_' . $form_name . '_' . ldcce_get_unique_id() . '.csv';
	$ldcce_csv_attachments = $attachments;

	add_action( 'phpmailer_init', 'ldcce_email_add_attachments' );
	wp_mail( $to, $subject, wpautop($body), $headers );
	remove_action( 'phpmailer_init', 'ldcce_email_add_attachments' );

	return true;
}

function ldcce_email_add_attachments( $phpmailer ) {
	global $ldcce_csv_content, $ldcce_csv_attachments, $ldcce_csv_filename;

	if ( $ldcce_csv_content && $ldcce_csv_filename ) {
		$phpmailer->AddStringAttachment( $ldcce_csv_content, $ldcce_csv_filename );
	}

	if ( $ldcce_csv_attachments ) {
		foreach( $ldcce_csv_attachments as $name => $path ) {
			$phpmailer->AddAttachment( $path );
		}
	}
}

function ldcce_get_unique_id() {
	// Always get a fresh value of the increment value
	wp_cache_delete('ldcce_increment');

	$id = get_option( 'ldcce_increment' );
	if ( !$id ) $id = 1;

	$id = $id + 1;
	update_option( 'ldcce_increment', $id );

	return $id;
}