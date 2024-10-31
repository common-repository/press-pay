Stripe.setPublishableKey(stripe_vars.publishable_key);
jQuery(document).ready(function($) {
  $("#stripe-confirmation-form").submit(function(event) {
	//close dialog box
	$('.dialog').dialog('close');
  });
});

jQuery(document).ready(function($) {
  var dialogOpts = {
	autoOpen: true,
        minHeight: 'auto',
	modal:	true,
	open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
  }
  $(".dialog").dialog(dialogOpts);
});
jQuery(document).ready(function($) {
  $('#dialog_link').click(function() {
	$('#stripe-dialog').dialog('open');
	return false;
  });
});

function stripeConfirmationHandler(e) {
  e.submit();
}

jQuery(document).ready(function($) {
  $('#stripe-confirmation-form').submit(function(e) {
    stripeConfirmationHandler(e);
  });

  $('#stripe-confirmation-submit').click(function(e) {
    stripeConfirmationHandler(e);
  });
});

jQuery(document).ready(function($) {
  $(".hidden-address").hide();
  $("#shipping-address").click(function (e) {
    if($(this).is(':checked')) {
      $(".hidden-address").show(e);
    }else{
      $(".hidden-address").hide(e);
    }
  });
});

jQuery(document).ready(function($) {
  $(".close").click(function (e) {
    $(".dialog").dialog('close');
  });
});
jQuery(document).ready(function($){
	$(".dialog.input:first", document.forms[0]).focus();
});
