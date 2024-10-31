<?php if (class_exists('PressPayEmail')) return;
class PressPayEmail {
  public $id;
  public $body;
  public $test;
  public $created_at;
  public $product_id;
  public $updated_at;
  public $description;
  public $press_pay_customer_id;
  public $press_pay_transaction_id;

  public function __construct($params = array()) {
    DebugModel::debug('PressPayEmail::__construct()');

    $default_vals = array(
    'id'                        => false,
    'press_pay_customer_id'     => '',
    'press_pay_transacction_id' => '',
    'product_id'                => '',
    'description'               => '',
    'body'                      => '',
    'created_at'                => '',
    'updated_at'                => '',
    );

    $params = array_merge( $default_vals, $params );

    $this->press_pay_customer_id    = $params['press_pay_customer_id'];
    $this->press_pay_transaction_id = $params['press_pay_transaction_id'];
    $this->product_id               = $params['product_id'];
    $this->description              = $params['description'];
    $this->body                     = $params['body'];
    $this->created_at               = $params['created_at'];
    $this->updated_at               = $params['updated_at'];

    // Setup defaults for email.
    add_filter ("wp_mail_content_type",
      array($this, "presspay_mail_content_type"));
    add_filter ("wp_mail_from", array($this, 'admin_email'));
    add_filter("wp_mail_from_name", array($this, 'name'));
  }

  /*****************************************************************************
  * Set WordPress Mail's content type, from, and from name                    *
  *****************************************************************************/
  public function presspay_mail_content_type() {
    return "text/html";
  }
  public function name() {
    return html_entity_decode(get_bloginfo('name'));
  }
  public function admin_email() {
    $admin_email     = get_bloginfo('admin_email');
    $press_pay_email = str_replace('admin', 'press-pay', $admin_email);
    return $press_pay_email;
  }

  public function save() {
    global $wpdb;
    $data = array(
      'press_pay_customer_id'    => $this->press_pay_customer_id,
      'press_pay_transaction_id' => $this->press_pay_transaction_id,
      'body'                     => $this->body,
      'updated_at'               => current_time( 'mysql' ),
      'test'                     => PressPaySettings::test_mode(),
    );

    if ($this->id) {
      $db_status = $wpdb->update(
        PRESS_PAY_EMAIL_TABLE, $data, array( 'id' => $this->id )
      );
    } else {
      $data['created_at'] = current_time( 'mysql' );
      $db_status          = $wpdb->insert( PRESS_PAY_EMAIL_TABLE, $data );
      $this->id           = $wpdb->insert_id;
    }
    return($db_status);
  }

  public function send($params = array()) {
    DebugModel::debug("Email::send({$params['type']})");

    $customer    = PressPayCustomer::find($this->press_pay_customer_id);
    $transaction = PressPayTransaction::find($this->press_pay_transaction_id);

    $last4  = $transaction->last4;
    $amount = $transaction->amount / 100;

    switch ($params['type']) {
    case 'order':
      $to      = get_bloginfo('admin_email');
      $subject = "Yer Makin' Money";
      $body    = STRIPE_BASE_DIR . '/app/view/email/order.php';
      break;
    case 'receipt':
      $to      = $customer->email;
      $subject = 'Payment Receipt';
      $body    = STRIPE_BASE_DIR . '/app/view/email/receipt.php';
      break;
    default:
      DebugModel::debug("PressPayEmail::send can't support {$params['type']}");
    }

    $product_id     = $this->product_id;
    $description    = $this->description;
    $customer_name  = $customer->billing_name;
    $customer_email = $customer->email;

    $shipping_address_line_1 = $customer->shipping_address_line_1;
    $shipping_address_city   = $customer->shipping_address_city;
    $shipping_address_state  = $customer->shipping_address_state;
    $shipping_address_zip    = $customer->shipping_address_zip;

    $billing_name           = $customer->billing_name;
    $billing_address_line_1 = $customer->billing_address_line_1;
    $billing_address_city   = $customer->billing_address_city;
    $billing_address_state  = $customer->billing_address_state;
    $billing_address_zip    = $customer->billing_address_zip;

    ob_start();
    include( STRIPE_BASE_DIR .'/app/view/email/layout.php' );
    $message = ob_get_clean();

    wp_mail($to, $subject, $message);
  }
}
?>
