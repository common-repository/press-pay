<?php
if (!class_exists( 'AddPressPayEmails' )) {
  class AddPressPayEmails {
    public function migrate() {
      $sql = "CREATE TABLE " . PRESS_PAY_EMAIL_TABLE . " (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        press_pay_customer_id bigint(20),
        press_pay_transaction_id bigint(20),
        product_id bigint(20),
        description text,
        body blob,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        test bool NOT NULL,
        UNIQUE KEY id (id)
      );";

      dbDelta( $sql );
    }
    private function drop_table() {
      global $wpdb;
      $sql = "DROP TABLE " . PRESS_PAY_EMAIL_TABLE . ";";
      $wpdb->query( $sql );
    }
  }
}
?>
