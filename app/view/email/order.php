Order Info:<br><br>
<div style="padding-left: 5em;">
  Item ID: <?php echo($product_id); ?><br>
  Item Description: <?php echo($description); ?><br>
  <br>
  Customer Name: <?php echo($customer_name); ?><br>
  Customer Email: <?php echo($customer_email); ?><br>
  <br>
</div>
<br>
<div style="padding-left: 5em">
  Shipping Address:
  <div style="padding-left: 5em;">
    <?php echo($customer_name); ?>
    <br>
    <?php echo($shipping_address_line_1); ?>
    <br>
    <?php echo("{$shipping_address_city}, {$shipping_address_state} {$shipping_address_zip}"); ?>
    <br>
  </div>
</div>
<br>
<div style="padding-left: 5em">
  Billing Address:
  <div style="padding-left: 5em;">
    <?php echo($billing_name ? $billing_name : $customer_name); ?>
    <br>
    <?php echo($billing_address_line_1); ?>
    <br>
    <?php echo("{$billing_address_city}, {$billing_address_state} {$billing_address_zip}"); ?>
    <br>
  </div>
</div>
<br>
Charge Info:<br>
<div style="padding-left: 5em;">
  Card: **** **** **** <?php echo($last4); ?><br>
  Amount: <?php echo( money_format( '%.2n', $amount ) ); ?><br>
</div>
