<?php

if ( !class_exists( 'wp_html_sitemap_uninstall' ) ) {

	// wp_html_sitemap_uninstall class
	class wp_html_sitemap_uninstall {
		
		// public constructor method
		function wp_html_sitemap_uninstall() { 
			$this->__construct();
		}
		
		// hidden constructor method
		function __construct() {
			// verify administrative rights
			if ( is_admin() ) {
				
				if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
					exit();
				}
				/*
				 * @TODO must be updated to include additonal option groups for WP Sitemap Pro
				*/
				// remove wp_html_sitemap settings from wordpress database
				$settings = array( 'wp-html-sitemap-general', 'wp-html-sitemap-sections' );
				foreach ( $settings as $key => $value ) {
					// remove options from WordPress database
					delete_option( $value );
				}
			}
		} // end method __construct()
		
	} // end class wp_html_sitemap_uninstall
	
} // end if class exists

// instantiate new wp_html_sitemap_uninstall object
if ( class_exists( 'wp_html_sitemap_uninstall' ) ) {
	new wp_html_sitemap_uninstall();
}

?>