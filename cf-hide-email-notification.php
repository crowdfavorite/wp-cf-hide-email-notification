<?php
/*
Plugin Name: CF Hide Email Notification
Description: Plugin for handling the blocking of Email confirmations for new users in a MultiSite install of WordPress.
Version: 1.0
Author: Crowd Favorite
Author URI: http://crowdfavorite.com
*/

/**
 * Request Handler for functions related to this plugin
 *
 * @return void
 */
function cfhide_request_handler() {
	// Get the JS File if it is asked for
	if (!empty($_GET['cf_action'])) {
		switch ($_GET['cf_action']) {
			case 'cfhide_js':
				cfhide_js();
				die();
				break;
			default:
				break;
		}
	}
	
	// If the form is posted from the New User page, modify the $_POST
	if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'adduser') {
		check_admin_referer('add-user');
		$_POST['noconfirmation'] = 1;
	}
}
add_action('init', 'cfhide_request_handler', 1);

/**
 * Enqueue the JS for the New User screen
 *
 * @return void
 */
function cfhide_enqueue_js() {
	wp_enqueue_script('cfhide_js', admin_url('?cf_action=cfhide_js'), array('jquery') );
}
add_action('admin_print_scripts-user-new.php', 'cfhide_enqueue_js');

/**
 * JS to be added to the New User screen to hide the noconfirmation checkbox
 *
 * @return void
 */
function cfhide_js() {
	// Using JS to check and hide, essentially removes the checked 
?>
jQuery(function($) {
	$('input#noconfirmation').closest('tr').hide();
});
<?php
}

/**
 * This function will block the Welcome email from being sent to the user
 *
 * @param int $user_id 
 * @param string $password 
 * @param array $meta 
 * @return bool
 */
function cfhide_email_confirmation($user_id = '', $password = '', $meta = '') {
	return false;
}
add_filter('wpmu_welcome_user_notification', 'cfhide_email_confirmation', 10, 3);


/**
 * 
 * CF Readme Inclusion
 * 
 **/

/**
 * Enqueue the readme function
 */
function cfhide_add_readme() {
	if(function_exists('cfreadme_enqueue')) {
		cfreadme_enqueue('cf-hide-email-notifications','cfhide_readme');
	}
}
add_action('admin_init','cfhide_add_readme');

/**
 * return the contents of the links readme file
 * replace the image urls with full paths to this plugin install
 *
 * @return string
 */
function cfhide_readme() {
	$file = realpath(dirname(__FILE__)).'/readme/readme.txt';
	if(is_file($file) && is_readable($file)) {
		$markdown = file_get_contents($file);
		$markdown = preg_replace('|!\[(.*?)\]\((.*?)\)|','![$1]('.WP_PLUGIN_URL.'/cf-hide-email-notifications/readme/$2)',$markdown);
		return $markdown;
	}
	return null;
}

?>