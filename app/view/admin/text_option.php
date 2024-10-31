<tr>
  <th><? echo( $heading ); ?></th>
  <td>
    <input id="<? echo( $key ); ?>"
           name="stripe_settings[<? echo( $key ); ?>]"
           type="text" class="regular-text"
           value="<?php echo( isset( $stripe_options[$key] ) ? $stripe_options[$key] : '' ); ?>"
    />
    <label class="description"
           for="stripe_settings[<? echo( $key ); ?>]"
    ><? echo( $label ); ?></label>
  </td>
</tr>
