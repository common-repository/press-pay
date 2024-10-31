<?php
class Controller extends Mixer
{
  /***************************************************************************
   * Interface expected by StripeAPIModel                                    *
   ***************************************************************************/
  protected $stripe_options;
  protected $stripe_authenticated;
  protected $customer_email;
  private $amount;
  private $product_id;
  private $description;
  private $stripeToken;
  private $customer_id;
  private $image;
  private $blog_description;
  private $stripe_customer;

  public function get_customer_email()
  {
    return $this->customer_email;
  }
  public function get_name()
  {
    return $this->stripe_customer->active_card->name;
  }
  public function get_last4()
  {
    return $this->stripe_customer->active_card->last4;
  }
  public function get_billing_zip(){
    return $this->stripe_customer->active_card->address_zip;
  }
   public function get_billing_state(){
    return $this->stripe_customer->active_card->address_state;
  }
   public function get_billing_line1(){
    return $this->stripe_customer->active_card->address_line1;
  }
  public function get_billing_city(){
    return $this->stripe_customer->active_card->address_city;
  }
  public function get_stripe_customer()
  {
    return $this->stripe_customer;
  }
  public function set_stripe_customer( $stripe_customer )
  {
    $this->stripe_customer = $stripe_customer;
  }
  public function get_blog_description()
  {
    return $this->blog_description;
  }
  public function get_image()
  {
    return $this->image;
  }
  public function get_customer_id(){
    DebugModel::debug( 'get_customer_id()' );

    if (isset( $this->stripe_customer )) {
      DebugModel::debug('stripe customer exists');
      return $this->stripe_customer->id;
    } else {
      DebugModel::debug('create new stripe customer');
      if (!isset( $this->customer_id )) {
        // create a new customer if our current user doesn't have one
        $customer = Stripe_Customer::create();
        $this->customer_id = $customer->id;
        // If the user is logged in, we store the newly created
        // '_stripe_customer_id' in their user info.
        if (is_user_logged_in ()) {
          update_user_meta( get_current_user_id(),
            '_stripe_customer_id', $this->customer_id );
        }
      }
      return $this->customer_id;
    }
if (!$this->customer_id) {
	    // create a new customer if our current user doesn't have one
	    $customer = Stripe_Customer::create(array('card' => $this->token));	
	    $this->customer_id = $customer->id;
	    // If the user is logged in, we store the newly created 
	    // '_stripe_customer_id' in their user info.
	    if (is_user_logged_in ()) {
        update_user_meta( get_current_user_id(), 
          '_stripe_customer_id', $this->customer_id );
	    }
    }  }
  public function get_amount()
  {
    return $this->amount;
  }
  public function get_product_id()
  {
    return $this->product_id;
  }
  public function get_headline()
  {
    return $this->headline;
  }
  public function get_description()
  {
    return $this->description;
  }
  public function get_stripe_token()
  {
    return $this->stripeToken;
  }
  public function set_image( $image )
  {
    $this->image = $image;
  }
  public function set_amount( $amount )
  {
    $this->amount = $amount;
  }
  public function set_product_id( $product_id )
  {
    $this->product_id = $product_id;
  }
  public function set_blog_description( $blog_description )
  {
    $this->blog_description = $blog_description;
  }
  public function set_headline( $headline )
  {
    $this->headline = $headline;
  }
  public function set_description( $description )
  {
    $this->description = $description;
  }
  public function set_customer_id( $id )
  {
    $this->customer_id = $id;
  }
  public function set_stripeToken( $stripeToken )
  {
    $this->stripeToken = $stripeToken;
  }
  public function get_stripe_option( $key )
  {
    if (!isset( $this->stripe_options[$key] )) $this->get_options();
    return $this->stripe_options[$key];
  }
  public function get_options()
  {
    $this->stripe_options = isset( $this->stripe_options )
                                 ? $this->stripe_options
                                 : get_option( 'stripe_settings' );
    return $this->stripe_options;
  }
  public function set_option( $key, $value )
  {
    $this->get_options();
    $this->stripe_options[$key] = $value;
    update_option( 'stripe_settings', $this->stripe_options );
  }
  public function is_stripe_authenticated(){
    return isset( $this->stripe_authenticated ) ? $this->stripe_authenticated : false ;
  }
  public function stripe_is_authenticated(){
    $this->stripe_authenticated = true;
  }
 }
?>
