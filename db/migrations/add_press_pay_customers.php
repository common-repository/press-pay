<?php
if (!class_exists( 'AddPressPayCustomers' )) {
  class AddPressPayCustomers {
    public function migrate() {
      $sql = "CREATE TABLE " . PRESS_PAY_CUSTOMER_TABLE . " (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        stripe_client_id text NOT NULL,
        wp_user_id bigint(20),
        email text,
        billing_name text,
        billing_address_line_1 text,
        billing_address_zip text,
        billing_address_city text,
        billing_address_state text,
        billing_address_country text,
        shipping_name text,
        shipping_address_line_1 text,
        shipping_address_zip text,
        shipping_address_city text,
        shipping_address_state text,
        shipping_address_country text,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        test bool NOT NULL,
        UNIQUE KEY id (id)
      );";
      dbDelta( $sql );
    }
    private function drop_table() {
      global $wpdb;
      $sql = "DROP TABLE " . PRESS_PAY_CUSTOMER_TABLE . ";";
      $wpdb->query( $sql );
    }
  }
}
?>
