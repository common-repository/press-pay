<tr>
  <th><? echo( $heading ); ?></th>
  <td>
    <input id="stripe_settings[<? echo( $key ); ?>]"
           name="stripe_settings[<? echo( $key ); ?>]"
           type="checkbox" value="1"
    <?php checked( 1, isset( $stripe_options[$key] ) ? $stripe_options[$key] : 'false' ); ?>/>
    <label class="description"
           for="stripe_settings[<? echo( $key ); ?>]"><?php echo( $label ); ?>
    </label>
  </td>
</tr>

