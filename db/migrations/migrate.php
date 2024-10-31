<?php
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once(STRIPE_BASE_DIR . '/db/migrations/add_press_pay_transactions.php');
require_once(STRIPE_BASE_DIR . '/db/migrations/add_press_pay_customers.php');
require_once(STRIPE_BASE_DIR . '/db/migrations/add_press_pay_emails.php');

function press_pay_migrate_database() {
  DebugModel::debug("press_pay_migrate_database()");

  $press_pay_db_version = "1.19";

  $add_press_pay_transactions = new AddPressPayTransactions;
  $add_press_pay_customers    = new AddPressPayCustomers;
  $add_press_pay_emails       = new AddPressPayEmails;

  $add_press_pay_transactions->migrate();
  $add_press_pay_customers->migrate();
  $add_press_pay_emails->migrate();

  if (!get_option('press_pay_db_version')) {
    add_option( "press_pay_db_version", $press_pay_db_version );
  } else if (get_option('press_pay_db_version') != $press_pay_db_version) {
    update_option( "press_pay_db_version", $press_pay_db_version );
  }
}
?>
