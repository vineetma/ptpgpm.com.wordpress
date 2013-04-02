<?php

/*

 	Plugin Name: WP HTML Sitemap
	Plugin URI: http://oaktondata.com/wordpress-html-sitemap/
	Version: 1.2
	Author: Bill Edgar
	Author URI: http://oaktondata.com
	Text Domain: wp-html-sitemap
	Description: WP HTML Sitemap adds a dynamic HTML sitemap page to your site that is always up-to-date. Visitors can use your sitemap to more easily navigate your site, and it is search engine crawlable. Options allow inclusion or exclusion of sections for posts, pages, categories, tags, authors, and more. Also includes the ability to customize the order of section listing on your sitemap.
	Tags: HTML sitemap, sitemap, custom sitemap, dynamic sitemap
	Package: wp-html-sitemap
	License: BSD New (3-Clause License)
	
	Copyright (c) 2012, Oakton Data LLC
	All rights reserved.

	Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
	    
	    * Redistributions of source code must retain the above copyright
	      notice, this list of conditions and the following disclaimer.
	    * Redistributions in binary form must reproduce the above copyright
	      notice, this list of conditions and the following disclaimer in the
	      documentation and/or other materials provided with the distribution.
	    * Neither the name of the Oakton Data LLC nor the
	      names of its contributors may be used to endorse or promote products
	      derived from this software without specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL Oakton Data LLC BE LIABLE FOR ANY
	DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	
	This is the main PHP class file for the wp-html-sitemap plugin.
	
*/

// define constants
define( 'WP_HTML_SITEMAP_VERSION', '1.2' );
define( 'WP_HTML_SITEMAP_BASE_URL', network_site_url() );

// directory locations
define( 'WP_HTML_SITEMAP_PLUGIN_DIR', plugin_dir_url(__FILE__) );
define( 'WP_HTML_SITEMAP_CSS', WP_HTML_SITEMAP_PLUGIN_DIR . 'css/' );
define( 'WP_HTML_SITEMAP_IMAGES', WP_HTML_SITEMAP_PLUGIN_DIR . 'images/' );
define( 'WP_HTML_SITEMAP_INCLUDES', dirname(__FILE__) . '/' . 'inc/' );
define( 'WP_HTML_SITEMAP_ADMIN_INCLUDES', strstr( dirname(__FILE__), 'wp-content' ) . '/wp-admin/includes/' );
define( 'WP_HTML_SITEMAP_JS', WP_HTML_SITEMAP_PLUGIN_DIR . 'js/' );
define( 'WP_HTML_SITEMAP_HTML', WP_HTML_SITEMAP_PLUGIN_DIR . 'html/' );
define( 'WP_HTML_SITEMAP_THEMES', WP_HTML_SITEMAP_PLUGIN_DIR . 'themes/' );

// php class files
define( 'WP_HTML_SITEMAP_ADMINPAGE_CLASS', 'AdminPage' );
define( 'WP_HTML_SITEMAP_EXCEPTION_CLASS', 'Exception' );
define( 'WP_HTML_SITEMAP_MAP_CLASS', 'Map' );
define( 'WP_HTML_SITEMAP_OPTIONS_CLASS', 'Options' );
define( 'WP_HTML_SITEMAP_SHORTCODE_CLASS', 'SitemapShortcode' );
define( 'WP_HTML_SITEMAP_UNINSTALL', WP_HTML_SITEMAP_PLUGIN_DIR . 'uninstall.php' );
define( 'WP_HTML_SITEMAP_UTILITIES_CLASS', 'Utilities' );
define( 'WP_HTML_SITEMAP_THEME_CLASS', WP_HTML_SITEMAP_ADMIN_INCLUDES . 'theme.php' );

// definition and configuration files
define( 'WP_HTML_SITEMAP_ADD_OPTIONS', WP_HTML_SITEMAP_INCLUDES . 'options.cfg' );
define( 'WP_HTML_SITEMAP_REGISTER_SCRIPTS', WP_HTML_SITEMAP_INCLUDES . 'scripts.cfg' );
define( 'WP_HTML_SITEMAP_REGISTER_STYLES', WP_HTML_SITEMAP_INCLUDES . 'styles.cfg' );
define( 'WP_HTML_SITEMAP_PLUGIN_STYLE', 'style.css' );
define( 'WP_HTML_SITEMAP_MAP_STYLE', 'sitemap.css' );

// html files
define( 'WP_HTML_SITEMAP_ABOUT_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'about.htm' );
define( 'WP_HTML_SITEMAP_AUTHOR_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'author_settings.htm' );
define( 'WP_HTML_SITEMAP_CATEGORY_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'category_settings.htm' );
define( 'WP_HTML_SITEMAP_FORUM_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'forum_settings.htm' );
define( 'WP_HTML_SITEMAP_GENERAL_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'general_settings.htm' );
define( 'WP_HTML_SITEMAP_GENERAL_SETTINGS_END_HTML', WP_HTML_SITEMAP_HTML . 'general_settings_end.htm' );
define( 'WP_HTML_SITEMAP_SECTION_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'section_settings.htm' );
define( 'WP_HTML_SITEMAP_PAGE_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'page_settings.htm' );
define( 'WP_HTML_SITEMAP_POST_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'post_settings.htm' );
define( 'WP_HTML_SITEMAP_PRODUCT_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'product_settings.htm' );
define( 'WP_HTML_SITEMAP_TOPIC_SETTINGS_HTML', WP_HTML_SITEMAP_HTML . 'topic_settings.htm' );

// generic include method
function wp_html_sitemap__autoinclude( $class_name ) {
	try {
		if ( is_file( WP_HTML_SITEMAP_INCLUDES . $class_name . '.php' ) ) {
			include_once( WP_HTML_SITEMAP_INCLUDES . $class_name . '.php' );
		} else {
			throw new Exception( WP_HTML_SITEMAP_INCLUDES . $class_name . '.php does not exist' );
		}
	} catch ( Exception $e ) {
		echo "<p>" . $e->getMessage() . "</p>";
		die( "<p>Unable to include $class_name. WP HTML Sitemap exiting.</p>" );
	}
}

// generic getter method
function wp_html_sitemap__get($property,$obj) { // generic getter
	if (array_key_exists($property, get_class_vars(get_class($obj)))) {
		return $obj->$property;
	} else {
		die("<p>$property does not exist in " . get_class($obj) . "</p>");
	}
}

// generic setter method
function wp_html_sitemap__set($property,$value,$obj) { // generic setter
	if (array_key_exists($property, get_class_vars(get_class($obj)))) {
		$obj->$property = $value;
	} else {
		die("<p>$property does not exist in " . get_class($obj) . "</p>");
	}
}

// generic toString method
function wp_html_sitemap__toString($obj) { // generic object toString
	$attributes = get_class_vars(get_class($obj));
	$str = 'Class = ' . get_class($obj) . '\n';
	foreach ($attributes as $name => $value) {
		$str .= $name . ' = ' . $value . '\n';
	}
	return $str;
}

if ( !class_exists( 'wp_html_sitemap' ) ) {

	// wp_html_sitemap class
	class wp_html_sitemap {
		
		// public constructor method
		function wp_html_sitemap() { 
			$this->__construct();
		}
		
		// hidden constructor method
		function __construct() {
			
			wp_html_sitemap__autoinclude( WP_HTML_SITEMAP_EXCEPTION_CLASS );
			wp_html_sitemap__autoinclude( WP_HTML_SITEMAP_UTILITIES_CLASS );
			wp_html_sitemap__autoinclude( WP_HTML_SITEMAP_MAP_CLASS );
			wp_html_sitemap__autoinclude( WP_HTML_SITEMAP_SHORTCODE_CLASS );
			
			try {
				$this->include_wp_functions();
				$this->addShortcodes();
			} catch ( wp_html_sitemap_Exception $e ) {
				echo $e->getError();
				die( "<p>WP HTML Sitemap exiting.</p>" );
			}
			
			// print sitemap stylesheet
			add_action(
				$tag = 'wp_head',
				$callback = array( &$this, 'addSitemapStyle' ),
				$priority = 1
				);
			
			// verify we're on admin pages
			if ( is_admin() ) {

				// includes
				wp_html_sitemap__autoinclude( WP_HTML_SITEMAP_OPTIONS_CLASS );
				wp_html_sitemap__autoinclude( WP_HTML_SITEMAP_ADMINPAGE_CLASS );

				// runs when plugin is activated
				register_activation_hook( __FILE__, array( &$this, 'activate' ) );
				// runs when plugin is deactivated
				register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
				
				// instantiate wp_html_sitemap_options class
				if ( class_exists( 'wp_html_sitemap_options' ) ) {
					new wp_html_sitemap_options( $this );
				}
				
				// actions
				// add plugin setup function link on init
				add_action(
					$tag = 'init',
					$callback = array( &$this, 'setup' ),
					$priority = 1
					);
				
				// filters
				// add filter to output settings link with plugin list
				add_filter(
					$tag = 'plugin_action_links',
					$function_to_add = array( &$this, 'plugin_actions_links' ),
					$priority = 10,
					$accepted_args = 2
					);
				
			}
		
		}
		
		// activate method
		function activate() {
			// set up wp_html_sitemap
			$this->setup();
		}
		
		function addShortcodes() {
			if ( class_exists( 'wp_html_sitemap_shortcode' ) ) {
				add_shortcode( 'wp_html_sitemap', 'wp_html_sitemap_shortcode::start' );
			} else {
				throw new wp_html_sitemap_Exception( 'wp_html_sitemap_shortcode class not loaded.' );
			}
		}
		
		// add mostpopular widget style
		function addSitemapStyle() {
			$style = WP_HTML_SITEMAP_CSS . WP_HTML_SITEMAP_MAP_STYLE;
			wp_register_style(
				$handle = 'wp-html-sitemap-style',
				$src = $style
				);
			wp_enqueue_style(
				$handle = 'wp-html-sitemap-style'
				);
		}
		
		// deactivate method
		function deactivate() {
			// unregister wp_html_sitemap settings from wordpress database
			/*
			 * @TODO must be updated to include additonal option groups for WP HTML Sitemap Pro
			 */
			$settings = array( 'wp-html-sitemap-general', 'wp-html-sitemap-sections'  );
			foreach ( $settings as $key => $value ) {
				// unregister settings
				unregister_setting(
					$option_group = $value,
					$option_name = $value,
					$sanitize_callback = ''
					);
			}
		}
		
		// method to upgrade options
		function do_option_upgrade( $version = '1.0', $option_group = 'wp-html-sitemap-general', $options ) {
			/*
			 * @TODO must be updated to include additonal option groups for WP HTML Sitemap Pro
			*/
			if ( $version == '1.0' ) {
				return $options;
			}
		} // end method do_option_upgrade
		
		function include_wp_functions() {
			// check include path for wp-content			
			if ( preg_match( "/wp-content/", WP_HTML_SITEMAP_INCLUDES ) ) {
				// set admin include path
				$paths = preg_split( "/wp-content/", WP_HTML_SITEMAP_INCLUDES );
				$wp_admin_includes = $paths[0] . 'wp-admin/includes/';
			// otherwise path is not recognized, throw error
			} else {
				throw new wp_html_sitemap_Exception( 'wp-content path not found in ' . WP_HTML_SITEMAP_INCLUDES );
			}
			// check for theme.php file existence
			if ( is_file( $wp_admin_includes . 'theme.php' ) ) {
				// check for get_page_templates function existence
				if ( !function_exists( 'get_page_templates' ) ) {
					// include get_page_templates function if necessary
					if ( !include_once( $wp_admin_includes . 'theme.php' ) ) { // for WP get_page_templates function
						throw new wp_html_sitemap_Exception( 'get_page_templates function not defined.</p>' );
					}
				}
			// otherwise path is incorrect, throw error
			} else {
				throw new wp_html_sitemap_Exception( $wp_admin_includes . 'theme.php file not found.' );
			}
			// check for plugin.php file existence
			if ( is_file( $wp_admin_includes . 'plugin.php' ) ) {
				// check for get_plugins function existence
				if ( !function_exists( 'get_plugins' ) ) {
					// load get_plugins function if necessary
					if ( !include_once( $wp_admin_includes . 'plugin.php' ) ) { // for WP get_plugins function
						throw new wp_html_sitemap_Exception( 'get_plugins function not defined</p>' );
					}
				} 
			// otherwise path is incorrect, throw error
			} else {
				throw new wp_html_sitemap_Exception( $wp_admin_includes . 'plugin.php file not found.' );
			}
		}
		
		// method to check options to upgrade plugin options, if necessary
		function upgrade_options() {
			/*
			 * @TODO must be updated to include additonal option groups for WP HTML Sitemap Pro
			*/
			$options_groups = array( 'wp-html-sitemap-general', 'wp-html-sitemap-sections' );
			foreach ( $options_groups as $key => $group ) {
				// retrieve options array from wordpress database
				$options = get_option(
					$show = $group,
					$default = false
					);
				// check to see if option array exists
				if ( $options !== false ) {
					$this->do_option_upgrade( '1.0', $group, $options );
					// check and upgrade options if necessary
				}
				// update new options in wordpress database
				update_option(
					$option_name = $group,
					$newvalue = $options
					);
			}
		} // end method upgrade_options
		
		// method to output plugin action links on main plugins page
		function plugin_actions_links( $links, $file ) {
			static $this_plugin;
 			// set $this_plugin to current file name
		    if ( empty( $this_plugin ) ) {
		        $this_plugin = plugin_basename( __FILE__ );
		    }
		 
		    // check to make sure we are on the correct plugin
		    if ( $file == $this_plugin ) {
		        // the anchor tag and href to the URL. For a "Settings" link, this needs to be the url of your settings page
		        $settings_link = '<a href="' . 
		        	get_bloginfo( 'wpurl' ) . 
		        	'/wp-admin/options-general.php?page=wp-html-sitemap">Settings</a>';
		        // add the link to the list
		        array_unshift( $links, $settings_link );
		    }
		 	// return modified array of links
		    return $links;
		}
		
		// setup method
		function setup() {
			// upgrade options if necessary
			$this->upgrade_options();
		}
		
		// uninstall method
		function uninstall() {
			if ( !defined( 'ABSPATH' ) || !defined( 'WP_HTML_SITEMAP_UNINSTALL' ) ) {
				exit();
			}
			/*
			 * @TODO must be updated to include additonal option groups for WP HTML Sitemap Pro
			*/
			// remove wp_html_sitemap settings from wordpress database
			$settings = array( 'wp-html-sitemap-general', 'wp-html-sitemap-sections' );
			foreach ( $settings as $key => $value ) {
				// remove options from WordPress database
				delete_option( $value );
			}
		}
		
		// validate method
		function validate( $rawinput ) {
			return $rawinput;
		}
		
	}
	
} // end class wp_html_sitemap

// instantiate new wp_html_sitemap object
if ( class_exists( 'wp_html_sitemap' ) ) {
	new wp_html_sitemap();
}
?>