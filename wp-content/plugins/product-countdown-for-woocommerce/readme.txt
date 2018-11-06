=== Product Time Countdown for WooCommerce ===
Contributors: algoritmika, anbinder
Tags: woocommerce, product, countdown, time, product time countdown, product countdown, time countdown, time counter
Requires at least: 4.4
Tested up to: 4.9
Stable tag: 1.4.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add live time counter to any WooCommerce product.

== Description ==

**Product Time Countdown for WooCommerce plugin** lets you add live time counter to any WooCommerce product.

There is a number of automatic actions available after product's **countdown time ends**:

* product set to **non-purchasable**,
* product **hidden** (Pro version),
* product's **sale cancelled** (Pro version),
* product set to **sold out** (Pro version),
* **no changes** to the product state.

**General options** include:

* timer **template**,
* **time format** (and optional "Human readable format" option),
* timer **style**,
* timer **update rate**,
* option to enable/disable **page reload** on time end,
* option to set **message on time finished**.

You can choose the **timer position**:

* on **single product page**,
* on **archive (shop) pages** (Pro version),
* with **shortcode** (Pro version).

**Admin products list options**:

* add "Countdown" **column** to admin products list.

= Feedback =
* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Visit the [Product Time Countdown for WooCommerce plugin page](https://wpfactory.com/item/product-time-countdown-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Product Time Countdown".

== Changelog ==

= 1.4.0 - 14/10/2018 =
* Fix - "Disable product" Action - "Make completely invisible" option - Possible pagination issue fixed.
* Dev - Actions - "Cancel sale" action added.
* Dev - Actions - "Make sold out" action added.
* Dev - General Options - "Message on time finished" option added.
* Dev - General Options - "Time format" option added.
* Dev - General Options - "Reload page" option added.
* Dev - General Options - Template - Raw input is now allowed.
* Dev - General Options - Template - Shortcodes are now allowed.
* Dev - General Options - Update rate - Minimum value set to 100.
* Dev - `[alg_wc_ptc_translate]` shortcode added.
* Dev - Admin settings descriptions updated, restyled, `select` type display fixed (by adding `wc-enhanced-select` class).
* Dev - Code refactoring.
* Dev - Plugin URI updated.

= 1.3.0 - 11/10/2017 =
* Dev - "Disable product" Action Options - Settings section added.
* Dev - Admin Products List Options - "Add column" option added.
* Dev - Settings sections array stored as main class property.
* Dev - Admin Settings - Minor fixes, description updates, restyling.

= 1.2.0 - 22/07/2017 =
* Dev - WooCommerce v3 compatibility - Product ID.
* Dev - "Position on Archive Pages" options added.
* Dev - Option to disable counter on single product page added.
* Dev - `[product_time_counter]` shortcode added.
* Dev - "Update Rate" option added.
* Dev - "Human Readable Format" option added.
* Dev - More positions added to "Position on Single Product Page".
* Dev - Admin settings - Input types changed to `date` and `time` (instead of simple `text`).
* Dev - `location.reload()` removed from JS (on time ended).
* Dev - "Reset Section Settings" option added.
* Dev - Autoloading plugin options.
* Dev - Plugin renamed from "Product Countdown" to "Product Time Countdown".
* Dev - Link updated from http://coder.fm to https://wpcodefactory.com.
* Dev - Plugin header ("Text Domain" etc.) updated.

= 1.1.0 - 14/02/2017 =
* Fix - `load_plugin_textdomain` moved to constructor.
* Dev - Language (POT) file uploaded.
* Dev - Autoload set to `no` in `add_option`.
* Dev - "Position on Single Product Page" and "Position Priority" options added.
* Dev - Tags updated.

= 1.0.0 - 24/11/2016 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
