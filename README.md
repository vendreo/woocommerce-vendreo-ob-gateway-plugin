<p align="center">   
    <img src="https://cdn.vendreo.com/images/vendreo-fullcolour.svg" width="270" height="auto">
</p>

# WooCommerce Vendreo OB Gateway Plugin
Tags: wordpress, woocommerce, visa, payment-gateway, payment-processing, mastercard, openbanking\
Requires at least WordPress: 6.1.1\
Tested on WordPress up to: 6.4.2\
Requires at least WooCommerce: 6.9\
Tested on WooCommerce up to: 8.4.0\
Tested on PHP: 7.4 & 8.0\
Stable tag: 1.2.1\
License: GPLv3\
License URI: https://www.gnu.org/licenses/gpl-3.0.html

[![License: MIT](https://img.shields.io/badge/license-GPLv3-blue)](https://opensource.org/licenses/GPLv3)
![PHP 7.2](https://img.shields.io/badge/PHP-7.4-blue.svg)
![Wordpress](https://img.shields.io/badge/wordpress-v6.1.1-green)
![woocommerce](https://img.shields.io/badge/woocommerce-v6.9-green)

### Description
Vendreo's latest payment solution. Accept online payments in your WooCommerce store via the Open Banking API, safe and secure.

### Requirements

To install the WooCommerce Vendreo OB Gateway Plugin, you need:

* WordPress Version 6.1.1 or newer (installed).
* WooCommerce Version 6.9 or newer (installed and activated).
* PHP Version 7.4 or newer.

In order to process payments you will also need a Vendreo account. To get started please visit here. [Vendreo](https://vendreo.com).

### Instructions, Setup and Configuration

For instructions, setup and configuration information please refer to the `WooCommerce Integration Guide` in your Vendreo
Admin area `https://app.vendreo.com/developer/woocommerce-integration`.


#### Notes:
**Orders not being marked as Processing?**\
Ensure that the callback endpoint is working by visiting `https://your-site.com/wc-api/ob_callback` in your browser.
You should see `-1` shown with a 200 response code.

If not, this can be caused by permalinks automatically adding a slash to the end of the url.
Try resolving this by:
1. In the WordPress admin visit `Settings / Permalinks`.
2. Select `Day and name` under `Permalink structure` being sure to hit save.
---

## Changelog
As documented here [Keep A Change Log](https://keepachangelog.com/en/1.0.0/).

### [1.2.1] - 10-01-2024

#### Changed
- License to GPLv3.
- ReadMe file changes.

### [1.2.0] - 04-01-2024

#### Added
- WooCommerce block support.
- File structure to include folders for improved readability.

#### Changed
- Updated Notes & Installation details in `ReadMe`.
- Renamed file `vendreo-gateway.php` to `includes/php/woocommerce-vendreo-ob-gateway.php`.
 
### [1.1.1] - 01-12-2022

#### Added
- 200 response code check.

### [1.1.0] - 01-12-2022

#### Added
- File `LICENSE.txt`.
- Doc blocks to `vendreo-gateway.php` to help improve code readability.
- Class variables to make code more explicit within `vendreo-gateway.php` file.
- Changed `vendreo-gateway.php` file by adding `basket_items` key to POST data (using data from the new `get_basket_details()` method).

#### Changed
- `README.md` updated to contain useful project information such as dependency versions, instructional and the Changelog.
- Updated clearing of basket to be applied only upon successful checkout in `vendreo-gateway.php` file.
- Converted `array()` calls to`[]` in `vendreo-gateway.php` file.
- Altered Curl request in `vendreo-gateway.php` file to match new API endpoint requirements.

#### Removed
- Unnecessary comments and spacing in the `vendreo-gateway.php` file.


### [1.0.2] - 15-06-2022

#### Changed
- relocated `vendreo-gateway.php` to root of project.

#### Removed
- `vendreo` folder from root of project as it was no longer required.


### [1.0.1] - 15-06-2022

#### Changed
- Appended project title to the `README.md` file.

#### Removed
- `vendreo.zip` file, as no longer required.


### [1.0.0] - 14-06-2022

#### Added
- `vendreo.zip` file containing compressed version of the entire project.
- `vendreo` folder to hold the main files.
- `vendreo-gateway.php` main plugin script.
