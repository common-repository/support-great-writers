=== Amazon Book Store ===
Contributors: heypublisher, aguywithanidea, loudlever
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y8SL68GN5J2PL
Tags: affiliate, amazon, product, book store, affiliate sales,ASIN, Amazon Associate, monetize, heypublisher
Requires at least: 5.0
Tested up to: 5.5.0
Stable tag: 3.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sell Amazon products in sidebar widgets, unique to the individual POST or generically from a default pool of products that you define.

== Description ==

The Amazon Book Store widget gives you a simple way to sell Amazon products in the sidebar of your blog, magazine, or other WordPress-powered website.  The widget can be included multiple times, with different products being displayed in each instance.

You can sell specific products on specific pages of your website, or define a 'pool' of products from which the widget will randomly chose.  This allows you to quickly build up an Amazon store of products that are related to your individual POSTs.

== Installation ==

**Install the Widget**

Install the plugin, then activate it.  Once activated, configure the Widget:

* First, drag the 'Amazon Book Store' widget to your sidebar.
* Where prompted, set the "Title" you want displayed above the widget
* Select how many product images you want displayed in the widget, 1 or 2.

**Configure the ASIN pool**

Navigate to 'Settings' -> 'Amazon Book Store'.  Input your Amazon Affiliate ID and country, the add the ASINs for the products you want to be displayed in the widget.  See [How to Find Amazon ASINs](https://www.amazon.com/gp/seller/asin-upc-isbn-info.html) for more information.  There are two categorizations of settings:

* POST Specific:  If set, these products will be displayed when users are reading the designated POST.
* Default: If the request is to a POST that does not have specific ASINs defined, then the widget will display products from this group.

== Frequently Asked Questions ==

= Can I Use This Widget More Than Once In My Sidebar? =
Yes.  Absolutely.  The widget will keep track of which products have already been displayed on the page and will ensure that duplicates are not shown.

= Can I Make a Donation to HeyPublisher for Use of this Plugin? =
If you wish to make a donation, please consider donating instead to the [Literary Arts](https://www.literary-arts.org/contribute/) organization.  Literary Arts introduces high school students to the craft of writing.  They are our kind of group!

== Screenshots ==

1. Widget displayed in the Widget manager.
2. Widget displayed in the Side-Bar (your styling may differ)
3. Widget process flow, illustrating how the products are selected for display
4. ASIN configuration for POSTs and for Default pool

== Changelog ==

= 3.1.1 =
* Released: 2020-09-05
* [[#21](https://github.com/HeyPublisher/amazon-book-store/issues/21)] : Fixed bug when resetting default ASINs by clearing the field and saving.
* Updated the README to better explain how to install directly from GitHub.

= 3.1.0 =
* Released: 2020-07-25
* [[#18](https://github.com/HeyPublisher/amazon-book-store/issues/18)] : Amazon Brazil is now supported
* [[#16](https://github.com/HeyPublisher/amazon-book-store/issues/16)] : Removed unneeded constant
* [[#14](https://github.com/HeyPublisher/amazon-book-store/issues/14)] : ASIN information, when not cached locally, is not fetched from HeyPublisher.com, which wraps the Amazon PAAPI, eliminating the need for users of this plugin to have their own API key and secret.
* Fixed additional minor code inconsistencies.

= 3.0.0 =
* Released: 2020-07-03
* Plugin is now hosted on [GitHub](https://github.com/HeyPublisher/amazon-book-store)
* Fixed issue with call to function `split` not being found in PHP versions 7.0 and above.
* Added an updater that will check GitHub for latest version and update locally.
* Organized code around HeyPublisher/Base class
* Validated plugin works up through WordPress 5.3

= 2.2.1 =
* Released: 2017-05-27
* Fixed an issue where affiliate code would not stick if changing from any international affiliate country back to US.

= 2.2.0 =
* Released: 2017-04-05
* This plugin is now maintained by [HeyPublisher](https://www.heypublisher.com)
* Cleaned out a bunch of the kruft
* Standardized the plugin layout using HeyPublisher plugin framework
* Validated this plugin works up to WordPress 4.7.3
* Added Italy and Spain as supported Affiliate countries

= 2.1.3 =
* Fixed error with returning empty array in older versions of PHP.

= 2.1.2 =
* Fixed error with array initialization occurring with older versions of PHP.

= 2.1.1 =
* Fix upgrade error.

= 2.1.0 =
* Affiliate ID and Country are now managed on the Settings page - you will need to update these values after upgrading.
* Duplicate products are no longer displayed when you use the widget multiple times on a page.
* Widget no longer displays on page if there are insufficient unique ASINs for it's display.

= 2.0.0 =
* Redesigned plugin and improved admin layout.
* WordPress 4.1 compliant.

= 1.1.3 =
* Fixed a layout problem where books and media product images had different widths.
* Updated description of plugin.

= 1.1.2 =
* Updated JavaScript in admin tool to make use of jQuery.
* Fixed issue with ASIN assignment for existing POSTS in Admin screen.
* Plugin is now WP 4.x compatible.
* Release date (12/8/2014)

= 1.1.1 =
* Fixed problem with wrapping of form labels on the Settings page.
* Extra long POST titles are now truncated to 40 characters for display purposes.
* Removed the POST id from the display title.
* Added screenshot showing Settings configuration screen.

= 1.1.0 =
* Changed name of sidebar widget to 'Amazon Book Store'
* Added configuration management screen at 'Settings' -> 'Amazon Book Store'
* Widget can now be used with UK, DE, FR and CA affiliate programs.
* Widget can be configured to display 1 or 2 product images in widget.
* Added ability to create a 'pool' of ASINs for POSTs, as well as a 'default' pool to be used as a last resort.

= 1.0.1 =
* Updated screenshots to highlight the fact that users should change their Amazon Associate ID.
* Plugin is now owned and maintained by [Loudlever, Inc.](https://www.loudlever.com)
* Update Release (03/26/2010)

= 1.0 =
* Plugin Release (10/23/2009)
