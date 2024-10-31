<?php
require_once(STRIPE_BASE_DIR . "/app/view/email.view.php");
require_once(STRIPE_BASE_DIR . "/app/model/debug.model.php");
require_once(STRIPE_BASE_DIR . "/app/model/stripe.api.model.php");
require_once( STRIPE_BASE_DIR . '/app/controller/controller.php' );
class EmailController extends Controller
{
 /*****************************************************************************
  * private variables, associated getters and setters.                        *
  *****************************************************************************/
  private $shipping_address = false;
  private $shipping_line1 = false;
  private $shipping_city = false;
  private $shipping_state = false;
  private $shipping_zip = false;
 /****************************************************************************
  * __construct()                                                            *
  ****************************************************************************/
  public function __construct(){
    $this->addMixin(new EmailModel());
    $this->addMixin(new EmailView());
    $this->addMixin(new DebugModel());
    $this->addMixin(new StripeAPIModel());
    // Setup defaults for email.
    add_filter ("wp_mail_content_type",
      array($this, "presspay_mail_content_type"));
    add_filter ("wp_mail_from", array($this, "presspay_mail_from"));
    add_filter("wp_mail_from_name", array($this, "presspay_mail_from_name"));
    $this->debug( 'EmailController::__contruct()' );
  }
 /*****************************************************************************
  * Set WordPress Mail's content type, from, and from name                    *
  *****************************************************************************/
  public function presspay_mail_content_type() {
    return "text/html";
  }
  public function presspay_mail_from() {
    return get_bloginfo('admin_email');
  }
  public function presspay_mail_from_name() {
    return html_entity_decode(get_bloginfo('name'));
  }
 /***************************************************************************
   * send_emails()                                                           *
   *                                                                         *
   * Once the user has given us their email, save it on Stripe's server.     *
   * If the user has entered an email or updated their email, send them      *
   * a confirmation email.                                                   *
   ***************************************************************************/
  public function send_emails($customer, $transaction, $product_id, $description){
    $this->debug("send_emails()");
    // grab state and store in local variables.
    // TODO: find a better way to pass the product info.
    $this->set_product_id( $product_id );
    $this->set_description( $description );
    $this->set_customer_id( $customer->stripe_client_id );

    $this->customer_email = $customer->email;
    $this->shipping_address = 'different';
    $this->shipping_line1 = $customer->shipping_address_line_1;
    $this->shipping_city = $customer->shipping_address_city;
    $this->shipping_state = $customer->shipping_address_state;
    $this->shipping_zip = $customer->shipping_address_zip;

    $stripe_customer = $this->get_remote_stripe_customer( $this );
    $this->set_stripe_customer( $stripe_customer );
    $this->set_remote_stripe_email( $this );
    //send emails
    $this->email_order_to_admin();
    $this->email_receipt_to_customer();
    return true;
  }
  /****************************************************************************
   * private functions                                                        *
   * **************************************************************************/
 /*****************************************************************************
   * email_receipt_to_customer()                                              *
   *                                                                          *
   * Create a receipt from the charge object and email it to the customer     *
   * if they have set their email address.                                    *
   ****************************************************************************/
  private function email_receipt_to_customer(){
    // we can only send an email if it exists in the customer record
    if(isset($this->customer_email)){
      $last_charge = $this->get_last_charge($this);
      // amount comes in as amount in cents, so we need to convert to dollars
      $amount = $last_charge->amount / 100;
      $subject = "Payment Receipt";
      $stripe_customer = $this->get_stripe_customer();

      $body = STRIPE_BASE_DIR . '/app/view/email/receipt.php';

      setlocale(LC_MONETARY, 'en_US');

      ob_start();
      include( STRIPE_BASE_DIR . '/app/view/email/layout.php' );
      $message = ob_get_clean();

      wp_mail($this->customer_email, $subject, $message);
    }else{
     $this->debug("send_customer_receipt() failed. Stripe Customer: " . $customer->id .
            " does not have email set.");
    }
  }
  /***************************************************************************
   * email_order_to_admin() - sends the blog admin an email about the order. *
   * *************************************************************************/
  private function email_order_to_admin() {
    $this->debug("email_order_to_admin()");

    $last_charge     = $this->get_last_charge($this);
    $this->debug("1");
    $amount          = $last_charge->amount / 100;
    $this->debug("2");
    $stripe_customer = $this->get_stripe_customer();
    $this->debug("3");
    $subject         = "Yer Makin' Money";
    $this->debug("4");
    $body            = STRIPE_BASE_DIR . '/app/view/email/order.php';
    $this->debug("5");

    ob_start();
    include( STRIPE_BASE_DIR .'/app/view/email/layout.php' );
    $message = ob_get_clean();
    $this->debug("6");
    wp_mail(get_bloginfo('admin_email'), $subject, $message);
    $this->debug(get_bloginfo('admin_email'));
  }
   /*
   * send_event_receipt(event_id)
   *
   * Take an event_id and poll stripe.com for information about that event. Then,
   * send a receipt to the customer associated with the event.
   */
  function send_event_receipt($event_id){
   $this->debug("send_event_receipt(event_id)");

    $this->authenticate_stripe( $this );

    // process event here
    try {
     // to verify this is a real event, we re-retrieve the event from Stripe
      $event = Stripe_Event::retrieve($event_id);
      $invoice = $event->data->object;

      // successful payment, both one time and recurring payments
      if($event->type == 'charge.succeeded') {
        send_charge_receipt($event->data->object);
      }
      // failed payment
      if($event->type == 'charge.failed') {
        // send a failed payment notice email here
        // retrieve the payer's information
        $customer = Stripe_Customer::retrieve($invoice->customer);
        $email = $customer->email;

        $subject = 'Failed Payment';
        $headers = 'From: "' . html_entity_decode(get_bloginfo('name'))
          .	'" <' . get_bloginfo('admin_email') . '>';
        $message = "Hello " . $customer_name . "\n\n";
        $message .= "We have failed to process your payment of " . $amount . "\n\n";
        $message .= "Please get in touch with support.\n\n";
        $message .= "Thank you.";

        wp_mail($email, $subject, $message, $headers);
      }
    } catch (Exception $e) {
      // something failed, perhaps log a notice or email the site admin
    }
  }
 /*****************************************************************************
  * private functions for mass assignment.                                    *
  *****************************************************************************/
  private function copy_get_vars(){
    $this->debug( 'copy_get_vars()' );
    $this->set_product_id( $_GET['product_id'] );
    $this->set_description( $_GET['description'] );
    $this->set_customer_id( $_GET['customer_id'] );
  }

  private function copy_post_vars(){
    $this->customer_email = $_POST['email'];
    if ( (isset($_POST['shipping-address']))
      && ($_POST['shipping-address'] == 'different') )
    {
      $this->shipping_address = $_POST['shipping-address'];
      $this->shipping_line1 = $_POST['shipping_line_1'];
      $this->shipping_city = $_POST['shipping_city'];
      $this->shipping_state = $_POST['shipping_state'];
      $this->shipping_zip = $_POST['shipping_zip'];
    }
  }
  /***************************************************************************
   * getters                                                                 *
   ***************************************************************************/
  public function get_shipping_line1(){
    return $this->shipping_line1;
  }
  public function get_shipping_zip(){
    return $this->shipping_zip;
  }
   public function get_shipping_state(){
    return $this->shipping_state;
  }
  public function get_shipping_city(){
    return $this->shipping_city;
  }
}
?>
