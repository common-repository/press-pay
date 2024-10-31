<?php
class PressPayView{
  /*
   * stripe_button_form(amount, image, desc, product_id, description)
   *
   * Show the stripe button wrapped in a form
   */
  public function stripe_button_form( $controller )
  {
    $controller->debug( 'stripe_button_form( $controller )' );
    ob_start(); ?>
    <form method="POST" id="stripe-button-form">
      <script
        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
        data-key="<?php echo $controller->publishable_key; ?>"
        data-name="<?php echo esc_attr( $controller->get_headline() ); ?>"
        data-amount="<?php echo esc_attr( $controller->get_amount() ); ?>"
        data-description="<?php echo esc_attr( $controller->get_description() ); ?>"
        data-image="<?php echo $controller->get_image(); ?>"
        data-billing-address="true"
        data-shipping-address="true"
      ></script>
      <input type="hidden" name="action" value="stripe"/>
      <input type="hidden" name="redirect"
             value="<?php echo(get_permalink()); ?>"/>
      <input type="hidden" name="amount"
             value="<?php echo( base64_encode( $controller->get_amount() ) ); ?>"/>
      <input type="hidden" name="product_id"
             value="<?php echo( $controller->get_product_id() ); ?>"/>
      <input type="hidden" name="description"
             value="<?php echo( $controller->get_description() ); ?>"/>
      <input type="hidden" name="stripe_nonce"
             value="<?php echo wp_create_nonce('stripe-nonce'); ?>"/>
    </form>
    <?php echo ob_get_clean();
  }

  public function thank_you(){
    ob_start(); ?>
    <script type="text/javascript">
    /*
      $.ajax({
          type : "POST",
          url  : "https://api.siftscience.com/v203/events",
          data : '{"$type":"$create_order", "$api_key":"a268fa1205541cc1", "$user_id" : "test@example.com", "$order_id" : "test order", "$user_email" : "test@example.com", $amount : 314000000, "$currency_code" : "USD", "$items" : [{"$item_id" : "12344321", "$product_title"  : "Microwavable Kettle Corn: Original Flavor","$price" : 49900000, "$upc" : "097564307560", "$sku" : "03586005", "$brand" : "Peters Kettle Corn", "$manufacturer" : "Peters Kettle Corn", "$category" : "Food and Grocery", "$tags" : ["Popcorn", "Snacks", "On Sale"], "$quantity" : 4}]}'
          success : function (){ alert("success"); },
  });
     */
    </script>

    <div class="dialog app"
         id="thank_you"
         title="<h1>Thank You</h1><h2>Your order is on it's way</h2>">
      <div class="panel">
        <article>
          Thanks for Shopping with Us.
        </article>
        <footer>
          <button class="blue submit close">
            <span>Close</span>
          </button>
        </footer>
      </div>
    </div>
    <?php echo ob_get_clean();
  }
}
?>
