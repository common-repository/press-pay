<?php
require_once(STRIPE_BASE_DIR . '/app/view/presspay.view.php');
require_once( STRIPE_BASE_DIR . '/app/controller/controller.php' );
/*****************************************************************************
 * PressPayController extends Controller                                     *
 *****************************************************************************/
class PressPayController extends Controller
{
  public $publishable_key;
  public $email=false;

  public function __construct(){
    $this->stripe_options = get_option( 'stripe_settings' );
    $this->addMixin(new PressPayView());
    $this->addMixin( new DebugModel() );
    $this->addMixin( new StripeApiModel() );
    $this->set_publishable_key();

    add_shortcode( 'presspay', array($this, 'presspay_shortcode') );
    add_action( 'template_redirect', array($this, 'mobile_template_redirect') );
    add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
  }
  /***************************************************************************
   * load_scripts()                                                          *
   * *************************************************************************/
  public function load_scripts() {
    DebugModel::debug( 'press_pay_laod_scripts()' );

	  // check to see if we are in test mode
	  if(isset($this->stripe_options['test_mode']) && $this->stripe_options['test_mode']) {
		  $publishable = $this->stripe_options['test_publishable_key'];
  	} else {
	  	$publishable = $this->stripe_options['live_publishable_key'];
    }

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
      array('publishable_key' => $publishable,)
    );

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
        'account_id' => $this->stripe_options['sandbox_sift_science_js'],
        'api_key' => $this->stripe_options['sandbox_sift_science_rest'],
        'session_id' => session_id(),
        'user_id'    => $sift_science_user
      )
    );

    // css
    wp_register_style( 'press-pay',	STRIPE_BASE_URL . 'press-pay.css');
  	wp_enqueue_style( 'press-pay' );
  }

  public static function get_publishable_key()
  {
    $this->debug( 'get_publishable_key()' );
    if (!isset( $this->publishable_key )) $this->set_publishable_key();
    return $this->publishable_key;
  }

  public function presspay_shortcode( $atts, $content = null ) {
    $this->debug("presspay_shortcode($atts, $content = null )");

    $this->get_shortcode_attributes($atts);
    $this->set_publishable_key();

    ob_start();
    if(!isset($_GET['payment'])){
      // we haven't collected the payment information yet
      $this->stripe_button_form( $this );
    }elseif($_GET['payment'] == 'paid'){
      // process extra info and send emails.
      $this->thank_you();
    }
    return ob_get_clean();
  }

  private function set_email(){
    $this->authenticate_stripe( $this );

    $customer_id = $_GET['customer_id'];
    $product_id = $_GET['product_id'];

    //retrieve stripe customer's email if it exists
    $stripe_customer = Stripe_Customer::retrieve( $customer_id );
    $this->email = (isset($stripe_customer->email)
      ? $stripe_customer->email : "you@example.com");
  }
  /***************************************************************************
   * get_shortcode_attributes( $attributes )                                 *
   ***************************************************************************/
  private function get_shortcode_attributes( $attributes )
  {
    //extract values from short code.
    extract( shortcode_atts( array(
	    'amount' => '',
	    'image'	 => '',
	    'headline'   => get_bloginfo( 'title' ),
      'product_id' => '',
	    'description'	=> get_bloginfo( 'description' )
    ), $attributes ) );
    $this->set_amount( $amount );
    $this->set_image( $image );
    $this->set_headline( $headline );
    $this->set_product_id( $product_id );
    $this->set_description( $description );
  }
  /****************************************************************************
   * mobile_template_redirect()                                               *
   ****************************************************************************/
  public function mobile_template_redirect() {
    $this->debug("mobile_template_redirect()");
    global $wp_query;
    // check if it is not a page or if it is front page, then exit from the hook
    if ( !$wp_query->is_page() || $wp_query->is_front_page() ) {
      return;
    }
    // send the mobile page if we aren't on a desktop.
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (!preg_match('/Mobile/',$user_agent)
        || (!isset($_GET['payment'])) || ($_GET['payment'] !== 'paid')
        || (isset($_POST['email']))){
	    return;
    }
    $this->debug("template_redirect");
    $this->debug("user_agent: " . $user_agent);
    $this->mobile_confirmation();
    exit;
  }
  /***************************************************************************
   * set_publishable_key()                                                   *
   ***************************************************************************/
  private function set_publishable_key()
  {
    $this->debug( 'set_publishable_key()' );
    $this->publishable_key = $this->get_stripe_option( 'test_mode' )
                           ? $this->get_stripe_option( 'test_publishable_key' )
                           : $this->get_stripe_option( 'live_publishable_key' );
    trim( $this->publishable_key );
  }
}
?>
