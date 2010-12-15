<?php
/*
Plugin Name: CF Hide Email Notification
Description: This plugin will hide the nonconfirmation checkbox. It will then force no notification for user creation.
This is currently setup to work on a multisite install.
Version: 0.1
Author: Crowd Favorite
Author URI: http://crowdfavorite.com
*/

function cfhide_request_handler() {
	if (!empty($_GET['cfhide_action'])) {
		switch ($_GET['cfhide_action']) {
			case 'cfhide_js':
				cfhide_js();
				die();
				break;
			default:
				break;
		}
	}	
}
add_action('admin_init', 'cfhide_request_handler');

function cfhide_enqueue_js() {
	wp_enqueue_script('cfhide_js', admin_url('?cfhide_action=cfhide_js'), array('jquery') );
}
add_action('admin_print_scripts-user-new.php', 'cfhide_enqueue_js');

function cfhide_js() {
	// Using JS to check and hide, essentially removes the checked 
?>
jQuery(function($) {
	$('input#noconfirmation').closest('tr').hide();
});
<?php
}

// Force nonconfirmation
function cfhide_force_nonconfirmation() {
	if ( isset($_REQUEST['action']) && 'adduser' == $_REQUEST['action'] ) {
		check_admin_referer('add-user');
		$_POST['noconfirmation'] = 1;
	}
}
add_action('admin_init', 'cfhide_force_nonconfirmation');


?>