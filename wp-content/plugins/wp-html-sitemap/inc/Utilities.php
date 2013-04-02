<?php

/*
	WP HTML Sitemap
	Contact: Bill Edgar (bill.edgar@oaktondata.com)
	http://www.oaktondata.com/wordpress-html-sitemap
	
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
	
	This is the wp_html_sitemap_Utilities PHP class file.
	
 */

if ( !class_exists( 'wp_html_sitemap_Utilities' ) ) {

	/** wp_html_sitemap_Utilities class
	 *
	 * static PHP class for general tools/utilities.
	 * @author wmsedgar
	 *
	 */
	abstract class wp_html_sitemap_Utilities {
		/* public variables */
		
		/* private variables */
		
		/**
		 * old style constructor method for backward PHP compatibility
		 */ 
		public function wp_html_sitemap_Utilities() {
			$this->__construct();
		}
		
		/**
		 * public constructor method
		 */
		public function __construct() {
			
		}
		
		/*
		 * private functions
		 */
		
		/*
		 * public functions
		 */
		
		/**
		 * Inserts any number of scalars or arrays at the point
		 * in the haystack immediately after the search key ($needle) was found,
		 * or at the end if the needle is not found or not supplied.
		 * Modifies $haystack in place.
		 * @param array &$haystack the associative array to search. This will be modified by the function
		 * @param string $needle the key to search for
		 * @param mixed $stuff one or more arrays or scalars to be inserted into $haystack
		 * @return int the index at which $needle was found
		 */
		public function array_insert_after( $haystack, $needle = '', $stuff ) {
			if ( !is_array( $haystack ) ) return $haystack;
			$new_array = array();
			for ( $i = 2; $i < func_num_args(); ++$i ){
				$arg = func_get_arg( $i );
				if ( is_array( $arg ) ) $new_array = array_merge( $new_array, $arg );
				else $new_array[] = $arg;
			}
			$i = 0;
			foreach( $haystack as $key => $value ){
				++$i;
				if ( $key == $needle ) break;
			}
			$haystack = array_merge(
					array_slice( $haystack, 0, $i, true ),
					$new_array, array_slice( $haystack, $i, null, true )
			);
			return $haystack;
		}
		
		/**
		 *
		 * Find string $search and replace with string $replace within $buffer.
		 *
		 * @param string $buffer
		 *
		 * @return string
		 */
		public function bufferFilter( $buffer ) {
			return str_replace( 'h3', 'p', $buffer );
		}
		
		public function detectPlugin( $uri, $plugin_name ) {
			$active = $installed = false;
			$plugin_list = get_plugins();
			$uris = wp_list_pluck( $plugin_list, 'PluginURI' );
			if ( in_array( $uri, $uris ) ) {
				$installed = true;
				// plugin is installed, now check to see if it's active
				if ( $plugin_file = array_search( $uri, $uris ) ) {
					if ( $check = is_plugin_active( $plugin_file ) ) {
						// then plugin is active
						$active = true;
					}
				}
			}
			return array( 'active' => $active, 'installed' => $installed );
		}
		
		/**
		 * 
		 * method to retrieve list of WP post categories
		 * 
		 * @return array $categories (formatted as $categories['cat_1'] = 'category_one' )
		 */
		public function getCategories() {
			$categories = array();
			$args = array(
			 	'orderby' => 'name',
				'order' => 'ASC'
				);
			$categoryObjs = get_categories( $args );
			foreach ( $categoryObjs as $category ) {
				$categories['cat_' . $category->cat_ID] = $category->name ;
			}
			return $categories;
		}
		
		/**
		 * 
		 * method to retrieve WP comment count for a post/page by title
		 * 
		 * @param string $endDate
		 * @param string $title
		 * @param string $startDate
		 * 
		 * @return integer
		 */
		public function getCommentCountByTitle( $endDate, $title, $startDate ) {
			global $wpdb;
			$myQuery = array( 
				"SELECT COUNT(*) AS comment_count, post_title ",
				"FROM {$wpdb->prefix}posts AS t1 INNER JOIN {$wpdb->prefix}comments as t2 ON t1.ID = t2.comment_post_ID ",
				"WHERE t2.comment_date >= '" . $startDate . " 00:00:00' AND t2.comment_date <= '" . $endDate . " 23:59:59' ",
				"AND t1.post_title = '$title' ",
				"GROUP BY ID ",
				"ORDER BY comment_count DESC LIMIT 1"
				);
			$posts = $wpdb->get_results( join( '', $myQuery ) );
			foreach ( $posts as $post ) {
				return $post->comment_count;
			}
			return 0;
		}
		
		/**
		 * 
		 * Get html from external file using file_get_contents, return html output.
		 * 
		 * @param string $target
		 * @param array $replacement_array
		 * 
		 * @return string $html
		 */
		public function getFilteredHtml( $target, $replacement_array = array() ) {
			// output linechart settings begin html
			$html = file_get_contents(
				$filename = $target,
				$use_include_path = false
				);
			if ( !$html ) {
				throw new Exception( 'wp_html_sitemap_Utilities::getFilteredHtml unable to load contents from ' . $target );
			} else {
				if ( !empty( $replacement_array ) ) {
					foreach ( $replacement_array as $key => $value ) {
						$html = str_replace( $key, $value, $html );
					}
				}
				return $html;
			}
		}
		
		/**
		 * 
		 * method for retrieving a specified option (group) from WP database.
		 * 
		 * @param string $group
		 * @param string $section
		 * @return array $options
		 */
		public function getOptions( $group, $section = '' ) {
			$options = get_option(
				$show = $group,
				$default = false
				);
			if ( empty( $section ) ) {
				return $options;
			} else {
				return $options[$section];
			}
		}
		
		public function getPages() {
			$page_list = array();
			$myPages = get_pages( array( 'sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page' ) );
			foreach ( $myPages as $page_obj ) {
				$page_list[$page_obj->ID] = $page_obj->post_title;
			}
			return $page_list;
		}
		
		public function getPosts() {
			$post_list = array();
			$myPosts = get_pages( array( 'sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'post' ) );
			foreach ( $myPosts as $post_obj ) {
				$post_list[$post_obj->ID] = $post_obj->post_title;
			}
			return $post_list;
		}
		
		public function getPageTemplates() {
			$templates = get_page_templates();
			$template_list = array();
			foreach ( $templates as $template_name => $template_filename ) {
				$template_list[$template_filename] = $template_name;
			}
			// add default to list
			$template_list['default'] = 'Default';
			return $template_list;
		}
		
		public function getPostFormats() {
			$my_formats = array();
			if ( current_theme_supports( 'post-formats' ) ) {
				$post_formats = get_theme_support( 'post-formats' );
				if ( is_array( $post_formats[0] ) ) {
					foreach ( $post_formats[0] as $key => $format ) {
						$my_formats[$format] = $format;
					}
					$my_formats['Standard'] = 'Standard';
				} else {
					$my_formats['Standard'] = 'Standard';
				}
			} else {
				$my_formats['Standard'] = 'Standard';
			}
			return $my_formats;
		}
		
		/**
		 *
		 * method to push selection keys from an associative array into a list
		 *
		 * @param array $selections
		 *
		 * @return array $list
		 */
		function getSelections( $selections ) {
			$list = Array();
			// check to make sure selections is an array
			if ( is_array( $selections ) ) {
				// check to make sure selections is an associative array
				if ( wp_html_sitemap_Utilities::isAssociative( $selections ) ) {
					foreach ( $selections as $key => $value ) {
						array_push( $list, $key );
					}
					return $list;
				} else {
					throw new wp_html_sitemap_Exception( 'selections list is not an associative array.' );
				}
			} else {
				throw new wp_html_sitemap_Exception( 'selections list is not a valid array.' );
			}
		}
		
		/**
		 * 
		 * method to check if array is associative
		 * 
		 * @param array $arr
		 * 
		 * @return boolean
		 */
		public function isAssociative( $arr ) {
			if ( is_array( $arr ) ) {
				foreach ( $arr as $key => $value ) {
					if ( !is_int( $key ) ) {
						return true;
					}
				}
			}
			return false;
		}
		
		public function remove_items( $list, $values ) {
			foreach ( $values as $key => $value ) {
				foreach ( $list as $k => $v ) {
					if ( $v == $value ) {
						unset( $list[$k] );
					}
				}
			}
			return $list;
		}
		
		/**
		 * 
		 * method to validate proper check box values
		 * 
		 * @param string $value
		 * 
		 * @return boolean
		 */
		function validCheckbox( $value ) {
			if ( $value !== 'enabled' && $value !== 'disabled' ) {
				return false;
			} else {
				return true;
			}
		} // end method validCheckbox
		
	} // end class wp_html_sitemap_Utilities
	
} // end if class exists