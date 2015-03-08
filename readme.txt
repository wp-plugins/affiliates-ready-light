=== Affiliates Ready! Ecommerce Integration Light ===
Contributors: itthinx
Donate link: http://www.itthinx.com/plugins/affiliates-ready-light
Tags: ads, advertising, affiliate, affiliate marketing, affiliate plugin, affiliate tool, affiliates, bucks, contact form, crm, earn money, e-commerce, e-commerce, integration, lead, link, marketing, money, online sale, order, partner, referral, referral links, referrer, shopping cart, sales, shop, shopping cart, site, track, transaction, ready, ready ecommerce, wordpress
Requires at least: 3.5.1
Tested up to: 4.1.1
Stable tag: 1.0.3
License: GPLv3

This plugin integrates Affiliates with Ready! Ecommerce Shopping Cart.

== Description ==

_Please note_ that we provide this latest update as a courtesy for existing users. Support for this integration is going to be __dropped__ and we recommend to use any of the other supported e-commerce systems.

This plugin integrates [Affiliates](http://www.itthinx.com/plugins/affiliates/) with [Ready! Ecommerce](http://readyshoppingcart.com).

With this integration plugin, referrals are created automatically for your affiliates when sales are made.

The plugin allows you to set a referral (commission) rate so that your affiliates get credited with a referral based on a percentage of each sale's total net amount.

Please note that this integration does not support automatic synchronization between the order status and referrals in any of the Affiliates plugins.

Requirements:

- [Ready! Ecommerce](http://http://readyshoppingcart.com)
- [Affiliates](http://wordpress.org/extend/plugins/affiliates)
- [Affiliates Ready! Ecommerce Integration Light](http://www.itthinx.com/plugins/affiliates-ready-light) (this plugin)

Install these, set up your shop, decide how much you want to pay your affiliates and start selling!

__Feedback__ is welcome!
If you need help, have problems, want to leave feedback or want to provide constructive criticism, you can leave a comment here at the [plugin page](http://www.itthinx.com/plugins/affiliates-ready-light).

Please try to solve problems there before you rate this plugin or say it doesn't work. There goes a _lot_ of work into providing you with free quality plugins! Please appreciate that and help with your feedback. Thanks!

You are welcome to [follow @itthinx on Twitter](http://twitter.com/itthinx) for updates on this and related plugins.

== Installation ==

1. Install and activate the [Ready! Ecommerce](http://http://readyshoppingcart.com) plugin. Configure your shop and create products.
2. Install and activate the [Affiliates](http://wordpress.org/extend/plugins/affiliates) plugin. Use the default settings or configure it to your needs.
3. Install and activate the [Affiliates Ready! Ecommerce Integration Light](http://www.itthinx.com/plugins/affiliates-ready-light) plugin.
4. A new *Ready! Light* menu item will appear under the *Affiliates* menu in WordPress. Set the referral rate for your affiliates there.

Note that you can install the plugins from your WordPress installation directly: use the *Add new* option found in the *Plugins* menu.
You can also upload and extract them in your site's `/wp-content/plugins/` directory or use the *Upload* option.

== Frequently Asked Questions ==

= What features does this integration provide? =

When a sale is made through Ready! Ecommerce, a referral is recorded for the affiliate that referred the sale.

= How can I set the amount that affiliates earn on each sale? =

Go to *Affiliates > Ready! Light* and set the rate there.

Example: If you want to give an affiliate 10% of each net total sales amount, set the rate to *0.1*.

== Screenshots ==

See also: [Affiliates Ready! Light](http://www.itthinx.com/plugins/affiliates-ready-light/)

1. Referral rate setting - Here, a referral rate set at 0.25 credits affiliates with 25% commissions on sales.
2. Referrals created automatically for sales - Each referral is linked to its order.

== Changelog ==

= 1.0.3 =
* Fixed: Referrals not created with PayPal checkout. Added a dispatcher hook on orderPost to cover cases where onSuccessOrder does not work to create a referral.

= 1.0.2 =
* WordPress 3.8 compatible

= 1.0.1 =
* WordPress 3.6 compatible

= 1.0.0 =
* Initial release, tested on WordPress 3.6-beta3, Ready! 0.3.2.9 and Affiliates 2.2.0.

== Upgrade Notice ==

= 1.0.3 =
* Fixes referrals not created with PayPal checkout. Tested with Ready 0.6.1, WordPress 4.1.1 and Affiliates 2.8.0.

= 1.0.2 =
* WordPress 3.8 compatible

= 1.0.1 =
* WordPress 3.6 compatible

= 1.0.0 =
* Initial release.
