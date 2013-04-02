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
	
	This is the wp_html_sitemap_map PHP class file.
*/

if ( !class_exists( 'wp_html_sitemap_map' ) ) {
	
	// wp_html_sitemap_options class
	class wp_html_sitemap_map {
		
		/* 
		 * public variables
		 */
		
		/*
		 * private variables
		 */
		private $defaults = 		array(
			'title' =>				'Sitemap',
			'name' =>				'sitemap',
			'template' =>			'default',
			'type' =>				'page',
			'allow_comments' =>		'disabled',
			'allow_pings' =>		'disabled'
			);
		private $ID = 				null;
		
		// public constructor
		public function wp_html_sitemap_map( $parentInstance ) {
			$this->__construct( $parentInstance );
		}
		
		// public constructor
		public function __construct( $parentInstance ) {
			$this->parentInstance = $parentInstance;
			$this->init();
		}
		
		private function createSitemapPage( $args ) {
			$args = $this->setCommentPingStatus( $args );
			extract( $args );
			// create post object
			$sitemap = array(
				'post_title' => 	$title,
				'post_content' => 	'[wp_html_sitemap]',
				'post_name' =>		$name,
				'post_status' =>	'publish',
				'post_type' =>		$type,
				'comment_status' =>	$allow_comments,
				'ping_status' =>	$allow_pings
				);
			$sitemap_ID = wp_insert_post( $post = $sitemap, $wp_error = true );
			if ( is_wp_error( $sitemap_ID ) ) {
				return array( 'result' => false, 'msg' => 'Sitemap page creation failed.', 'ID' => 0 );
			} else {
				return array( 'result' => true, 'msg' => 'Sitemap page creation successful.', 'ID' => $sitemap_ID );
			}
		}
		
		private function getSitemapAttr() {
			// first get options array from wordpress database
			$arr = get_option(
				$show = 'wp-html-sitemap-general',
				$default = false
				);
			// check to see if option array was successfully retrieved
			if ( $arr !== false && !empty( $arr ) ) {
				return $arr;
			// if options have not been saved, go with defaults
			} else {
				return $this->defaults;
			}
		}
		
		private function getSitemapPageID() {
			// first get options array from wordpress database
			$arr = get_option(
				$show = 'wp-html-sitemap-general',
				$default = false
				);
			// check to see if option array was successfully retrieved
			if ( $arr !== false && !empty( $arr ) ) {
				if ( !empty( $arr['page_id'] ) ) {
					return array( 'result' => true, 'msg' => '', 'ID' => $arr['page_id'] );
				}
			}
			return array( 'result' => false, 'msg' => 'Sitemap page ID is not stored.', 'ID' => 0 );
		}
		
		private function init() {
			// first, check if sitemap page is already created
			$checkID = $this->getSitemapPageID();
			// retrieve sitemap page attributes array
			$args = $this->getSitemapAttr();
			// if sitemap page doesn't exist, create it now
			if ( !$checkID['result'] ) {
				$create = $this->createSitemapPage( $args );
				// if page was created successfully
				if ( $create['result'] ) {
					// store page ID
					$this->ID = $create['ID'];
					$store = $this->storeSitemapPageID( $create['ID'] );
					// if page ID was stored successfully
					if ( $store['result'] ) {
						// update page template
						$args['ID'] = $create['ID'];
						$update = $this->updateSitemapTemplate( $args );
						// if page template could not be updated
						if ( !$update['result'] ) {
							echo '<p>' . $update['msg'] . '</p>';
						}
					// otherwise page ID store operation failed, output error message
					} else {
						echo '<p>' . $store['msg'] . '</p>';
					}
				// otherwise page creation failed, output error message
				} else {
					echo '<p>' . $create['msg'] . '</p>';
				}
			// if sitemap page exists, update it's attributes
			} else {
				$args['ID'] = $checkID['ID'];
				$update = $this->updateSitemapPage( $args );
				// if sitemap page update failes, output error message
				if ( !$update['result'] ) {
					echo '<p>' . $update['msg'] . '</p>';
				}
				$update = $this->updateSitemapTemplate( $args );
				// if sitemap template update fails, output error message
				if ( !$update['result'] ) {
					echo '<p>' . $update['msg'] . '</p>';
				}
			}
		}
		
		private function setCommentPingStatus( $args ) {
			if ( $args['allow_comments'] == 'disabled' ) {
				$args['allow_comments'] = 'closed';
			} else {
				$args['allow_comments'] = 'open';
			}
			if ( $args['allow_pings'] == 'disabled' ) {
				$args['allow_pings'] = 'closed';
			} else {
				$args['allow_pings'] = 'open';
			}
			return $args;
		}
		
		private function storeSitemapPageID( $page_id ) {
			// first get options array from wordpress database
			$arr = get_option(
				$show = 'wp-html-sitemap-general',
				$default = false
				);
			// check to see if option array was successfully retrieved
			if ( $arr !== false && !empty( $arr ) ) {
				if ( empty( $arr['page_id'] ) && $arr['page_id'] != $page_id ) {
					$arr['page_id'] = $page_id;
					// update options in wordpress database
					$result = update_option(
						$option_name = 'wp-html-sitemap-general',
						$newvalue = $arr
						);
					if ( $result ) {
						return array( 'result' => true, 'msg' => '', 'ID' => $arr['page_id'] );
					}
				} elseif ( $arr['page_id'] != $page_id ) {
					return array( 'result' => true, 'msg' => 'Sitemap page ID already stored.', 'ID' => $arr['page_id'] );
				}
			}
			return array( 'result' => false, 'msg' => 'Sitemap page ID store operation failed.', 'ID' => $arr['page_id'] );
		}
		
		private function updateSitemapPage( $args ) {
			$args = $this->setCommentPingStatus( $args );
			extract( $args );
			// create post object
			$sitemap = array(
				'ID' =>				$ID,
				'post_title' => 	$title,
				'post_content' => 	'[wp_html_sitemap]',
				'post_name' =>		$name,
				'post_status' =>	'publish',
				'post_type' =>		$type,
				'comment_status' =>	$allow_comments,
				'ping_status' =>	$allow_pings
				);
			$sitemap_ID = wp_update_post( $post = $sitemap );
			if ( $sitemap_ID == 0 ) {
				return array( 'result' => false, 'msg' => 'Sitemap page update failed.', 'ID' => 0 );
			} else {
				return array( 'result' => true, 'msg' => 'Sitemap page update successful', 'ID' => $sitemap_ID );
			}
		}
		
		private function updateSitemapTemplate( $args ) {
			extract( $args );
			if ( !empty( $ID ) && !empty( $template ) ) {
				update_post_meta( 
					$id = $ID,
					$meta_key = '_wp_page_template',
					$meta_value = $template
					);
				return array( 'result' => true, 'msg' => '', 'ID' => $ID );
			} else {
				return array( 'result' => false, 'msg' => 'Sitemap template update failed.', 'ID' => $ID );
			}
		}
		
		/* -------------------------------------------------------------------------*/
		/* Public API
		/* -------------------------------------------------------------------------*/
		
		public function getID() {
			return $this->ID;
		}
		
	} // end class wp_html_sitemap_map
} // end if wp_html_sitemap_map class exists
?>