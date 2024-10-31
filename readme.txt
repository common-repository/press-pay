=== Plugin Name ===
Contributors: presspay
Donate link: http://atomicbroadcast.net/
Tags: webstore, payment, stripe
Requires at least: 3.0.1
Tested up to: 3.8.1
Stable tag: 2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Press Pay is a fast and easy way to turn your WordPress Blog into a Web Store.
With a simple short code, you can process credit cards through Stripe.

== Description ==

Press Pay is a fast and easy way to turn your WordPress Blog into a Web Store. After signing up for an account with Stripe, 
insert a shortcode into any Page or Post and you'll be given a payment button which pops up a credit card form when pressed. 
Your customers enter their credit card information in the form and Stripe processes the sale. Simple, Easy, Straightforward.

== Installation ==

This section describes how to install the Press Pay and get it working.

1. Search and install PressPay on WordPress.org.
2. Activate press-pay through the 'Plugins' menu in WordPress
3. Navigate to the Plugins Menu. Press the "Connect with Stripe" Button.
5. Place `[presspay amount="2000" product_id="007" description="Wonder Widget"]` in any of your pages or posts.

== Frequently Asked Questions ==

= What parameters does the presspay shortcode support? =

amount:      The price you will charge the client (in cents).
image:       A relative URL pointing to a square image of your brand or product. The recommended
             minimum size is 128x128px.
headline:    Name of the product (defaults to the blog title).
description: Description of the product (defaults to blog description).
product_id:  Unique identifier for the product (required if you add multiple payment buttons to
             a page.

= Can I put more than one button on a page? =

Yes. Just make sure that each shortcode has a unique product id.
Ex:
  [presspay amount="2000" product_id="1" description="stuff"]
  [presspay amount="3000" product_id="2" description="things"]

= Do I need a Stripe account to use Press Pay? =

Yes. You must sign up for an account with Stripe as they will be processing credit card transactions for you. Press
Pay is created on top of Stripes API. You should read and understand Stripe's terms of service because you will be
working with them to manage refunds and disputes.

= How much does it cost? = 

PressPay is free to download and test. If you've configured PressPay with
Stripe Connect, you can also take live credit card charges with no up-front
cost. PressPay will then collect $0.50 on each 1 time transaction. So, we
don't make money until you make money. If you have
a need for volume pricing, contact presspay@atomicbroadcast.net and let us
know what we can do for you.

== Screenshots ==

1. Place a payment button on any page or post.
2. Lightbox for customers to enter their credit card information.
3. Confirmation email that is sent to you every time you make a sale.
4. Receipt that is sent to the customer when they purchase a product.

== Changelog ==

= 2.3 =
Style Fix.
= 2.2 =
Add Description to Stripe Transactions.
= 2.1 =
Allow multiple buttons on a page. You must provide a product id for multiple buttons.
= 2.0 =
Easier configuration with Stripe Connect.
= 1.7 =
RESTful Endpoints.
= 1.6 =
Upgraded to use Stripe Checkout Version 3.
= 1.5 =
Maintenance release.
= 1.4 =
Added "headline" and "description" options to presspay shortcode. This allows you to set the
text that appears in the stripe checkout lightbox.
= 1.3 =
Removed dependency on curl to make PressPay even easier to use.
= 1.2 =
Fix bugs with screenshots.
= 1.1 =
Updated documentation.
= 1.0 =
* Initial release of Press Pay.
