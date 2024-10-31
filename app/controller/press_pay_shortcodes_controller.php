<?php
if (class_exists('PressPayShortcodeController')) return;

class PressPayShortcodeController {
  public function __construct() {
    add_shortcode( 'presspay', array($this, 'route') );
 }

  public function route( $shortcode_attributes, $content = null ) {
    DebugModel::debug( 'route()' );

    switch ( $_SERVER['REQUEST_METHOD'] ) {
    case 'GET':
      return( $this->get( $shortcode_attributes ) );
    case 'POST':
      if ($_POST['product_id'] == $shortcode_attributes['product_id']) return($this->post());
      break;
    default:
      die( "{$_SERVER['REQUEST_METHOD']} not supported by PressPay" );
    }
  }

  // maps to new in rails - new is a keyword in php :(
  public function get( $shortcode_attributes ) {
    DebugModel::debug( 'PressPayShortcodesController::get' );

    extract( shortcode_atts( array(
      'amount'      => '',
      'image'       => '',
      'headline'    => get_bloginfo( 'title' ),
      'product_id'  => '',
      'description' => get_bloginfo( 'description' )
    ), $shortcode_attributes ) );

    $redirect        = get_permalink();
    $base64_amount   = base64_encode( $amount );
    $stripe_nonce    = wp_create_nonce('stripe-nonce');
    $publishable_key = PressPaySettings::get_stripe_publishable_key();

    // abstract into render() method in base class.
    ob_start();
    include( STRIPE_BASE_DIR . '/app/view/shortcode/stripe_button_form.php' );
    return ob_get_clean();
  }

  // maps to create in rails
  public function post() {
    DebugModel::debug( 'PressPayShortcodesController::post()' );
    if (wp_verify_nonce($_POST['stripe_nonce'], 'stripe-nonce')) {
      try {
        Stripe::setApiKey( PressPaySettings::get_stripe_secret_key() );

        // create a stripe customer
        $stripe_client = Stripe_Customer::create( array( 'card' => $_POST['stripeToken'] ) );

        // charge the stripe customer
        $amount = base64_decode( $_POST['amount'] );
        $charge = Stripe_Charge::create(
          array(
            'amount'          => $amount,
            'currency'        => 'usd',
            'customer'        => $stripe_client->id,
            'application_fee' => 50,
            'description'     => $_POST['product_id'] . ' -- ' . $_POST['description'],
          )
        );

	DebugModel::debug('post');
	DebugModel::debug($_POST);

        // create a presspay customer
        $customer = new PressPayCustomer( array(
          'wp_user_id'               => null,
          'stripe_client_id'         => $stripe_client->id,
          'email'                    => $_POST['stripeEmail'],
          'billing_name'             => $_POST['stripeBillingName'],
          'billing_address_line_1'   => $_POST['stripeBillingAddressLine1'],
          'billing_address_zip'      => $_POST['stripeBillingAddressZip'],
          'billing_address_city'     => $_POST['stripeBillingAddressCity'],
          'billing_address_state'    => $_POST['stripeBillingAddressState'],
          'billing_address_country'  => $_POST['stripeBillingAddressCountry'],
          'shipping_name'            => $_POST['stripeShippingName'],
          'shipping_address_line_1'  => $_POST['stripeShippingAddressLine1'],
          'shipping_address_zip'     => $_POST['stripeShippingAddressZip'],
          'shipping_address_city'    => $_POST['stripeShippingAddressCity'],
          'shipping_address_state'   => $_POST['stripeShippingAddressState'],
          'shipping_address_country' => $_POST['stripeShippingAddressCountry']
        ));
        $customer->save();

        // create a presspay transaction
        $transaction = new PressPayTransaction( array(
          'type'                  => 'charge.initiated',
          'amount'                => $amount,
          'last4'                 => $charge->card->last4,
          'press_pay_customer_id' => $customer->id,
        ));
        $transaction->save();

        // create a presspay email
        $email = new PressPayEmail(
          array(
            'description'              => $_POST['description'],
            'product_id'               => $_POST['product_id'],
            'press_pay_customer_id'    => $customer->id,
            'press_pay_transaction_id' => $transaction->id,
          )
        );
        // send the emails
        $email->send(array('type' => 'receipt'));
        $email->send(array('type' => 'order'));
      }
      catch (Exception $e) {
        wp_die($e);
      }

      include( STRIPE_BASE_DIR . '/app/view/shortcode/thank_you.php' );
    }
  }
}
?>
