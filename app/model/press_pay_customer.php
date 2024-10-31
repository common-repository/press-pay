<?php
if (!class_exists( 'PressPayCustomer' )) {
  class PressPayCustomer {
    public $id = false;
    public $stripe_client_id;
    public $wp_user_id;
    public $email;
    public $billing_name;
    public $billing_address_line_1;
    public $billing_address_zip;
    public $billing_address_city;
    public $billing_address_state;
    public $billing_address_country;
    public $shipping_name;
    public $shipping_address_line_1;
    public $shipping_address_zip;
    public $shipping_address_city;
    public $shipping_address_state;
    public $shipping_address_country;
    public $created_at;
    public $updated_at;
    public $test;

    public function __construct( $params = array() ) {
      DebugModel::debug("PressPayCustomer::__construct");

      $default_vals = array(
        'id'                       => false,
        'stripe_client_id'         => '',
        'wp_user_id'               => '',
        'email'                    => '',
        'billing_name'             => '',
        'billing_address_line_1'   => '',
        'billing_address_zip'      => '',
        'billing_address_city'     => '',
        'billing_address_state'    => '',
        'billing_address_country'  => '',
        'shipping_name'            => '',
        'shipping_address_line_1'  => '',
        'shipping_address_zip'     => '',
        'shipping_address_city'    => '',
        'shipping_address_state'   => '',
        'shipping_address_country' => '',
        'created_at'               => '',
        'updated_at'               => '',
        'test'                     => true,
      );

      $params = array_merge( $default_vals, $params );

      $this->id                       = $params['id'];
      $this->stripe_client_id         = $params['stripe_client_id'];
      $this->wp_user_id               = $params['wp_user_id'];
      $this->email                    = $params['email'];
      $this->billing_name             = $params['billing_name'];
      $this->billing_address_line_1   = $params['billing_address_line_1'];
      $this->billing_address_zip      = $params['billing_address_zip'];
      $this->billing_address_city     = $params['billing_address_city'];
      $this->billing_address_state    = $params['billing_address_state'];
      $this->billing_address_country  = $params['billing_address_country'];
      $this->shipping_name            = $params['shipping_name'];
      $this->shipping_address_line_1  = $params['shipping_address_line_1'];
      $this->shipping_address_zip     = $params['shipping_address_zip'];
      $this->shipping_address_city    = $params['shipping_address_city'];
      $this->shipping_address_state   = $params['shipping_address_state'];
      $this->shipping_address_country = $params['shipping_address_country'];
      $this->created_at               = $params['created_at'];
      $this->updated_at               = $params['updated_at'];
      $this->test                     = $params['test'];
    }

    public function save() {
      global $wpdb;
      $data = array(
        'stripe_client_id'         => $this->stripe_client_id,
        'wp_user_id'               => $this->wp_user_id,
        'email'                    => $this->email,
        'billing_name'             => $this->billing_name,
        'billing_address_line_1'   => $this->billing_address_line_1,
        'billing_address_zip'      => $this->billing_address_zip,
        'billing_address_city'     => $this->billing_address_city,
        'billing_address_state'    => $this->billing_address_state,
        'billing_address_country'  => $this->billing_address_country,
        'shipping_name'            => $this->shipping_name,
        'shipping_address_line_1'  => $this->shipping_address_line_1,
        'shipping_address_zip'     => $this->shipping_address_zip,
        'shipping_address_city'    => $this->shipping_address_city,
        'shipping_address_state'   => $this->shipping_address_state,
        'shipping_address_country' => $this->shipping_address_country,
        'updated_at'               => current_time('mysql'),
        'test'                     => PressPaySettings::test_mode(),
      );

      if ($this->id) {
        // If we have an id we are updating an existing record.
        $db_status = $wpdb->update(PRESS_PAY_CUSTOMER_TABLE, $data, array('id' => $this->id));
      } else {
        // If we don't have an id, we are creating a record.
        $data['created_at'] = current_time('mysql');
        $db_status = $wpdb->insert(PRESS_PAY_CUSTOMER_TABLE, $data);
        $this->id = $wpdb->insert_id;
      }
      return($db_status);
    }

    public function find_by_stripe_customer_id( $stripe_customer_id ) {
      global $wpdb;
      $customer = $wpdb->get_row(
        "SELECT * from " . PRESS_PAY_CUSTOMER_TABLE .
        " WHERE stripe_client_id = '$stripe_customer_id'"
      );
      return new PressPayCustomer( get_object_vars( $customer ) );
    }

    public function find($id) {
      global $wpdb;
      $customer = $wpdb->get_row(
        "SELECT * FROM " . PRESS_PAY_CUSTOMER_TABLE .
        " WHERE id = '$id'"
      );
      return new PressPayCustomer(get_object_vars($customer));
    }
  }
}
?>
