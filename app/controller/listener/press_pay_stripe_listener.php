<?php class PressPayStripeListener {
  public function __construct() {
    add_action( 'init', array( $this, 'add_stripe_endpoint' ) );
    add_action( 'template_redirect', array( $this, 'route' ) );
  }

  public function add_stripe_endpoint() {
    add_rewrite_endpoint( 'stripe-listener', EP_NONE );
  }

  public function route() {
    global $wp_query;
    if ($wp_query->query_vars['name'] != 'stripe-listener') return;

    switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
      return( $this->post() );
    default:
      return;
    }
  }

  public function post() {
    DebugModel::debug("PressPayListener::post()");

    Stripe::setApiKey( PressPaySettings::get_stripe_secret_key() );

    // retrieve the request's body and parse it as JSON
    $body = @file_get_contents('php://input');
    $event = json_decode($body);

    try {
      $stripe_event = Stripe_Event::retrieve( $event->id );
      DebugModel::debug( $stripe_event->type );
      if (preg_match( '/charge/', $stripe_event->type )) {
        PressPayTransaction::event( $stripe_event );
      }
      header("HTTP/1.1 202 OK");
    } catch ( Exception $e ) {
      DebugModel::debug( "test event?" );
      header("HTTP/1.1 200 OK");
    }
  }
}?>
