<?php class PressPayUpgradeListener {
  public function __construct() {
    add_action( 'init', array( $this, 'add_upgrade_endpoint' ) );
    add_action( 'template_redirect', array( $this, 'route' ) );
  }

  public function add_upgrade_endpoint() {
    add_rewrite_endpoint( 'upgrade', EP_NONE );
  }

  public function route() {
    global $wp_query;
    if ($wp_query->query_vars['name'] != 'upgrade') return;

    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      return( $this->post() );
    default:
      return;
    }
  }

  public function post()
  {
    $data = @file_get_contents('php://input');
    parse_str( $data );
    $stripe_settings = get_option( 'stripe_settings' );

    switch ($action) {
    case 'upgrade':
      $stripe_settings['payment'] = 'paid';
      update_option( 'stripe_settings', $stripe_settings );
      break;
    case 'downgrade':
      $stripe_settings['payment'] = '';
      update_option( 'stripe_settings', $stripe_settings );
      break;
    }
  }
}?>
