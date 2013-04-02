=== WP HTML Sitemap ===
Contributors: wmsedgar
Donate link: http://oaktondata.com/wordpress-html-sitemap/
Tags: html sitemap, seo, dynamic sitemap, sitemap
Requires at least: 2.9
Tested up to: 3.3.1
Stable tag: 1.2

Add a WordPress HTML sitemap that is fully customizable to improve your website SEO and enable easy navigation for your users.

== Description ==

WP HTML Sitemap generates an HTML sitemap that is readable/crawlable by search engines and is always up to date. Search engines like sitemaps, and so do your users. Take advantage of this easy-to-use plugin to improve your website SEO and ease of navigation. Publish your sitemap as a page or post. WP HTML Sitemap automatically detects available page templates and post formats supported by your theme. Modify your sitemap title. Use a custom slug. Choose whether to display available sections, and in what order. No need to remember category IDs. Sections available include: Authors, Categories, Forums (bbPress plugin), Pages, Posts, Products (WP e-Commerce plugin), and Topics (bbPress plugin).

<strong>Key Features:</strong>
<ul>
	<li>Include or exclude specific Sections</li>
	<li>Customize Section order on Sitemap</li>
	<li>Automatic integration with WP e-Commerce plugin</li>
	<li>Automatic integration with bbPress plugin</li>
	<li>Publish Sitemap as Page or Post using theme supported page templates and post formats</li>
	<li>Sitemap dynamically generated, works with default settings, easy to customize</li>
	<li>CSS tags set up for easy style customization</li>
</ul>

= Plugin's Official Site =

WP HTML Sitemap Website ([http://oaktondata.com/wordpress-html-sitemap](http://oaktondata.com/wordpress-html-sitemap))

= Plugin Support Forum =

WP HTML Sitemap Web Forum ([http://oaktondata.com/forum/wordpress-plugins/wp-html-sitemap/](http://oaktondata.com/forum/wordpress-plugins/wp-html-sitemap/))

== Installation ==

<ol>
	<li>Download the WP HTML Sitemap archive, then extract the wp-html-sitemap directory to your WordPress plugins directory (generally ../wordpress/wp-content/plugins/).</li>
	<li>Go to the <em>Plugins</em> page of your WordPress admin console and activate the WP HTML Sitemap plugin.</li>
	<li>Once activated, go to the WP HTML Sitemap <em>General</em> tab, via the WordPress <em>Settings</em> menu.</li>
	<li>On the WP HTML Sitemap <em>General</em> tab you can customize your sitemap attributes, or just go with the defaults.</li>
	<li>Click on <em>Save Settings</em> and either <em>Create Sitemap</em> or <em>Update Sitemap</em> (if sitemap has already been created) whenever you change any of the settings on the <em>General</em> tab.
	<li>Customize the appearance and order of appearance for sections on the <em>Sections</em> tab, and click on <em>Save Settings</em>.</li>
	<li>If using WP HTML Sitemap Pro, use the remaining tabs (Categories, Forums, Pages, Posts, and Topics) to further customize display options as desired.</li>
	<li>Consult the WP HTML Sitemap Pro documentation for instruction on use of shortcodes.</li>
	<li>Go to your sitemap url to view.</li>
	<li>Should you wish to remove your sitemap, just click on <em>Delete Sitemap</em>.</li>
</ol>
<p><strong>A Few Notes:</strong></p>
<p><strong>Modifying Your Sitemap:</strong> Do not edit your sitemap page or post directly (except as noted below), if you need to alter the title or other attributes of your sitemap, do it from within the settings page on WP HTML Sitemap.</p>
<p><strong>Tags, Categories, &amp; Author:</strong> The following sitemap page/post attributes can be modified directly: author, categories, and tags. Should you need to modify any of these settings for your sitemap, do so via the standard WordPress editing screens. By default, WP HTML Sitemap does not assign any tags or categories to the sitemap.</p>
<p><strong>Title or Slug Conflicts: </strong>If another page or post exists in your site with the same title you choose, it will not be overwritten. A new page or post will be created for your sitemap, with a modified slug as necessary to avoid conflicts.</p>
<p><strong>Temporarily Disabling:</strong> If you just want to disable your sitemap temporarily, you can also mark it as draft within your pages/posts listing, and then re-publish when desired.</p>

== Frequently Asked Questions ==

= Does WP HTML Sitemap allow me to customize the page format/display? =

Yes, WP HTML Sitemap is set up with CSS classes and id tags to allow this to be done easily. You can do this by editing the sitemap.css file in the ../wordpress/wp-content/plugins/wp-html-sitemap/css directory to customize the display of each section to your taste.

= Do I need to Update my Sitemap if I make a change on the Sections tab? =

No, just make sure to click on <em>Save Settings</em> on the Sections tab and any changes on the <em>Sections</em> tab will automatically be shown in your sitemap.

== Screenshots ==

1. General sitemap options.
2. Section display options.
3. Example sitemap publisehd as a post.
4. Example sitemap published as a page.
5. Example sitemap published as a post.

== Changelog ==

= 1.2 =
* Fixed bug with loading of page templates and post formats on General Options tab in MS Internet Explorer.
* Improved error handling for plugin load and activation process.
* Removed unused/unnecessary functions from Utility class.

= 1.1.2 =
* Fixed bug preventing options screen from displaying resulting from incorrect internal function call.

= 1.1.1 =
* Removed dependency on updated strstr function in PHP v5.3 which caused the plugin to crash for anyone running an earlier version of PHP.

= 1.1 =
* Updated default options to avoid conflicts with XML sitemaps in WordPress document root.
* Added more robust error handling for loading of WP functions required for load of sitemap.
* Fixed bug with automatic sitemap generation for non-administrative users.

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.2 =
* Improved error handling, fixed bug with options display in MS Internet Explorer, and other minor bug fixes.

= 1.1.2 =
* IMPORTANT: Fixed bug preventing options screen from displaying resulting from incorrect internal function call.

= 1.1.1 = 
* Bug fix for anyone running PHP v5.2 or earlier, the plugin should now function properly (rather than crash).

= 1.1 =
* Updated default options to avoid conflicts with XML sitemaps, minor bug fixes, and enhanced error handling.

= 1.0 =
* Initial release
