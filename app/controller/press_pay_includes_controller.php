<?php
class PressPayIncludesController {
  public function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
    // start session for sift science
    add_action( 'wp_loaded', array( $this, 'sift_science_session' ) );
 }

  public function sift_science_session() {
    if (!session_id()) {
      session_start();
    }
  }

  /***************************************************************************
   * load_scripts()                                                          *
   * *************************************************************************/
  public function load_scripts() {
    DebugModel::debug( 'press_pay_laod_scripts()' );

    $publishable_key = PressPaySettings::get_stripe_publishable_key();

    // jquery
  	wp_enqueue_script('jquery');
	  wp_enqueue_script('jquery-ui');
	  wp_enqueue_script('jquery-ui-dialog');

    // stripe
    wp_enqueue_script('stripe', 'https://js.stripe.com/v1/');
    wp_enqueue_script(
      'stripe-processing',
      STRIPE_BASE_URL . 'include/js/stripe-processing.js'
    );
    wp_localize_script(
      'stripe-processing',
      'stripe_vars',
      array('publishable_key' => $publishable_key,)
    );
/*
    // sift science - javascript snippet
    wp_enqueue_script(
      'sift-science',
      STRIPE_BASE_URL . 'include/js/sift-science.js'
    );

    global $current_user;
    get_currentuserinfo();

    $url = parse_url(site_url());

    $sift_science_user = get_current_user_id()
                       ? "{$current_user->user_login}@{$url['host']}"
                       : '';

    wp_localize_script(
      'sift-science',
      'sift_science',
      array(
        'account_id' => $stripe_options['sandbox_sift_science_js'],
        'api_key'    => $stripe_options['sandbox_sift_science_rest'],
        'session_id' => session_id(),
        'user_id'    => $sift_science_user
      )
    );
 */
    // css
    wp_register_style( 'press-pay',	STRIPE_BASE_URL . 'press-pay.css');
  	wp_enqueue_style( 'press-pay' );
  }
}
?>
