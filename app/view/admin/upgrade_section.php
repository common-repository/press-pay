<?php
  $permalink = get_site_url();
  $permalink = preg_replace("/^http/", "", $permalink);
  $href = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=ca_38T6O1nnMRY3eoBzWlgd0oiZCfLqJ05K&scope=read_write&state=";
?>
<a href="<?php echo($href . $permalink); ?>" class="stripe-connect"><span>Connect with Stripe</span></a>
<div <?php if(!ADMIN_TEST) echo("style ='display:none'"); ?>>
  </br>
  <h3>Upgrade to Accept Live Payments</h3>
  <a href=<?php echo($href . $permalink); ?>> Upgrade</a>
</div>
