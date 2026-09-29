=== WP Top Bar ===
Contributors: saeedcodez
Tags: top bar, announcement bar, notification bar, sticky bar, banner
Requires at least: 5.2
Tested up to: 6.6
Requires PHP: 7.2
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, fast top announcement bar with a custom message, button, image, sticky mode and a scheduling window.

== Description ==

WP Top Bar adds a clean, lightweight notification bar to the top of your site. Use it for announcements, promotions, cookie notices, or time-limited offers.

**Features**

* Two bar types: a content bar (text + button + small logo) or a full-image bar (a single image fills the entire bar)
* Custom text with basic inline HTML (links, bold, italic)
* Optional call-to-action button with adjustable colors
* Optional image (logo/icon) with an optional link
* Full-image mode: pick any image to fill the whole bar, with an optional link and alt text
* Adjustable background, text and button colors
* Adjustable bar height
* Sticky mode, so the bar stays visible while visitors scroll
* Dismissible close button — the visitor's choice is remembered in their browser (session, days, or permanently)
* Excluded pages — hide the bar on any pages you choose
* Display time range — schedule the bar to only appear between a start and end date/time
* Scoped styles — the bar's layout and colors hold up against typical theme and plugin CSS
* Uses your theme's body font automatically
* Minimal, modern admin screen with a live preview
* Built-in English and Persian (فارسی) translations
* No database bloat: a single option, no extra database tables

= Privacy =

The close button stores a small flag in the visitor's browser `localStorage` or `sessionStorage` (never a server-side cookie, and no data is sent to any server) so a closed bar doesn't come back until it should.

== Installation ==

1. Upload the `wp-topbar` folder to `/wp-content/plugins/`, or install the zip via **Plugins → Add New → Upload Plugin**.
2. Activate the plugin through the **Plugins** screen.
3. Go to **Top Bar** in the admin menu to configure and enable it.

== Frequently Asked Questions ==

= The bar doesn't show up on the frontend =

Make sure the bar is toggled **on** under **Top Bar → Bar type**, and that your theme calls `wp_body_open()` in its `header.php` (all modern WordPress themes since 2019 do).

= How does the full-image bar mode work? =

Switch **Bar type** to **Full image bar**, then choose an image. It's cropped to the configured bar height and stretched to the full width of the page. You can optionally link it to a URL and set alt text for accessibility.

= Can I use HTML in the message? =

Yes — `<a>`, `<strong>`, `<em>`, `<br>` and `<span>` are allowed.

= Will the bar's styling clash with my theme? =

No. All of the plugin's CSS is scoped to the bar's `#wptb-bar` ID, so it doesn't clash with typical theme and plugin styles, and you can still customize it with your own CSS. It intentionally inherits your theme's body font.

== Changelog ==

= Unreleased =
* Added an "Excluded pages" setting: choose the pages on which the bar should not be displayed.
* Compatibility with themes that pin their header with `position: fixed` or `sticky`: such headers are now pushed below the visible part of the bar instead of overlapping it. The offset is also exposed as the `--wptb-offset` CSS variable.

= 1.1.0 =
* Added a full-image bar mode: display a single image across the entire bar, with an optional link and alt text.

= 1.0.0 =
* Initial release.
