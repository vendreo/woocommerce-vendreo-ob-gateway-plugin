=== Vendreo Open Banking Gateway ===
Contributors: vendreo
Tags: wordpress, woocommerce, visa, payment-gateway, payment-processing, mastercard, openbanking
Requires at least: 6.1
Tested up to: 6.4
Stable tag: 1.2.1
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Vendreo's latest payment solution. Accept online payments in your WooCommerce store via the Open Banking API, safe and secure.

== Description ==

Vendreo are disrupting the payment processing industry, with a suite of class-leading solutions.

With decades of payments experience, the Vendreo team combine their expertise and points their focus exactly where it needs to be for the online world to benefit.

Vendreo's latest payment solution. Accept online payments in your WooCommerce store via the Open Banking API, safe and secure.

In order to process payments you will also need a Vendreo account. To get started please visit Vendreo - https://vendreo.com.

== Frequently Asked Questions ==

= How can I get setup =

For instructions, setup and configuration information please refer to the WooCommerce Integration Guide in your Vendreo Admin area https://app.vendreo.com/developer/woocommerce-integration.

= Why are my orders not being marked as paid? =

Ensure that the callback endpoint is working by visiting `https://your-site.com/wc-api/card_callback` in your browser.
You should see `-1` shown with a 200 response code.

If not, this can be caused by permalinks automatically adding a slash to the end of the url.

Try resolving this by:

1. In the WordPress admin visit `Settings / Permalinks`.
2. Select `Day and name` under `Permalink structure` being sure to hit save.

== Screenshots ==

1. The Vendreo Open Banking Gateway setting page.

== Changelog ==

= 1.2.1 =
* Renamed files to remove the word WooCommerce.
* License changed to GPLv3.
* ReadMe file changes.

= 1.2.0 =
* Added WooCommerce block support.
* AddedFile structure to include folders for improved readability.
* Updated Notes & Installation details in ReadMe.
* Renamed file vendreo-gateway.php to includes/php/vendreo-open-banking-gateway.php.

= 1.1.1 =
* Added 200 response code check.

= 1.1.0 =
* Added Doc blocks to vendreo-gateway.php to help improve code readability.
* Added Class variables to make code more explicit within vendreo-gateway.php file.
* Added Changed vendreo-gateway.php file by adding basket_items key to POST data (using data from the new get_basket_details() method).
* Changed README updated to contain useful project information such as dependency versions, instructional and the Changelog.
* Updated clearing of basket to be applied only upon successful checkout in vendreo-gateway.php file.
* Converted array() calls to[] in vendreo-gateway.php file.
* Altered Curl request in vendreo-gateway.php file to match new API endpoint requirements.
* Removed unnecessary comments and spacing in the vendreo-gateway.php file.

= 1.0.2 =
* Changed relocated vendreo-gateway.php to root of project.
* Removed vendreo folder from root of project as it was no longer required.

= 1.0.1 =
* Appended project title to the README.md file.
* Removed vendreo.zip file, as no longer required.

= 1.0.0 =
* Added vendreo.zip file containing compressed version of the entire project.
* Added vendreo folder to hold the main files.
* Added vendreo-gateway.php main plugin script.

== Upgrade Notice ==
