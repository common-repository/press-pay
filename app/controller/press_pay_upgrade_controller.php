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
    DebugModel::debug("PressPayUpgradeListener::post()");
    $this->set_option( 'payment', 'paid' );
    header("HTTP/1.1 202 OK");
  }
}?>
