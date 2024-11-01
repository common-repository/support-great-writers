<?php
/*
Plugin Name: Amazon Book Store
Plugin URI: https://wordpress.org/plugins/support-great-writers/
Description: Sell Amazon products in sidebar widgets, unique to the individual POST or generically from a default pool of products that you define.
Author: HeyPublisher
Author URI: https://www.heypublisher.com
Version: 3.1.1
Requires at least: 5.0

  Copyright 2009-2014 Loudlever (wordpress@loudlever.com)
  Copyright 2014-2017 Richard Luck (https://github.com/aguywithanidea/)
  Copyright 2017-2020 HeyPublisher (https://www.heypublisher.com/)

  Permission is hereby granted, free of charge, to any person
  obtaining a copy of this software and associated documentation
  files (the "Software"), to deal in the Software without
  restriction, including without limitation the rights to use,
  copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the
  Software is furnished to do so, subject to the following
  conditions:

  The above copyright notice and this permission notice shall be
  included in all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
  OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
  HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
  WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
  FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
  OTHER DEALINGS IN THE SOFTWARE.

*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

/*
---------------------------------------------------------------------------------
  OPTION SETTINGS
---------------------------------------------------------------------------------
*/
global $HEYPUB_LOGGER;

$debug = (getenv('HEYPUB_DEBUG') === 'true');
if ($debug) {
  define('SGW_DEBUG',true);
} else {
  define('SGW_DEBUG',false);
}

// Configs specific to the plugin
define('SGW_PLUGIN_VERSION', '3.1.1');
define('SGW_PLUGIN_TESTED', '5.5.0');


define('SGW_PLUGIN_OPTTIONS', '_sgw_plugin_options');
define('SGW_BASE_URL', get_option('siteurl').'/wp-content/plugins/support-great-writers/');
define('SGW_DEFAULT_IMAGE', get_option('siteurl').'/wp-content/plugins/support-great-writers/images/not_found.gif');
define('SGW_POST_META_KEY','SGW_ASIN');           // This is the visible meta data key
define('SGW_POST_ASINDATA_KEY','_sgw_asindata');  // This is the invisible one that is structured hash
define('SGW_ADMIN_PAGE','amazon_bookstore');
define('SGW_ADMIN_PAGE_NONCE','sgw-save-options');
define('SGW_PLUGIN_ERROR_CONTACT','Please contact <a href="mailto:wordpress@heypublisher.com?subject=Amazon%20Bookstore%20Widget">wordpress@heypublisher.com</a> if you have any questions');
define('SGW_PLUGIN_FILE',plugin_basename(__FILE__));
define('SGW_PLUGIN_FULLPATH', dirname(__FILE__));

if (!class_exists("\HeyPublisher\Base\Log")) {
  require_once(SGW_PLUGIN_FULLPATH . '/include/classes/HeyPublisher/Base/Log.class.php');
}
if (!class_exists("\AMZNBS\ASIN")) {
  require_once(SGW_PLUGIN_FULLPATH . '/include/classes/AMZNBS/ASIN.class.php');
}
if (!class_exists("\HeyPublisher\Base\Updater")) {
  require_once(SGW_PLUGIN_FULLPATH . '/include/classes/HeyPublisher/Base/Updater.class.php');
}
// initialize the updater and test for update
$sgw_updater = new \HeyPublisher\Base\Updater( __FILE__ );
$sgw_updater->set_repository( 'amazon-book-store' ); // set repo
$sgw_updater->initialize(SGW_PLUGIN_TESTED); // initialize the updater

require_once(SGW_PLUGIN_FULLPATH . '/include/classes/SGW_Widget.class.php');
require_once(SGW_PLUGIN_FULLPATH . '/include/classes/AMZNBS/Admin.class.php');
$SGW_ADMIN = new \AMZNBS\Admin;


// enable link to settings page
add_filter($SGW_ADMIN->plugin_filter(), array(&$SGW_ADMIN,'plugin_link'), 10, 2 );

function RegisterAdminPage() {
  global $SGW_ADMIN;
  // ensure our js and style sheet only get loaded on our admin page
  $page = add_options_page('Amazon Book Store', 'Amazon Book Store', 'manage_options', SGW_ADMIN_PAGE, array(&$SGW_ADMIN,'action_handler'));
  $SGW_ADMIN->help = $page;
  add_action("admin_print_scripts-$page", 'AdminInit');
  add_action("admin_print_styles-$page", array(&$SGW_ADMIN,'admin_stylesheets'));
}

function AdminInit() {
  wp_enqueue_script('sgw', WP_PLUGIN_URL . '/support-great-writers/include/js/sgw.js',array(),SGW_PLUGIN_VERSION);
}
function RegisterWidgetStyle() {
	wp_enqueue_style( 'sgw_widget', SGW_BASE_URL . 'css/sgw_widget.css', array(), SGW_PLUGIN_VERSION );
}

if (class_exists("SupportGreatWriters")) {
  add_action('widgets_init', create_function('', 'return register_widget("SupportGreatWriters");'));
  add_action('admin_menu', 'RegisterAdminPage');
	add_action('wp_enqueue_scripts','RegisterWidgetStyle');
	add_filter('contextual_help', array(&$SGW_ADMIN,'configuration_screen_help'), 10, 3);

}
register_activation_hook( __FILE__, array(&$SGW_ADMIN,'activate_plugin'));
register_deactivation_hook( __FILE__, array(&$SGW_ADMIN,'deactivate_plugin'));
?>
