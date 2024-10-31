<?php
if (!class_exists( 'PressPayTransaction' )) {
  class PressPayTransaction {
    public $id = false;
    public $press_pay_customer_id;
    public $amount;
    public $type;
    public $last4;
    public $created_at;
    public $updated_at;
    public $test;

    public function __construct( $params = array() ) {
      DebugModel::debug("PressPayTransaction::__construct");

      $default_vals = array(
        'id'                    => false,
        'press_pay_customer_id' => '',
        'amount'                => '',
        'type'                  => '',
        'last4'                 => '',
        'created_at'            => '',
        'updated_at'            => '',
        'test'                  => true,
      );

      $params = array_merge( $default_vals, $params );

      $this->id                    = $params['id'];
      $this->type                  = $params['type'];
      $this->amount                = $params['amount'];
      $this->last4                 = $params['last4'];
      $this->press_pay_customer_id = $params['press_pay_customer_id'];
      $this->created_at            = $params['created_at'];
      $this->updated_at            = $params['updated_at'];
      $this->test                  = $params['test'];
    }

    public function save() {
      global $wpdb;
      $data = array(
        'press_pay_customer_id' => $this->press_pay_customer_id,
        'type'                  => $this->type,
        'amount'                => $this->amount,
        'last4'                 => $this->last4,
        'updated_at'            => current_time( 'mysql' ),
        'test'                  => PressPaySettings::test_mode(),
      );

      if ($this->id) {
        $db_status = $wpdb->update(
          PRESS_PAY_TRANSACTION_TABLE, $data, array( 'id' => $this->id )
        );
      } else {
        $data['created_at'] = current_time( 'mysql' );
        $db_status          = $wpdb->insert( PRESS_PAY_TRANSACTION_TABLE, $data );
        $this->id           = $wpdb->insert_id;
      }
      return($db_status);
    }

    public function event( $event ) {
      DebugModel::debug( 'PressPayTransaction::event()' );

      $customer    = PressPayCustomer::find_by_stripe_customer_id($event->data->object->customer);
      $transaction = new PressPayTransaction(
        array(
          'press_pay_customer_id' => $customer->id,
          'amount'                => $event->data->object->amount,
          'type'                  => $event->type,
        )
      );

      if (!$transaction->save()) DebugModel::debug( "could not save transaction event" );
    }

    public function find($id) {
      global $wpdb;
      $transaction = $wpdb->get_row(
        "SELECT * FROM " . PRESS_PAY_TRANSACTION_TABLE .
        " WHERE id = '$id'"
      );
      return new PressPayTransaction(get_object_vars($transaction));
    }
  }
}
?>
