<?php class PressPayUpgradeListener {
  public function __construct() {
    DebugModel::debug('UpgradeListener::__construct()');

    add_action( 'init', array( $this, 'add_upgrade_endpoint' ) );
    add_action( 'template_redirect', array( $this, 'route' ) );
  }

  public function add_upgrade_endpoint() {
    add_rewrite_endpoint( 'upgrade', EP_NONE );
  }

  public function route() {
    DebugModel::debug('route()');
    global $wp_query;
    DebugModel::debug($_SERVER);
    if ($_SERVER['REQUEST_URI'] != '/upgrade') return;

    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      $this->post();
      exit;
    default:
      return;
    }
  }

  public function post() {
    DebugModel::debug("post()");
    $data = @file_get_contents('php://input');
    DebugModel::debug($data);
    parse_str( $data );

    $stripe_settings = get_option( 'stripe_settings' );

    switch ($action) {
    case 'connect':
      DebugModel::debug('connect');
      $stripe_settings['payment']         = 'connect';
      $stripe_settings['connect_user_id'] = $stripe_user_id;
      $stripe_settings['access_token']    = $code;
      $stripe_settings['connect_test_sk'] = $sk_test;
      $stripe_settings['connect_test_pk'] = $pk_test;
      $stripe_settings['connect_live_sk'] = $sk_live;
      $stripe_settings['connect_live_pk'] = $pk_live;
      update_option('stripe_settings', $stripe_settings);
      header("HTTP/1.1 201 OK");
      break;
    case 'upgrade':
      $stripe_settings['payment'] = 'paid';
      update_option( 'stripe_settings', $stripe_settings );
      header("HTTP/1.1 201 OK");
      break;
    case 'downgrade':
      $stripe_settings['payment'] = '';
      update_option( 'stripe_settings', $stripe_settings );
      header("HTTP/1.1 201 OK");
      break;
    }
  }
}?>
