var casper = require('casper').create();
var numCaptures = 0;
var info = 'INFO';
var fs = require('fs');
var url = 'http://www.leanpipeline.com/get-your-own-dot-com/';

casper.start(url);

// click the stripe button
casper.then(function () {
  var submitButton = 'button.stripe-button-el';
  busyWait(this, submitButton, function action(casper) {
    casper.click(submitButton);
    casper.echo('Clicked ' + submitButton, info);
  });
});

// fill out stripe's form.
casper.withFrame('stripe_checkout_app', function () {
  var emailID = 'input[name=email]';
  var email = 'andrew.d.dixon@gmail.com';

  busyWait(this, emailID, function (casper) {
    casper.sendKeys(emailID, email);
  });

  var paymentName = 'Test User';
  var paymentNameID = 'input[name=name]';

  busyWait(this, paymentNameID, function (casper) {
    casper.sendKeys(paymentNameID, paymentName);
    casper.echo('entered payment name', info);
  });

  var line1 = '123 Test Street';
  var line1ID = 'input[name=one-line-address]';

  busyWait(this, line1ID, function (casper) {
    casper.sendKeys(line1ID, line1);
    casper.echo('entered address line 1', info);
  });

  var zip = '12345';
  var zipSelector = 'input.shipping-zip';

  busyWait(this, zipSelector, function (casper) {
    casper.sendKeys(zipSelector, zip);
    casper.echo('entered zip', info);
  });

  var city = 'TestVille';
  var citySelector = 'input[name=city]';

  busyWait(this, citySelector, function (casper) {
    casper.sendKeys(citySelector, city);
    casper.echo('entered city', info);
  });

  var submitSelector = 'button[type=submit]';

  busyWait(this, submitSelector, function (casper) {
    casper.click(submitSelector);
    casper.echo('form submitted', info);
  });

  var cardNumber = '4242 4242 4242 4242';
  var cardNumberID = 'input[name=card_number]';

  busyWait(this, cardNumberID, function action(casper) {
    casper.sendKeys(cardNumberID, cardNumber);
    casper.echo('entered card number', info);
  });

  var expiration = '1214';
  var expirationSelector = 'input[name=cc-exp]';

  busyWait(this, expirationSelector, function (casper) {
    casper.sendKeys(expirationSelector, expiration);
    casper.echo('entered expiration', info);
  });
  var cvc = '123';
  var cvcID = 'input[name=cc-csc]';

  busyWait(this, cvcID, function (casper) {
    casper.sendKeys(cvcID, cvc);
    casper.echo('entered CVC', info);
  });

  busyWait(this, submitSelector, function (casper) {
    casper.click(submitSelector);
    casper.echo('form submitted', info);
  });
});

casper.wait(5000);

// close thank you window
casper.then(function () {
  var thanksID = 'button.blue.submit.close';

  busyWait(this, thanksID, function (casper) {
    casper.click(thanksID);
    casper.echo('done!', info);
  });
});

casper.run();

function captureFile() {
    return('spec/screenshots/leanpipeline.com' + ++numCaptures + '.png');
};

function busyWait(casper, selector, action) {
  casper.waitUntilVisible(
    selector,
    function then() {
      casper.echo(selector + ' is Visible', 'INFO');
      casper.captureSelector(captureFile(), selector);
      action(casper);
      casper.capture(captureFile());
    },
    function onTimeout() {
      casper.echo('Continue to Wait for ' + selector, 'INFO');
      casper.capture(captureFile());
      busyWait(casper, selector, function action(casper) { action(casper) });
    },
    5000
  );
};
