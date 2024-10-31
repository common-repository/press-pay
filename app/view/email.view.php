<?php
class EmailView{
  /***************************************************************************
   * email_header( $subject )                                                *
   * *************************************************************************/
  public function email_header($subject){  
    ob_start();?>
      <html style="background:#444">
      	<head>
      	<title><?php echo $subject ?></title>
     	</head>
     	<body>
    		<div id="email_container">
		  	<div id="email_header" style="width:570px; padding:0 0 0 20px; margin:50px auto 12px auto">
			  	<span style="background:#585858; color:#fff; padding:12px;font-family:trebuchet ms; letter-spacing:1px; 
				  	-moz-border-radius-topleft:5px; -webkit-border-top-left-radius:5px; 
				  	border-top-left-radius:5px;moz-border-radius-topright:5px; -webkit-border-top-right-radius:5px; 
				  	border-top-right-radius:5px;">
					<?php echo get_bloginfo('name') ?>
				</div>
  			</div>
  			<div style="width:550px; padding:0 20px 20px 20px; background:#fff; margin:0 auto; border:3px #000 solid;
	  			moz-border-radius:5px; -webkit-border-radus:5px; border-radius:5px; color:#454545;line-height:1.5em; " id="email_content">
				<h1 style="padding:5px 0 0 0; font-family:georgia;font-weight:500;font-size:24px;color:#000;border-bottom:1px solid #bbb">
				  <?php echo $subject ?>
        </h1>
    <?php echo ob_get_clean();
  }
  /***************************************************************************
   * order( $controller )                                                    *
   * *************************************************************************/
  public function order( $controller ){
    $controller->debug('order( $controller )');
    $stripe_customer = $controller->get_stripe_customer();
    ob_start();?>
      Order Info:<br><br>
      <div style="padding-left: 5em;">
      	Item ID: <?php echo($controller->get_product_id()); ?><br>
	      Item Description: <?php echo($controller->get_description()); ?><br>
        <br>
        Customer Name: <?php echo($stripe_customer->active_card->name); ?><br>
	      Customer Email: <?php echo($stripe_customer->email); ?><br>
        <br>
      </div>
    <?php echo ob_get_clean();
  }
  /***************************************************************************
   * address( $type, $controller )                                           *
   *                                                                         *
   * renders Shipping address or Billing Address according to $type.         *
   * *************************************************************************/
  public function address($type, $controller){
    $controller->debug('address($type, $controller)');
    $isShipping = ($type == "Shipping" ? true : false );
      ob_start();?>
        <br>
        <div style="padding-left: 5em">
          <?php echo( $isShipping ? "Shipping" : "Billing" ) ?> Address:
	        <div style="padding-left: 5em;">
          <?php
            echo( $isShipping ? $controller->get_shipping_line1()
                              : $controller->get_billing_line1() );
            echo( "<br>" );
            echo( $isShipping ? $controller->get_shipping_city()
                              : $controller->get_billing_city() );
            echo( ", " );
            echo( $isShipping ? $controller->get_shipping_state()
                              : $controller->get_billing_state() );
            echo( " " );
            echo( $isShipping ? $controller->get_shipping_zip()
                              : $controller->get_billing_zip() );
            echo( "<br>" );
          ?>
          </div>
        </div>
      <?php echo ob_get_clean();
  }
  /***************************************************************************
   * email_footer()                                                          *
   * *************************************************************************/
  public function email_footer(){
    ob_start();?>
			<p style="">
			  Warm Regards,<br>
				The <?php echo(get_bloginfo('name')); ?> Team
			</p>
			<div style="text-align:center; border-top:1px solid #eee;padding:5px 0 0 0;" id="email_footer"> 
				<small style="font-size:11px; color:#999; line-height:14px;">
		      You have received this email because you are a member of <?php echo(get_bloginfo('name'));?>.
		      If you would like to stop receiving emails from us, feel free to 
		      <a href="" style="color:#666">unregister</a> from our mailing list
		    </small>
      </div>
      </div>
		  </div>
	    </body>
      </html>
    <?php echo ob_get_clean();
  }
  /***************************************************************************
   * salutation( $controller, $amount )                                      *
   * *************************************************************************/
  public function salutation( $controller, $amount ){
    ob_start();?>
      Hi <?php echo( $controller->get_name() ) ?>,<br><br>

      Your order is on its way. We've charged 
      <?php echo( money_format( '%.2n', $amount ) ); ?> to your card ending in 
      <?php echo( $controller->get_last4() ); ?>.<br> <br>
    <? echo ob_get_clean();
  }
  /***************************************************************************
   * charge_info( $controller, $amount )                                     *
   * *************************************************************************/
  public function charge_info( $controller, $amount ){
    ob_start();?>
      <br>
      Charge Info:<br>
      <div style="padding-left: 5em;">
        Card: <?php echo( "**** **** **** " . $controller->get_last4() ); ?><br>
        Amount: <?php echo( money_format( '%.2n', $amount ) ); ?><br>
      </div>
    <?php echo ob_get_clean();
  }
  /***************************************************************************
   * closing()                                                               *
   * *************************************************************************/
  public function closing(){
    ob_start();?>
      <br>
      We appreciate your business and - as always - please don't hesitate to contact us if there's anything else that we can do for you.<br>
      <br>
    <?php echo ob_get_clean();
  }
}
?>
