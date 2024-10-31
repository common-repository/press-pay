<form method="POST" id="stripe-button-form">
  <script
    src="https://checkout.stripe.com/checkout.js"
    class="stripe-button"
    data-key="<?php echo( $publishable_key ); ?>"
    data-name="<?php echo( $headline ); ?>"
    data-amount="<?php echo( $amount ); ?>"
    data-description="<?php echo( $description ); ?>"
    data-image="<?php echo( $image ); ?>"
    data-billing-address="true"
    data-shipping-address="true"
  ></script>
  <input type="hidden" name="action" value="stripe"/>
  <input type="hidden" name="redirect" value="<?php echo( $redirect ); ?>"/>
  <input type="hidden" name="amount" value="<?php echo( $base64_amount ); ?>"/>
  <input type="hidden" name="product_id" value="<?php echo( $product_id ); ?>"/>
  <input type="hidden" name="description" value="<?php echo( $description ); ?>"/>
  <input type="hidden" name="stripe_nonce" value="<?php echo( $stripe_nonce ); ?>"/>
</form>
