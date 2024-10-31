<?php
if (!class_exists( 'AddPressPayTransactions' )) {
  class AddPressPayTransactions {
    public function migrate() {
      $sql = "CREATE TABLE " . PRESS_PAY_TRANSACTION_TABLE . " (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        press_pay_customer_id bigint(20),
        type text NOT NULL,
        last4 text,
        amount bigint(20) NOT NULL,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        test bool NOT NULL,
        UNIQUE KEY id (id)
      );";

      dbDelta( $sql );
    }
    private function drop_table() {
      global $wpdb;
      $sql = "DROP TABLE " . PRESS_PAY_TRANSACTION_TABLE . ";";
      $wpdb->query( $sql );
    }
  }
}
?>
