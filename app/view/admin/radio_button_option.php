<tr>
  <th><? echo( $heading ); ?></th>
  <td>
    <input
      type="radio"
      name="stripe_settings[<?echo $key; ?>]"
      value="1"
      <?php checked( isset( $stripe_options[$key] ) ? $stripe_options[$key] : '', 1); ?>
    >Test Mode
    <input
      type="radio"
      name="stripe_settings[<? echo $key; ?>]"
      value="0"
      <?php checked( isset( $stripe_options[$key] ) ? $stripe_options[$key] : '', 0); ?>
    >Live Mode<br />
  </td>
</tr>
