<h3><?php _e('Stripe', 'atomicbroadcast'); ?></h3>
<table class="form-table">
  <tr>
    <th>
      <label for="_stripe_customer_id">
        <?php _e('Stripe Customer ID', '_stripe_customer_id'); ?>
      </label>
    </th>
    <td>
      <input type="text"
             name="_stripe_customer_id"
               id="_stripe_customer_id"
            value="<?php echo esc_attr( get_the_author_meta( '_stripe_customer_id', $user->ID ) ); ?>"
            class="regular-text" /><br />
      <span class="description">
        <?php _e('Customer ID used by Stripe.', '_stripe_customer_id'); ?>
      </span>
    </td>
  </tr>
</table>
