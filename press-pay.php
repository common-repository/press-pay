<?php
/*****************************************************************************
 * Plugin Name: PressPay
 * Plugin URI: http://atomicbroadcast.net/
 * Description: A plugin to easily turn any WordPress blog into a webstore. Click the "Connect with Stripe" button to configure your account then add a shortcode (ex: [presspay amount="2000" product_id="007" description="Wonder Widget"]) to any post or page.
 * Author: Andrew Dixon
 * Author URI: http://atomicbroadcast.net
 * Version: 2.3
 *****************************************************************************/
/**********************************
* constants and globals
**********************************/
if (!defined( 'STRIPE_BASE_URL' )) {
	define( 'STRIPE_BASE_URL', plugin_dir_url( __FILE__ ) );
}
if (!defined( 'STRIPE_BASE_DIR' )) {
	define( 'STRIPE_BASE_DIR', dirname( __FILE__ ) );
}
if (!defined( 'PRESS_PAY_TRANSACTION_TABLE' )) {
  global $wpdb;
  define( 'PRESS_PAY_TRANSACTION_TABLE', $wpdb->prefix . 'press_pay_transactions' );
}
if (!defined('PRESS_PAY_CUSTOMER_TABLE')) {
  global $wpdb;
  define( 'PRESS_PAY_CUSTOMER_TABLE', $wpdb->prefix . 'press_pay_customers' );
}
if (!defined('PRESS_PAY_EMAIL_TABLE')) {
  global $wpdb;
  define('PRESS_PAY_EMAIL_TABLE', $wpdb->prefix . 'press_pay_emails');
}
// Setup Database Tables.
require_once( STRIPE_BASE_DIR . '/db/migrations/migrate.php' );
add_action( 'plugins_loaded', 'press_pay_migrate_database' );

/**********************************
* includes
**********************************/
include( STRIPE_BASE_DIR . '/lib/Mixin.php' );
include( STRIPE_BASE_DIR . '/app/model/debug.model.php' );
require_once( STRIPE_BASE_DIR . '/app/controller/controller.php' );
require_once( STRIPE_BASE_DIR . '/app/model/press_pay_settings.php' );
require_once( STRIPE_BASE_DIR . '/app/controller/listener/press_pay_stripe_listener.php' );
$listener = new PressPayStripeListener;
require_once( STRIPE_BASE_DIR . '/app/controller/listener/press_pay_upgrade_controller.php' );
$upgrade_listener = new PressPayUpgradeListener;

if (is_admin()) {
  // add links to the plugin page
  require_once( STRIPE_BASE_DIR . '/app/controller/admin.controller.php' );
  $adminController = new AdminController;

  add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'settings_link');
} else {
  require_once(STRIPE_BASE_DIR . '/app/controller/press_pay_shortcodes_controller.php');
  require_once(STRIPE_BASE_DIR . '/app/controller/press_pay_includes_controller.php');
  require_once(STRIPE_BASE_DIR . '/app/controller/email.controller.php');
  require_once(STRIPE_BASE_DIR . '/app/model/press_pay_email.php');
  require_once(STRIPE_BASE_DIR . '/app/model/press_pay_customer.php');
  require_once(STRIPE_BASE_DIR . '/app/model/press_pay_transaction.php');
  $shortcodeController = new PressPayShortcodeController;
  $includesController = new PressPayIncludesController;
}
function settings_link($links) {
  $permalink = get_site_url();
  $permalink = preg_replace("/^http/", "", $permalink);
  $href = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_38T6O1nnMRY3eoBzWlgd0oiZCfLqJ05K&scope=read_write&state=" . $permalink;
  $connect_link = "<a href={$href} class='stripe-connect'><span>Connect with Stripe</span></a>";
  array_push($links, $connect_link);
  return $links;
}
?>
