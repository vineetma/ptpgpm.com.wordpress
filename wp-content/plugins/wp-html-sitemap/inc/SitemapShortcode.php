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
	
	This is the wp_html_sitemap_shortcode PHP class file.
*/

if ( !class_exists( 'wp_html_sitemap_shortcode' ) ) {

	// wp_html_sitemap_shortcode class
	abstract class wp_html_sitemap_shortcode {
		
		private static $defaults = 			array(
			'wp-html-sitemap-general' =>	array(
				'title' =>					'Sitemap'
				),
			'wp-html-sitemap-sections' =>	array(
				'sections_displayed' =>		array( 'pages', 'categories', 'posts', 'authors' ),
				'sections_excluded' =>		array(),
				)
			);
		/* -------------------------------------------------------------------------*/
		/* Constructor
		/* -------------------------------------------------------------------------*/
		
		/**
		 * Constructor method.
		 *
		 * @param array $atts
		 */
		function wp_html_sitemap_shortcode( $atts = array() ) {
			// initialize
			return wp_html_sitemap_shortcode::start( $atts );
		} // end constructor method
		
		/* -------------------------------------------------------------------------*/
		/* Private Functions
		/* -------------------------------------------------------------------------*/
		
		private function authors( $opts ) {
			/*
			 * @todo: Make title for section customizable
			 * @todo: Provide option for toggling exclude_admin from authors list
			 * @todo: Provide option to display post count for author
			 * @todo: Provide orderby options
			 */
			// author section header
			$html = '<div id="wp_html_sitemap_authors" class="wp_html_sitemap_authors"><h2>Authors</h2><ul>';
			// authors list in valid html list
			$html .= wp_list_authors( array(
				'exclude_admin' => 	false,
				'echo' =>			0,
				'optioncount' =>	false
				) );
			// close author section
			$html .= '</ul></div>';
			// return html
			return $html;
		}
		
		private function categories( $opts ) {
			/*
			 * @todo: Make title for section customizable
			 * @todo: Provide orderby options
			 */
			// categories section header
			$html = '<div id="wp_html_sitemap_categories" class="wp_html_sitemap_categories"><h2>Categories</h2><ul>';
			// get category list
			$cats = get_categories( array(
				'exclude' =>		'',
				'hide_empty' =>		1,
				'number' =>			'',
				'order' =>			'ASC',
				'orderby' =>		'name'
				) );
			// put category list into linked html list
			foreach ( $cats as $cat ) {
				$html .= "<li><a href='" . get_category_link( $cat->cat_ID ) . "'>" . $cat->cat_name . "</a>";
			}
			// close section
			$html .= '</ul></div>';
			// return html
			return $html;
		}
		
		private function forums( $opts ) {
			/*
			 * @todo: Make title for section customizable
			 * @todo: Provide orderby options
			 */
			// section header
			$html = '<div id="wp_html_sitemap_forums" class="wp_html_sitemap_forums"><h2>Forums</h2><ul>';
			// retrieve posts
			$posts = get_posts( array(
				'category' =>		'',
				'exclude' =>		'',
				'numberposts' =>	'',
				'order' =>			'ASC',
				'orderby' =>		'post_title',
				'post_status' =>	'publish',
				'post_type' =>		'forum'
				) );
			foreach ( $posts as $post ) {
				$html .= '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
			}
			// close section
			$html .= '</ul></div>';
			// return html
			return $html;
		}
		
		private function getSettings() {
			$arr = array();
			// first get options array from wordpress database
			$arr['wp-html-sitemap-sections'] = get_option(
				$show = 'wp-html-sitemap-sections',
				$default = false
				);
			// check to see if option array was successfully retrieved
			if ( $arr !== false && !empty( $arr ) ) {
				$arr['wp-html-sitemap-general']['title'] = wp_html_sitemap_shortcode::$defaults['wp-html-sitemap-general']['title'];
				return $arr;
				// if options have not been saved, go with defaults
			} else {
				return wp_html_sitemap_shortcode::$defaults;
			}
		}
		
		private function outputPage( $opts ) {
			$html = '';
			foreach ( $opts['wp-html-sitemap-sections']['sections_displayed'] as $key => $section ) {
				switch ( $section ) {
					case 'authors':
						$html .= wp_html_sitemap_shortcode::authors( $opts );
						break;
					case 'categories':
						$html .= wp_html_sitemap_shortcode::categories( $opts );
						break;
					case 'forums':
						$html .= wp_html_sitemap_shortcode::forums( $opts );
						break;
					case 'pages':
						$html .= wp_html_sitemap_shortcode::pages( $opts );
						break;
					case 'posts':
						$html .= wp_html_sitemap_shortcode::posts( $opts );
						break;
					case 'products':
						$html .= wp_html_sitemap_shortcode::products( $opts );
						break;
					case 'topics':
						$html .= wp_html_sitemap_shortcode::topics( $opts );
						break;
				}
			}
			return $html;
		}
		
		private function pages( $opts ) {
			/*
			 * @todo: Make title for section customizable
			 * @todo: Provide orderby options
			 */
			// section header
			$html = '<div id="wp_html_sitemap_pages" class="wp_html_sitemap_pages"><h2>Pages</h2><ul>';
			// get pages list in valid html list
			$html .= wp_list_pages( array(
				'depth' =>			0,
				'echo' => 			0,
				'exclude' => 		'',
				'show_date' =>		'',
				'sort_column' =>	'post_title',
				'sort_order' =>		'ASC',
				'title_li' => 		''
				) );
			// close section
			$html .= '</ul></div>';
			// return html
			return $html;
		}
		
		private function posts( $opts ) {
			/*
			 * @todo: Make title for section customizable
			 * @todo: Provide orderby options
			 */
			// section header
			global $wpdb;
			$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'");
			if ( $numposts < 0 ) {
				$numposts = 100;
			}
			$html = '<div id="wp_html_sitemap_posts" class="wp_html_sitemap_posts"><h2>Posts</h2><ul>';
			// retrieve categories list
			$cats = get_categories( array(
				'exclude' =>		'',
				'hide_empty' =>		1,
				'number' =>			'',
				'order' =>			'ASC',
				'orderby' =>		'name'
				) );
			// retrieve posts
			$posts = get_posts( array(
				'category' =>		'',
				'exclude' =>		'',
				'numberposts' =>	$numposts,
				'order' =>			'DESC',
				'orderby' =>		'post_date',
				'post_status' =>	'publish',
				'post_type' =>		'post'
				) );
			// loop for each category
			foreach ($cats as $cat) {
				$html .= "<li><h3>" . $cat->cat_name . "</h3>";
				$html .= "<ul>";
				// loop through posts
				foreach ( $posts as $post ) {
					$category = get_the_category( $post->ID );
					// Only display a post link once, even if it's in multiple categories
					if ( $category[0]->cat_ID == $cat->cat_ID ) {
						$html .= '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
					}
				}
				$html .= "</ul></li>";
			}
			// close section
			$html .= '</ul></div>';
			// return html
			return $html;
		}
		
		private function products( $opts ) {
			/*
			 * @todo: Make title for section customizable
			 * @todo: Provide orderby options
			 */
			// section header
			$html = '<div id="wp_html_sitemap_products" class="wp_html_sitemap_products"><h2>Products</h2><ul>';
			// retrieve posts
			$posts = get_posts( array(
				'category' =>		'',
				'exclude' =>		'',
				'numberposts' =>	'',
				'order' =>			'ASC',
				'orderby' =>		'post_title',
				'post_status' =>	'publish',
				'post_type' =>		'wpsc-product'
				) );
			foreach ( $posts as $post ) {
				$html .= '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
			}
			// close section
			$html .= '</ul></div>';
			// return html
			return $html;
		}
		
		private function topics( $opts ) {
			/*
			 * @todo: Make title for section customizable
			 * @todo: Provide orderby options
			 */
			// section header
			$html = '<div id="wp_html_sitemap_topics" class="wp_html_sitemap_topics"><h2>Topics</h2><ul>';
			// retrieve posts
			$posts = get_posts( array(
				'category' =>		'',
				'exclude' =>		'',
				'numberposts' =>	'',
				'order' =>			'ASC',
				'orderby' =>		'post_title',
				'post_status' =>	'publish',
				'post_type' =>		'topic'
				) );
			// loop through each post
			foreach ( $posts as $post ) {
				$html .= '<li><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';
			}
			// close section
			$html .= '</ul></div>';
			// return html
			return $html;
		}
		
		/* -------------------------------------------------------------------------*/
		/* Public API
		/* -------------------------------------------------------------------------*/
		
		public function start( $atts = array() ) {
			// retrieve settings
			$opts = wp_html_sitemap_shortcode::getSettings();
			// get html output
			$html = wp_html_sitemap_shortcode::outputPage( $opts );
			// return html output
			return $html;
		}
	} // end wp_html_sitemap_shortcode class
} // end if class exists