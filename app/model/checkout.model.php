<?php
function authenticate_stripe(){
  require_once(STRIPE_BASE_DIR . '/lib/Stripe.php');

  $secret_key = PressPaySettings::get_stripe_secret_key();
  Stripe::setApiKey($secret_key);
}

?>
