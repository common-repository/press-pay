<?php
require_once( STRIPE_BASE_DIR . '/lib/Stripe.php' );
class StripeAPIModel{
  public function create_remote_stripe_customer( $controller )
  {
    try
    {
      DebugModel::debug( 'create_remote_stripe_customer( $controller )' );
      $controller->authenticate_stripe( $controller );
      $controller->set_stripe_customer(
        Stripe_Customer::create( array('card' => $controller->get_stripe_token()) )
      );
    }
    catch( Exception $e ) {
      //      throw( $e );
      die( $e );
    }
  }
  /***************************************************************************
   * get_remote_stripe_customer( $controller )                               *
   *                                                                         *
   * Retrieve Stripe_Customer from Stripe and store it locally. This function*
   * expects $controller to implement get_customer_id() which should return  *
   * the Stripe_Customer::Id of the record that we want to retrieve.         *
   * *************************************************************************/
  public function get_remote_stripe_customer( $controller ){
    DebugModel::debug( 'get_remote_stripe_customer( $controller )' );
    Stripe::setApiKey(PressPaySettings::get_stripe_secret_key());
    $stripe_customer_id = $controller->get_customer_id();
    $stripe_customer = Stripe_Customer::retrieve( $stripe_customer_id );
    return $stripe_customer;
  }
  public function get_last_charge( $controller ){
    Stripe::setApiKey(PressPaySettings::get_stripe_secret_key());
    // get most recent charge to this customer
    $stripe_charges = Stripe_Charge::all(array(
      "count" => 1,
      "customer" => $controller->get_customer_id()
    ));
    // retrieve array of charges from returned data structure
    $charges = $stripe_charges->data;
    // grab the first element of the array (i.e. the last charge)
    $last_charge = array_shift($charges);
    return( $last_charge );
  }
  /***************************************************************************
   * authenticate_stripe( $controller )                                      *
   * *************************************************************************/
  public function authenticate_stripe( $controller ){
    DebugModel::debug( 'authenticate_stripe( $controller )' );
    if ( $controller->is_stripe_authenticated() ){
      return;
    } else {
      $secret_key = $controller->get_stripe_option( 'test_mode' )
                  ? $controller->get_stripe_option( 'test_secret_key' )
                  : $controller->get_stripe_option( 'live_secret_key' );
      Stripe::setApiKey($secret_key);
      $controller->stripe_is_authenticated();
    }
  }
  /***************************************************************************
   * charge_customer( $customer_id, $amount )                                *
   ***************************************************************************/
  public function charge_customer( $controller )
  {
    try
    {
      DebugModel::debug( 'charge_customer( $controller )' );
      $controller->authenticate_stripe( $controller );
      $charge = Stripe_Charge::create(
        array(
          'amount'   => $controller->get_amount(),
          'customer' => $controller->get_customer_id(),
          'currency' => 'usd'
        )
      );
    } catch( Exception $e ) {
      die( $e );
    }
  }
  public function set_remote_stripe_email( $controller ){
    DebugModel::debug("set_remote_stripe_email()");
    $stripe_customer = $controller->get_stripe_customer();
    $stripe_customer->email = $controller->get_customer_email();
    $stripe_customer->save();
  }
}
?>
