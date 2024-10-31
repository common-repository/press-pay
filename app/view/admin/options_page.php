<h3>Credit Card Processing</h3>
<form method="post" action="options.php">
  <?php settings_fields('stripe_settings_group'); ?>
  <fieldset>
    <ul>
      <li>
        Test Mode
        <input
          type="radio"
          name="stripe_settings[test_mode]"
          value="1"
          <?php checked(isset($stripe_options['test_mode'] ) ? $stripe_options['test_mode'] : '', 1); ?>
        >
        Live Mode
        <input
          type="radio"
          name="stripe_settings[test_mode]"
          value="0"
          <?php checked(isset($stripe_options['test_mode'] ) ? $stripe_options['test_mode'] : '', 0); ?>
        >
        <input type  = "submit"
               class = "button-primary"
               value = "Save"
        />
      </li>
    </ul>
  </fieldset>

  <div class = 'conect'>
  <h3>Connect</h3>
  <fieldset>
    <legend><h4>User ID</h4></legend>
    <ul>
      <li>
        <label for = "stripe_settings[connect_user_id]">Connect User ID</label>
        <input
          type     = "text"
          class    = "regular-text"
          disabled = 'disabled'
          value    = "<?php if (isset($stripe_options['connect_user_id'])) echo($stripe_options['connect_user_id']); ?>"
        />
        <input
          type     = "hidden"
          id       = "stripe_settings[connect_user_id]"
          name     = "stripe_settings[connect_user_id]"
          value    = "<?php if (isset($stripe_options['connect_user_id'])) echo($stripe_options['connect_user_id']); ?>"
        />
      </li>
    </ul>
  </fieldset>
  <fieldset>
    <legend><h4>Test Keys</h4></legend>
    <ul>
      <li>
        <label for = "stripe_settings[connect_test_sk]">Connect Test Secret Key</label>
        <input
          type     = "text"
          disabled = 'disabled'
          class    = "regular-text"
          value    = "<?php if (isset($stripe_options['connect_test_sk'])) echo($stripe_options['connect_test_sk']); ?>"
        />
        <input
          type     = "hidden"
          id       = "stripe_settings[connect_test_sk]"
          name     = "stripe_settings[connect_test_sk]"
          value    = "<?php if (isset($stripe_options['connect_test_sk'])) echo($stripe_options['connect_test_sk']); ?>"
        />
      </li>
      <li>
        <label for = "stripe_settings[connect_test_pk]">Connect Test Publishable Key</label>
        <input
          type     = "text"
          disabled = 'disabled'
          class    = "regular-text"
          value    = "<?php if (isset($stripe_options['connect_test_pk'])) echo($stripe_options['connect_test_pk']); ?>"
        />
        <input
          type     = "hidden"
          id       = "stripe_settings[connect_test_pk]"
          name     = "stripe_settings[connect_test_pk]"
          value    = "<?php if (isset($stripe_options['connect_test_pk'])) echo($stripe_options['connect_test_pk']); ?>"
        />
      </li>
    </ul>
  </fieldset>
  <fieldset>
    <legend><h4>Live Keys</h4></legend>
    <ul>
      <li>
        <label for = "stripe_settings[connect_live_sk]">Connect Live Secret Key</label>
        <input
          type     = "text"
          disabled = 'disabled'
          class    = "regular-text"
          value    = "<?php if (isset($stripe_options['connect_live_sk'])) echo($stripe_options['connect_live_sk']); ?>"
        />
        <input
          type     = "hidden"
          id       = "stripe_settings[connect_live_sk]"
          name     = "stripe_settings[connect_live_sk]"
          value    = "<?php if (isset($stripe_options['connect_live_sk'])) echo($stripe_options['connect_live_sk']); ?>"
        />
      </li>
      <li>
        <label for = "stripe_settings[connect_live_pk]">Connect Live Publishable Key</label>
        <input
          type     = "text"
          disabled = 'disabled'
          class    = "regular-text"
          value    = "<?php if (isset($stripe_options['connect_live_pk'])) echo($stripe_options['connect_live_pk']); ?>"
        />
        <input
          type     = "hidden"
          id       = "stripe_settings[connect_live_pk]"
          name     = "stripe_settings[connect_live_pk]"
          value    = "<?php if (isset($stripe_options['connect_live_pk'])) echo($stripe_options['connect_live_pk']); ?>"
        />
      </li>
      </ul>
      </br>
      <ul>
        <li>
          <?php
            $permalink = get_site_url();
            $permalink = preg_replace("/^http/", "", $permalink);
            $href = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_38T6O1nnMRY3eoBzWlgd0oiZCfLqJ05K&scope=read_write&state=";
          ?>
          <label></label>
          <a href="<?php echo($href . $permalink); ?>" class="stripe-connect"><span>Connect with Stripe</span></a>
        </li>
      </ul>
    </fieldset>
  </div>
  <input
    id    = "stripe_settings[payment]"
    name  = "stripe_settings[payment]"
    type  = "hidden"
    class = "regular-text"
    value = "<?php if (isset($stripe_options['payment'])) echo($stripe_options['payment']); ?>"
  >
</form>

<div <?php if(!ADMIN_TEST) echo("style ='display:none'"); ?>>

<div class="wrap">
  <h2>Stripe Settings</h2>
  <form id="stripe_settings_form" method="post" action="options.php">
    <?php settings_fields('stripe_settings_group'); ?>
    <h3>Credit Card Processing</h3>
    <table id="stripe_mode" class="form-table">
    <?php
      $heading = 'Select mode of opertation:';
      $key = 'test_mode';
      $option1 = 'Test Mode';
      $option2 = 'Live Mode';
      include( STRIPE_BASE_DIR . '/app/view/admin/radio_button_option.php' );
    ?>
    </table>
    <h3>Stripe API Keys</h3>
    <table id="stripe_keys" class="form-table">
      <?php // Test Keys
        $heading = 'Test Secret';
        $key     = 'test_secret_key';
        $label   = 'Paste your secret test key.';
        include( STRIPE_BASE_DIR . '/app/view/admin/text_option.php' );
        $heading = 'Test Publishable';
        $key     = 'test_publishable_key';
        $label   = 'Paste your publishable test key here.';
        include( STRIPE_BASE_DIR . '/app/view/admin/text_option.php' );
        if (isset($stripe_options['payment']) && ($stripe_options['payment'] == 'paid')){
          // Live Keys
          $heading = 'Live Secret';
          $key     = 'live_secret_key';
          $label   = 'Paste your live secret key.';
          include( STRIPE_BASE_DIR . '/app/view/admin/text_option.php' );
          $heading = 'Live Publishable';
          $key     = 'live_publishable_key';
          $label   = 'Paste your live publishable key here.';
          include( STRIPE_BASE_DIR . '/app/view/admin/text_option.php' );
        }
      ?>
      <tr>
        <th style = "<?php echo ADMIN_TEST ? '' : 'display: none';?>">Payment</th>
        <td style = "<?php echo ADMIN_TEST ? '' : 'display: none';?>">
          <input id = "stripe_settings[payment]"
              name  = "stripe_settings[payment]"
              type  = "<?php echo( ADMIN_TEST ? 'text' : 'hidden' ); ?>"
              class = "regular-text"
              value = "<?php echo( $stripe_options['payment'] ); ?>">
        </td>
      </tr>
      <tr>
        <th style = "<?php echo ADMIN_TEST ? '' : 'display: none';?>">Access Token</th>
        <td style = "<?php echo ADMIN_TEST ? '' : 'display: none';?>">
          <input id = "stripe_settings[access_token]"
              name  = "stripe_settings[access_token]"
              type  = "<?php echo( ADMIN_TEST ? 'text' : 'hidden' ); ?>"
              class = "regular-text"
              value = "<?php echo( $stripe_options['access_token'] ); ?>">
        </td>
      </tr>
    </table>
    <table class="form-table <?php echo( ADMIN_TEST ? '' : 'hidden' ); ?>">
      <tbody>
        <?php
          $heading = 'Allow Recurring';
          $key     = 'recurring';
          $label   = 'Allow Recurring Payments?';
          include( STRIPE_BASE_DIR . '/app/view/admin/checkbox_option.php' );
          $heading = 'One Time Sign-Up Fee';
          $key     = 'one_time_fee';
          $label   = 'charge a signup fee if checked';
          include( STRIPE_BASE_DIR . '/app/view/admin/checkbox_option.php' );
          $heading = 'Fee Amount';
          $key     = 'fee_amount';
          $label   = '';
          include( STRIPE_BASE_DIR . '/app/view/admin/text_option.php' );
        ?>
      </tbody>
    </table>
    <table id="sift_science_keys" class="form-table <?php echo( ADMIN_TEST ? '' : 'hidden' ); ?>">
      <th><h3>Sift Science API Keys</h3></th>
      <?php
        $heading = 'Sandbox Jabascript Snippet Key';
        $key     = 'sandbox_sift_science_js';
        $label   = 'Paste your Sift Science Sandbox Jabascript Snippet Key';
        include( STRIPE_BASE_DIR . '/app/view/admin/text_option.php' );
        $heading = 'Sandbox REST API Key';
        $key = 'sandbox_sift_science_rest';
        $label = 'Paste your Sift Science Sandbox REST API Key';
        include( STRIPE_BASE_DIR . '/app/view/admin/text_option.php' );
      ?>
    </table>
    <p class="submit">
      <input id    = "submit_stripe_options"
             type  = "submit"
             class = "button-primary"
             value = "Save Options" />
    </p>
  </form>
</div>
</div>
