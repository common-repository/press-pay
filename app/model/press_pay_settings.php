<?php
require_once(STRIPE_BASE_DIR . '/app/model/debug.model.php');
class PressPaySettings {
  public function get_stripe_publishable_key() {
    $stripe_options = get_option( 'stripe_settings' );

    DebugModel::debug($stripe_options);

    if ($stripe_options['payment'] == 'connect') {
      $publishable_key = $stripe_options['test_mode']
                       ? $stripe_options['connect_test_pk']
                       : $stripe_options['connect_live_pk'];
    } else {
      $publishable_key = $stripe_options['test_mode']
                       ? $stripe_options['test_publishable_key']
                       : $stripe_options['live_publishable_key'];
    }
    return( trim( $publishable_key ) );
  }

  public function get_stripe_secret_key() {
    $stripe_options = get_option( 'stripe_settings' );

    if ($stripe_options['payment'] == 'connect') {
      $secret_key = $stripe_options['test_mode']
                  ? $stripe_options['connect_test_sk']
                  : $stripe_options['connect_live_sk'];
    } else {
      $secret_key = $stripe_options['test_mode']
                  ? $stripe_options['test_secret_key']
                  : $stripe_options['live_secret_key'];
    }
    return( trim( $secret_key ) );
  }
  public function test_mode() {
    $stripe_settings = get_option('stripe_settings');

    if (!isset($stripe_options['test_mode']) || $stripe_options['test_mode']) {
      return true;
    } else {
      return false;
    }
  }
}
?>
