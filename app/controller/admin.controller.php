<?php class AdminController extends Controller {
  private $remote_response;
  private $response_info;
  private $upgrade_server;
  private $admin_email;

  public function __construct() {
    // Settings
    add_action('admin_init', array ($this, 'register_settings'));
    // Settings Page
    add_action('admin_menu', array ($this, 'menu_setup'));
    add_action(
      'admin_print_scripts-settings_page_stripe-settings',
      array( $this, 'add_javascript')
    );
    add_action('admin_enqueue_scripts', array($this, 'add_javascript'));
    // User Profile
    add_action('show_user_profile', array ($this, 'add_stripe_customer_id_to_user'));
    add_action('edit_user_profile', array ($this, 'add_stripe_customer_id_to_user'));

    add_action('personal_options_update',  array ($this, 'save_stripe_customer_id'));
    add_action('edit_user_profile_update', array ($this, 'save_stripe_customer_id'));
  }

  public function register_settings() {
    DebugModel::debug('register_settings()');
  	// creates our settings in the options table
	  register_setting('stripe_settings_group', 'stripe_settings');
  }

  public function menu_setup() {
    DebugModel::debug('menu_setup()');
    add_options_page(
      'Stripe Settings',                    // $page_title
      'Stripe Settings',                    // $menu_title
      'manage_options',                     // $capability
      'stripe-settings',                    // $menu_slug
      array( $this, 'render_options_page' ) // $function
    );
  }

  public  function add_javascript()
  {
    DebugModel::debug('add_javascript()');
    //We can include as many Javascript files as we want here.
    wp_enqueue_script(
      'pluginscript',
      STRIPE_BASE_URL . '/include/js/stripe-settings.js',
      array('jquery')
    );
    wp_register_style(
      'options_page',
      STRIPE_BASE_URL . '/app/assets/stylesheets/options_page.css'
    );
    wp_enqueue_style('options_page');
  }

  public function render_options_page() {
    $stripe_options = get_option('stripe_settings');
    include( STRIPE_BASE_DIR . '/app/view/admin/options_page.php' );
  }
  public function add_stripe_customer_id_to_user( $user ) {
    include( STRIPE_BASE_DIR . "/app/view/admin/stripe_customer_id.php" );
  }
  public function save_stripe_customer_id( $user_id ) {
    if (!current_user_can( 'edit_user', $user_id )) return FALSE;
    update_usermeta( $user_id, '_stripe_customer_id',
                     $_POST['_stripe_customer_id'] );
  }
  private function copy_post_vars() {
    $this->admin_email = get_bloginfo( 'admin_email' );
    $this->set_amount( $_POST['amount'] );
    $this->set_product_id( $_POST['product_id'] );
    $this->set_description( $_POST['description'] );
    $this->set_stripeToken( $_POST['stripeToken'] );
  }
  /***************************************************************************
   * setters                                                                 *
   * *************************************************************************/
  public function set_remote_response( $remote_response )
  {
    $this->remote_response = $remote_response;
  }
  public function set_response_info( $response_info )
  {
    $this->response_info = $response_info;
  }
  /***************************************************************************
   * getters                                                                 *
   * *************************************************************************/
  public function get_upgrade_server()
  {
    return $this->upgrade_server;
  }
  public function get_admin_email()
  {
    return $this->admin_email;
  }

  public function post_payment_to_remote( $controller )
  {
    $controller->debug( 'post_payment_to_remote( $controller )' );
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $controller->get_upgrade_server());
    curl_setopt($curl_handle, CURLOPT_POST, 1);
    $post_fields = "adminEmail=" . $controller->get_admin_email() . "&"
                 . "amount=" . $controller->get_amount() . "&"
                 . "product_id=" . $controller->get_product_id() . "&"
                 . "description=" . $controller->get_description() . "&"
                 . "stripeToken=" . $controller->get_stripe_token();
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $post_fields );
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    $controller->set_remote_response( curl_exec( $curl_handle ) );
    $controller->set_response_info( curl_getinfo( $curl_handle ) );
    curl_close($curl_handle);
  }
}
if (!defined('ADMIN_TEST' )) {
  define('ADMIN_TEST', false);
}?>
