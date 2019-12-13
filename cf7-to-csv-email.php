<?php
/*
Plugin Name: Contact Form 7 to CSV via Email
Version: 1.0.2
Plugin URI: http://www.radgh.com/
Description: (Requires Advanced Custom Fields PRO) Contact form submissions will be stored as a CSV file and emailed to a recipient of your choice. Every submission generates a CSV of all fields, and the email includes attachments. Will send to all forms by default, or you can specify which forms.
Author: Radley Sustaire
Author URI: mailto:radleygh@gmail.com
*/

/*
    Copyright (C) 2015 Radley Sustaire

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if( !defined( 'ABSPATH' ) ) exit;

define( 'LDCCE_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define( 'LDCCE_PATH', dirname(__FILE__) );

add_action( 'plugins_loaded', 'ldcce_initialize', 15 );

function ldcce_initialize() {
	if ( !class_exists('acf') ) {
		add_action( 'admin_notices', 'ldcce_no_acf_warning' );
		return;
	}else{
		if ( !function_exists('acf_add_options_sub_page') ) {
			add_action( 'admin_notices', 'ldcce_no_acf_pro_warning' );
			return;
		}
	}

	if ( !class_exists('WPCF7_ContactForm') ) {
		add_action( 'admin_notices', 'ldcce_no_cf7_warning' );
		return;
	}

	include( LDCCE_PATH . '/fields/settings.php' );
	include( LDCCE_PATH . '/includes/options.php' );
	include( LDCCE_PATH . '/includes/contact-form.php' );
	include( LDCCE_PATH . '/includes/csv.php' );
}

function ldcce_no_acf_warning() {
	$message = "<strong>Error:</strong> Advanced Custom Fields PRO is not active.\n\nThis plugin requires Advanced Custom Fields PRO and Contact Form 7 to be running in order to work properly. Please ensure these plugins are active, or disable this plugin.";

	ldcce_display_warning($message);
}

function ldcce_no_acf_pro_warning() {
	$message = "<strong>Error:</strong> Advanced Custom Fields PRO is not active.\n\nHowever, Advanced Custom Fields (standard edition) is active. Please install and activate Advanced Custom Fields PRO.";

	ldcce_display_warning($message);
}

function ldcce_no_cf7_warning() {
	$message = "<strong>Error:</strong> Contact Form 7 is not active.\n\nThis plugin requires Advanced Custom Fields PRO and Contact Form 7 to be running in order to work properly. Please ensure these plugins are active, or disable this plugin.";

	ldcce_display_warning($message);
}

function ldcce_display_warning( $message ) {
	?>
	<div class="error">
		<p><strong>Contact Form 7 to CSV via Email:</strong></p>
		<?php echo wpautop($message); ?>
	</div>
	<?php
}
