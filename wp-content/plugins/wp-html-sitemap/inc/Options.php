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
	
	This is the wp_html_sitemap_options PHP class file.
*/

if (!class_exists('wp_html_sitemap_options')) {
	
	// wp_html_sitemap_options class
	class wp_html_sitemap_options {
		
		/* 
		 * public variables
		 */
		
		public $OPTIONS = 					array(
			'wp-html-sitemap-general' =>	array(
				'options' =>				array(),
				'defaults' =>				array(),
				'page' =>					'general'
				),
			'wp-html-sitemap-sections' =>	array(
				'options' =>				array(),
				'defaults' =>				array(),
				'page' =>					'sections'
				)
			);
			
		public $TABS = 						array(
			'general' =>					'General',
			'sections' =>					'Sections',
			'authors' =>					'Authors',
			'categories' =>					'Categories',
			'pages' =>						'Pages',
			'posts' =>						'Posts',
			'about' =>						'About'
			);
		
		/*
		 * private variables
		 */
		private $bbPress_check = 			false;
		private $WPEC_check = 				false;
		private $option_groups = 			array(
			'wp-html-sitemap-general', 
			'wp-html-sitemap-sections'
			);
		private $page_hooks = 				array();
		private $parentInstance = 			null;
		private $scripts = 					array();
		private $section_selections = 		array( 
			'authors',
			'categories', 
			'pages', 
			'posts' 
			);
		private $settings = 			array();
		private $styles = 				array();
		private $types = 				array( 'page', 'post' );
		
		// public constructor
		public function wp_html_sitemap_options( $parentInstance ) {
			$this->__construct( $parentInstance );
		}
		
		// public constructor
		public function __construct( $parentInstance ) {
			// record parent object reference
			$this->parentInstance = $parentInstance;
			// load default options
			$this->OPTIONS['page_templates'] = wp_html_sitemap_Utilities::getPageTemplates();
			$this->OPTIONS['post_formats'] = wp_html_sitemap_Utilities::getPostFormats();
			$this->checkInstalledPlugins();
			$this->initializeOptions();
			$this->loadOptionDefaults();

			// actions
			// add action for admin initialization
			add_action(
				$tag = 'admin_init',
				$callback = array( &$this, 'admin_init' ),
				$priority = 1
				);
			// add menu item for options page
			add_action(
				$tag = 'admin_menu',
				$callback = array( &$this, 'admin_menu' ),
				$priority = 1
				);
				
			// filters
		
		}
		
		private function checkInstalledPlugins() {
			// check for WP e-Commerce
			$WPEC_check = wp_html_sitemap_Utilities::detectPlugin( 'http://getshopped.org/', 'WP e-Commerce' );
			// check for bbPress
			$bbPress_check = wp_html_sitemap_Utilities::detectPlugin( 'http://bbpress.org', 'bbPress' );
			// if bbPress is installed and active
			if ( $bbPress_check['installed'] && $bbPress_check['active'] ) {
				$this->bbPress_check = true; // set object property
				// add forums and topics to sections list, if necessary
				if ( !in_array( 'forums', $this->section_selections ) ) { 
					array_push( $this->section_selections, 'forums' );
				}
				if ( !in_array( 'topics', $this->section_selections ) ) {
					array_push( $this->section_selections, 'topics' );
				}
				// add forums and topics to tabs list, if necessary
				if ( !array_key_exists( 'forums', $this->TABS ) ) {
					$this->TABS = wp_html_sitemap_Utilities::array_insert_after( $this->TABS, 'categories', array( 'forums' => 'Forums' ) );
				}
				if ( !array_key_exists( 'topics', $this->TABS ) ) {
					$this->TABS = wp_html_sitemap_Utilities::array_insert_after( $this->TABS, 'posts', array( 'topics' => 'Topics' ) );
				}
			} else {
				if ( array_key_exists( 'forums', $this->TABS ) ) {
					$this->TABS = wp_html_sitemap_Utilities::remove_items( $this->TABS, array( 'Forums' ) );
				}
				if ( array_key_exists( 'topics', $this->TABS ) ) {
					$this->TABS = wp_html_sitemap_Utilities::remove_items( $this->TABS, array( 'Topics' ) );
				}
				if ( in_array( 'forums', $this->section_selections ) ) {
					$this->section_selections = wp_html_sitemap_Utilities::remove_items( $this->section_selections, array( 'forums' ) );
				}
				if ( in_array( 'topics', $this->section_selections ) ) {
					$this->section_selections = wp_html_sitemap_Utilities::remove_items( $this->section_selections, array( 'topics' ) );
				} 
			}
			// if WPEC is installed and active
			if ( $WPEC_check['installed'] && $WPEC_check['active'] ) {
				$this->WPEC_check = true; // set object property
				// add products to sections list, if necessary
				if ( !in_array( 'products', $this->section_selections ) ) {
					array_push( $this->section_selections, 'products' );
				}
				// add products to tabs list, if necessary
				if ( !array_key_exists( 'products', $this->TABS ) ) {
					$this->TABS = wp_html_sitemap_Utilities::array_insert_after( $this->TABS, 'posts', array( 'products' => 'Products' ) );
				}
			} else {
				if ( array_key_exists( 'products', $this->TABS ) ) {
				 $this->TABS = wp_html_sitemap_Utilities::remove_items( $this->TABS, array( 'Products' ) );
				}
				if ( in_array( 'products', $this->section_selections ) ) {
					$this->section_selections = wp_html_sitemap_Utilities::remove_items( $this->section_selections, array( 'products' ) );
				}
			}
			// resort section selections
			sort( $this->section_selections );
		}
		
		private function initializeOptions() {
			$this->OPTIONS['wp-html-sitemap-general']['options'] = array(
				'title' =>					null,
				'name' =>					null,
				'template' => 				null,
				'type' =>					null,
				'allow_comments' =>			null,
				'allow_pings' =>			null,
				'page_id' =>				null
				);
			$this->OPTIONS['wp-html-sitemap-sections']['options'] = array(
				'sections_displayed' =>		array(),
				'sections_excluded' => 		array()
				);
		}
		
		private function loadOptionDefaults() {
			$this->OPTIONS['wp-html-sitemap-general']['defaults'] = array(
				'title' =>					'Sitemap',
				'name' =>					'html-sitemap',
				'template' => 				'default',
				'type' =>					'page',
				'allow_comments' =>			'disabled',
				'allow_pings' =>			'disabled',
				'page_id' =>				null
				);
			$this->OPTIONS['wp-html-sitemap-sections']['defaults'] = array(
				'sections_displayed' =>		$this->section_selections,
				'sections_excluded' =>		array()
				);
		}
		
		// method to set options defaults
		private function setOptionDefaults( $group ) {
			if ( $group != 'page_templates' && $group != 'post_formats' ) {
				foreach ( $this->OPTIONS[$group]['options'] as $key => $value ) {
					// reset options to defaults
					$this->OPTIONS[$group]['options'][$key] = $this->OPTIONS[$group]['defaults'][$key];
				}
			}
		}
		
		// method to add plugin options
		function add_options() {
			
			try {
				// load settings from external file
			    $return = $this->load_file_input(
			    	$file = 		WP_HTML_SITEMAP_ADD_OPTIONS,
			    	$object = 		$this,
			    	$target = 		'settings',
			    	$pattern = 		"/obj=/",
			    	$delimiter = 	"/[\s]+/",
			    	$operator = 	"/=/"
			    	);
			} catch ( wp_html_sitemap_Exception $e ) {
				echo $e->getError();
				die( '<p>WP HTML Sitemap exiting.</p>' );
			}
		    	
			// verify load, report error
		    if ( !$return['retval'] ) {
		    	die( $return['msg'] );
		    }
			
			// loop through each setting object
	    	foreach ( $this->$target as $key => $setting ) {
	    		switch ( $setting['obj'] ) {
	    			case 'setting':
	    				// register setting with wordpress
	    				register_setting(
	    					$option_group = $setting['option_group'],
	    					$option_name = $setting['option_name'],
	    					$sanitize_callback = array( &$this, $setting['sanitize_callback'] )
	    					);
	    				break;
	    			case 'section':
	    				// add settings section to wordpress
	    				add_settings_section(
	    					$id = $setting['id'],
	    					$title = $setting['title'],
	    					$callback = array( &$this, $setting['callback'] ),
	    					$page = $setting['page']
	    					);
	    				break;
	    			case 'field':
	    				// add field to settings section
	    				if ( key_exists( 'selections', $setting['args'] ) ) {
	    					if ( isset( $this->$setting['args']['selections'] ) ) {
	    						$setting['args']['selections'] = $this->$setting['args']['selections'];
	    					}
	    				}
	    				add_settings_field(
	    					$id = $setting['id'],
	    					$title = $setting['title'],
	    					$callback = array( &$this, $setting['callback'] ),
	    					$page = $setting['page'],
	    					$section = $setting['section'],
	    					$args = $setting['args']
	    					);
	    				break;
	    		}
	    	}
	    	// initialize plugin options
			$this->options_init();
		}
		
		private function modifyOptionsList( $items, $action = 'add' ) {
			foreach ( $items as $key => $item ) {
				// check if item is in the options array
				if ( !in_array( $item, $this->OPTIONS['wp-html-sitemap-sections']['options']['sections_displayed'] ) &&
						!in_array( $item, $this->OPTIONS['wp-html-sitemap-sections']['options']['sections_excluded'] ) ) {
					if ( $action == 'add' ) {
						// add value to the options array
						array_push( $this->OPTIONS['wp-html-sitemap-sections']['options']['sections_excluded'], $item );
					}
				} else {
					if ( $action == 'remove' ) {
						$this->OPTIONS['wp-html-sitemap-sections']['options']['sections_excluded'] = 
							wp_html_sitemap_Utilities::remove_items( $this->OPTIONS['wp-html-sitemap-sections']['options']['sections_excluded'], array( $item ) );
						$this->OPTIONS['wp-html-sitemap-sections']['options']['sections_displayed'] =
							wp_html_sitemap_Utilities::remove_items( $this->OPTIONS['wp-html-sitemap-sections']['options']['sections_displayed'], array( $item ) );
					}
				}
			}
			// now update the options array in WP database
			update_option( 
				$option_name = 'wp-html-sitemap-sections',
				$newvalue = $this->OPTIONS['wp-html-sitemap-sections']['options']
				);
		}
		
		// method to initialize admin options and scripts
		function admin_init() {		
			// load options from database
			$this->retrieve_options();
			// verify that bbPress items are in options array if appropriate
			if ( $this->bbPress_check ) {
				$this->modifyOptionsList( array( 'forums', 'topics' ), 'add' );
			} else {
				$this->modifyOptionsList( array( 'forums', 'topics' ), 'remove' );
			}
			// verify that WPEC items are in options array if appropriate
			if ( $this->WPEC_check ) {
				$this->modifyOptionsList( array( 'products' ), 'add' );
			} else {
				$this->modifyOptionsList( array( 'products' ), 'remove' );
			}
			// register options groups, sections, and fields
			$this->add_options();
			
			// scripts, styles, and charts options to be loaded
			$loads = array(
				'scripts' => WP_HTML_SITEMAP_REGISTER_SCRIPTS,
				'styles' => WP_HTML_SITEMAP_REGISTER_STYLES
				);
			
			// loop for each file
			foreach ( $loads as $key => $value ) {
				try {
					// load external file
					$return = $this->load_file_input(
				    	$file = $value,
				    	$object = $this,
				    	$target = $key,
				    	$pattern = "/obj=/",
				    	$delimiter = "/[\s]+/",
				    	$operator = "/=/"
				    	);
			    } catch ( wp_html_sitemap_Exception $e ) {
			    	echo $e->getError();
			    	die( '<p>WP HTML Sitemap exiting.</p>' );
			    }
			}
		    
		    // register scripts with wordpress
		    $this->process_scripts();
		    // register styles with wordpress
		    $this->process_styles();
				
		}
		
		// method to create admin menu items
		function admin_menu() {
			
			// add WP HTML Sitemap options page link to settings menu
			$page_hook = add_options_page(
				$page_title = 'WP HTML Sitemap Options',
				$menu_title = 'WP HTML Sitemap',
				$capability = 'manage_options',
				$menu_slug = 'wp-html-sitemap',
				$callback = array( &$this, 'admin_page' )
				);
				
			$this->page_hooks['OPTIONS'] = $page_hook;	
			
			// add action to output scripts for options page
			add_action(
				$tag = 'admin_print_scripts-' . $this->page_hooks['OPTIONS'],
				$callback = array( &$this, 'admin_scripts' ),
				$priority = 1
				);
			
			// add action to output stylesheets for options page
			add_action(
				$tag = 'admin_print_styles-' . $this->page_hooks['OPTIONS'],
				$callback = array( &$this, 'admin_styles' ),
				$priority = 1
				);
				
		}
		
		// method to create options page
		function admin_page() {
			try {
				if ( class_exists( 'wp_html_sitemap_AdminPage' ) ) {	
					new wp_html_sitemap_AdminPage( $parentInstance = $this );
				} else {
					// failed to load wp_html_sitemap_AdminPage class
					throw new wp_html_sitemap_Exception( 'wp_html_sitemap_AdminPage class not loaded.' );
				}
			} catch ( wp_html_sitemap_Exception $e ) {
				echo $e->getError();
			}
		}
		
		// method to load custom javascripts
		function admin_scripts() {
			// add post formats to OPTIONS array
			$this->OPTIONS['post_formats'] = wp_html_sitemap_Utilities::getPostFormats();
			// loop through each script object
		    foreach ( $this->scripts as $key => $script ) {
		    	wp_enqueue_script( $handle = $script['handle'] );
		    	// catch localize flag
		    	if ( $script['localize'] === 'yes' ) {
		    		// encode PHP options array into json string
		    		$parameters = array( 'json_str' => json_encode( $this->$script['l10n'] ) );
		    		// localize script with json encoded options string as variable
		    		wp_localize_script(
		    			$handle = $script['handle'],
		    			$object_name = $script['object_name'],
		    			$l10n = $parameters
		    			);
		    	}
		    }			
		}
		
		// method to load css styles
		function admin_styles() {
			// loop through each style object
			foreach ( $this->styles as $key => $style ) {
				wp_enqueue_style( $handle = $style['handle'] );
			}
		}
		
		// method to load scripts & styles from external file
		function load_file_input( $file, $object, $target, $pattern, $delimiter, $operator ) {
			// read contents of script registration file
			$input_stream = file_get_contents(
		    	$filename = $file,
		    	$use_include_path = false
		    	);
		    // if input file is not able to be read, alert
		    if ( !$input_stream ) {
		    	throw new wp_html_sitemap_Exception( "Unable to load $file." );
		    // otherwise proceed
		    } else {
		    	// process input stream
		    	$this->process_file_input(
		    		$input = $input_stream,
		    		$load_obj = $object,
		    		$load_target = $target,
		    		$obj_pattern = $pattern,
		    		$item_delimiter = $delimiter,
		    		$assignment_operator = $operator
		    		);
		    	if ( $load_obj->$load_target ) {
		    		return array( 'retval' => true, 'msg' => "info: loaded $file." );
		    	} else {
		    		throw new wp_html_sitemap_Exception( "Unable to load file contents from $file." );
		    	}
		    }
		}
		
		// method to initialize options array if necessary
		function options_init() {
			// make sure options array is not empty
			foreach ( $this->OPTIONS as $group => $properties ) {
				if ( $group != 'page_templates' && $group != 'post_formats' ) {
					if ( array_filter( $properties['options'] ) ) {
						// check each option for empty value
						foreach ( $properties['options'] as $key => $value ) {
							if ( !isset( $value ) ) {
								$properties['options'][$key] = $properties['defaults'][$key];
							}
						}
						$this->OPTIONS[$group] = $properties;
					// if options array is empty, populate with defaults
					} else {
						$this->setOptionDefaults( $group );
					}
					// update options in wordpress database
					update_option(
						$option_name = $group,
						$newvalue = $this->OPTIONS[$group]['options'] 
						);
				}
			}
		}
		
		// method to output settings section text
		function output_section_text( $section ) {
			// explode the section array into variables
			extract( $section );
			$text = '';
			switch ( $id ) {
				default :
					foreach ( $this->settings as $key => $setting ) {
						if ( array_key_exists( 'id', $setting ) && $setting['id'] == $id ) {
							if ( array_key_exists( 'text', $setting) ) { $text = $setting['text']; }
							break;
						}
					}
					echo '<p>' . $text . '</p>';
					break;
			}
		} // end method output_section_text
		
		/**
		* Helper function for outputting form fields.
		*
		* @args array array of field attributes (name, id, class, type, value, onchange,
		* onclick, onload, readonly, size, my_selection)
		*/
		public function output_setting_field( $args ) {
			// explode args array into directly addressable variables
			extract( $args );
			// check class, onchange, onclick, onload, and readonly properties
			$class = ( isset( $class ) ) ? 'class="' . $class . '" ' : '';
			$cols = ( isset( $cols ) ) ? 'cols="' . $cols . '" ' : '';
			$multiple = ( isset( $multiple ) ) ? 'multiple="' . $multiple . '" ' : '';
			$onchange = ( isset( $onchange ) ) ? 'onchange="' . $onchange . '()" ' : '';
			$onclick = ( isset( $onclick ) ) ? 'onclick="' . $onclick . '()" ' : '';
			$onload = ( isset( $onload ) ) ? 'onload="' . $onload . '()" ' : '';
			$readonly = ( isset( $readonly ) ) ? 'readonly="' . $readonly . '" ' : '';
			$rows = ( isset( $rows ) ) ? 'rows="' . $rows . '" ' : '';
			$size = ( isset( $size ) ) ? 'size="' . $size . '" ' : '';
			$style = ( isset( $style ) ) ? 'style="' . $style . '" ' : '';
			
			if ( $type === 'checkbox' ) {
				// detect check box and set value
				$checked = (  $this->OPTIONS[$group]['options'][$name] === 'enabled' ) ? 'checked="checked" ' : '';
				$value = 'value="enabled" ';
			} else {
				$value = 'value="' . $this->OPTIONS[$group]['options'][$name] . '" ';
				$checked = '';
			}
				
			// determine open and close tags based on type
			if ( $type === 'dropdown' || $type === 'multi-select' ) {
				$open_tag = '<select id="';
				$close_tag = '>';
			} else {
				$open_tag = '<input id="';
				$close_tag = ' />';
			}
			// alter name if it is a multi-select for array
			$name = ( $type != 'multi-select' ) ? 'name="' . $group . '[' . $name.']" ' : 'name="' . $group . '[' . $name . '[]]" ';
			
			// output field
			$field = $open_tag . $input_id . '" ' .
			$name .
			$checked .
			$class .
			$cols .
			$multiple .
			$onchange .
			$onclick .
			$onload .
			$readonly .
			$rows .
			$size .
			$style;
			if ( $type != 'multi-select' ) {
				$field .= ' type="' . $type . '" ';
			}
			$field .= $value . $close_tag;
			echo $field;
			// continue to populate dropdown
			if ( $type === 'dropdown' || $type === 'multi-select' ) {
				// check to make sure selections is an array
				if ( is_array( $selections ) ) {
					if ( !wp_html_sitemap_Utilities::isAssociative( $selections ) ) {
						foreach ( $selections as $key => $item ) {
							if ( $type === 'dropdown' ) {
								$selected = ( $this->OPTIONS[$group]['options'][$input_id] == $item ) ? 'selected="selected"' : '';
								echo "<option value='$item' $selected>$item</option>";
							} else {
								$selected = '';
								if ( is_array( $this->OPTIONS[$group]['options'][$input_id] ) ) {
									foreach ( $this->OPTIONS[$group]['options'][$input_id] as $selection ) {
										if ( $selection === $item ) {
											$selected = 'selected="selected"';
											break;
										}
									}
								}
								echo "<option value='$item' $selected>$item</option>";
							}
						}
					} else {
						foreach ( $selections as $key => $item ) {
							if ( $type === 'dropdown' ) {
								$selected = ( $this->OPTIONS[$group]['options'][$input_id] == $key ) ? 'selected="selected"' : '';
								echo "<option value='$key' $selected>$item</option>";
							} else {
								$selected = '';
								if ( is_array( $this->OPTIONS[$group]['options'][$input_id] ) ) {
									foreach ( $this->OPTIONS[$group]['options'][$input_id] as $selection ) {
										if ( $selection == $key ) {
											$selected = 'selected="selected"';
											break;
										}
									}
								}
								echo "<option value='$key' $selected>$item</option>";
							}
						}
					}
								
				}
				echo "</select>";
			}
			echo "</p>";
		}
		
		function process_file_input( $input, $load_obj, $load_target, $obj_pattern, $item_delimiter, $assignment_operator ) {
			
			// array for script items as single raw string captured from file input
	    	$script_string = array();
	    	// match all occurrences of script identification pattern in input
	    	preg_match_all( $obj_pattern, $input, $matches, PREG_OFFSET_CAPTURE );
	    	// loop through all matches for script or style objects and push into temporary stack
	    	foreach ( $matches[0] as $key => $value ) {
	    		// check for end of input and grab appropriately
    			if ( $key+1 === count( $matches[0] ) ) {
    				// for each match occurrence, capture script/style item as single string
    				array_push( $script_string, trim( substr( $input, $value[1] ) ) );
    			} else {
    				// for each match occurrence, capture script/style item as single string
	    			array_push( $script_string, trim( substr( $input, $value[1], $matches[0][$key+1][1]-1-$value[1] ) ) );
    			}
	    	}
	    	// initialize array to hold associative arrays for script items
	    	$script_array = array();
	    	// loop through each raw script string
	    	foreach ( $script_string as $key => $value ) {
	    		// check for quoted items, and temporarily replace with underscores if necessary
	    		if ( preg_match_all( "/'/", $value, $matches, PREG_OFFSET_CAPTURE ) ) {
	    			for ( $k=0; $k<count($matches[0]); $k++ ) {
	    				if ( $matches[0][$k+1][1] - $matches[0][$k][1] > 1 ) {
		    				$raw_quoted_string = substr( $value, $matches[0][$k][1], $matches[0][$k+1][1]-$matches[0][$k][1]+1 );
		    				$mod_quoted_string = str_replace( " ", "_", $raw_quoted_string );
		    				$value = str_replace( $raw_quoted_string, $mod_quoted_string, $value );
	    				}
	    				$k++;
	    			}
	    			
	    		}
				// tokenize based on whitespace
	    		$lines = preg_split( $item_delimiter, $value );
	    		// initialize array to hold actual attributes and values
	    		$item = array();
	    		// initialize array to hold custom callback parameters
	    		$args = array();
	    		// set callback argument flag to false
	    		$args_flag = false;
	    		// loop through each line
	    		foreach ( $lines as $idx => $val ) {
	    			// check for comment character, throw line out if found
	    			if ( !preg_match( "/^\*|\/\*/", $val ) ) {
		    			// tokenize based on '='
		    			$line = preg_split( $assignment_operator, $val );
		    			// check to see if item is source, which may require imploding
		    			if ( $line[0] === 'src' ) {
		    				array_shift( $line );
		    				$src = implode( "=", $line );
		    				$line[0] = 'src';
		    				$line[1] = $src;
		    			// if item is comma delimited list, push into array
		    			} elseif ( preg_match( "/,/", $line[1] ) ) {
		    				$line[1] = preg_split( "/,/", $line[1] );
		    			// check for start of callback args
		    			} elseif ( $line[0] === 'args' && $line[1] === 'start' ) {
		    				$args_flag = true;
		    			// check for end of callback args
		    			} elseif ( $line[0] === 'args' && $line[1] === 'end' ) {
		    				$args_flag = false;
		    				$line[1] = $args;
		    			// if args_flag is already set, grab custom callback parameter
		    			} elseif ( $args_flag ) {
		    				$args[$line[0]] = $line[1];
		    			}
		    			// if args_flag is not set, proceed with line processing and recording
		    			if ( !$args_flag ) {
		    				// check for array in line[1] before reinserting whitespace
		    				if ( is_array( $line[1] ) ) {
		    					foreach ( $line[1] as $k => $v ) {
		    						if ( preg_match( "/'.+_/", $v ) ) {
		    							$line[1][$k] = str_replace( "_", " ", $v );
		    						}
		    						$line[1][$k] = trim( $line[1][$k], "'" );
		    					}
		    				} else {
				    			// re-insert spaces in place of underscores
				    			if ( preg_match( "/'.+_/", $line[1] ) ) {
				    				$line[1] = str_replace( "_", " ", $line[1] );
				    			}
				    			$line[1] = trim( $line[1], "'" );
		    				}
			    			// store item in temporary associative array
			    			$item[$line[0]] = $line[1];
		    			}
	    			}
	    		}
	    		// push item into object attribute
	    		array_push( $load_obj->$load_target, $item );
	    	}
		}
		
		function process_scripts() {
			
			// loop through each script object
	    	foreach ( $this->scripts as $key => $script ) {
	    		// check path and set appropriately
	    		if ( $script['path'] !== 'web' ) {
	    			$script['src'] = WP_HTML_SITEMAP_JS . $script['src'];
	    		}
	    		// register script with wordpress
	    		wp_register_script(
	    			$handle = $script['handle'],
		    		$src = $script['src'],
		    		$deps = $script['deps']
	    			);
	    	}
		}
		
		function process_styles() {
			// loop through each style object
	    	foreach ( $this->styles as $key => $style ) {
	    		switch ( $style['path'] ) {
    				case 'THEMES':
    					$style['src'] = WP_HTML_SITEMAP_THEMES . $style['src'];
    					break;
    				case 'CSS':
    					$style['src'] = WP_HTML_SITEMAP_CSS . $style['src'];
    					break;
	    		}
	    		// finally, register style
	    		wp_register_style(
	    			$handle = $style['handle'],
	    			$src = $style['src']
	    			);		    		
	    	}
		}
		
		// method to load options from database
		function retrieve_options() {
			foreach ( $this->option_groups as $key => $option_group ) {
				// first get options array from wordpress database
				$arr = get_option(
					$show = $option_group,
					$default = false
					);
				// check to see if option array was successfully retrieved
				if ( $arr !== false && !empty( $arr ) ) {
					// loop through options array
					foreach ( $this->OPTIONS as $group => $properties ) {
						// if option group is a match, process properties
						if ( $option_group == $group ) {
							foreach ( $properties['options'] as $key => $value ) {
								// if valid array value exists in database options array, store it
								if ( array_key_exists( $key, $arr ) ) {
									// check for non-empty value
									if ( !empty( $arr[$key] ) ) {
										$properties['options'][$key] = $arr[$key];
									// otherwise set to default
									} else {
										$properties['options'][$key] = $properties['defaults'][$key];
									}
								// otherwise, set default
								} else {
									$properties['options'][$key] = $properties['defaults'][$key];
								}
							}
							$this->OPTIONS[$group] = $properties;
						}
					}
				} else {
					// first time that this version of WP HTML Sitemap has been loaded, option group not found in database
					// load defaults
					$this->setOptionDefaults( $option_group );
				}
			}
		} // end method retrieve_options
		
		function getOptionGroup( $opts ) {
			foreach ( $opts as $key => $value ) {
				if ( preg_match( "/author/", $key ) ) {
					$ret = 'wp-html-sitemap-authors';
					break;
				} elseif ( preg_match ( "/categor/", $key ) ) {
					$ret = 'wp-html-sitemap-categories';
					break;
				} elseif ( preg_match ( "/forum/", $key ) ) {
					$ret = 'wp-html-sitemap-forums';
					break;
				} elseif ( preg_match( "/general/", $key ) ) {
					$ret = 'wp-html-sitemap-general';
					break;
				} elseif ( preg_match ( "/page/", $key ) ) {
					$ret = 'wp-html-sitemap-pages';
					break;
				} elseif ( preg_match ( "/post/", $key ) ) {
					$ret = 'wp-html-sitemap-posts';
					break;
				} elseif ( preg_match ( "/product/", $key ) ) {
					$ret = 'wp-html-sitemap-products';
					break;
				} elseif ( preg_match( "/section/", $key ) ) {
					$ret = 'wp-html-sitemap-sections';
					break;
				} elseif ( preg_match ( "/topic/", $key ) ) {
					$ret = 'wp-html-sitemap-topics';
					break;
				}
			}
			return $ret;
		}
		
		// method for trimming options values and eliminating bogus array keys
		function sanitize( $opts ) {
			$sanitized = array();
			$optGroup = $this->getOptionGroup( $opts );
			// loop through options
			foreach ( $this->OPTIONS[$optGroup]['options'] as $key => $value ) {
				// look for field in rawinput
				if ( array_key_exists( $key, $opts ) ) {
					// catch properties that are stored as arrays (dimensions, metrics, etc.)
					if ( is_array( $opts[$key] ) ) {
						// initialize key/values array
						$sanitized[$key] = array();
						// loop for fields stored as arrays
						foreach ( $opts[$key] as $k => $v ) {
							$sanitized[$key][$k] = trim( $v );
						}
					// otherwise it's a single value
					} else {
						$sanitized[$key] = trim( $opts[$key] );
					}
				// catch checkboxes that are not enabled (not submitted as part of rawinput array unless checked)
				} else if ( preg_match( "/enable/", $key ) ) {
					$sanitized[$key] = 'disabled';
				}
			} // end foreach options
			return $sanitized;
		} // end method sanitize_section
		
		// method for validation of options
		function validate_options( $rawinput ) {
			/*
			$sanitized = $this->sanitize( $rawinput );
			$optGroup = $this->getOptionGroup( $sanitized );
			foreach ( $sanitized as $key => $value ) {
				if ( preg_match( "/enable/", $key ) ) {
					if ( !wp_html_sitemap_Utilities::validCheckbox( $value ) ) {
						$sanitized[$key] =  $this->OPTIONS[$optGroup]['defaults'][$key];
					}
				} elseif ( preg_match( "/rank/", $key ) ) {
					if ( !in_array( $value, $this->rank_selections ) ) {
						$sanitized[$key] = $this->OPTIONS[$optGroup]['defaults'][$key];
					}
				} elseif ( preg_match( "/excluded/", $key ) ) {
					// @TODO validation check for page/post titles
				}
			}
			*/
			return $rawinput;
		} // end method validate_options()
	} // end class wp_html_sitemap_options
} // end if wp_html_sitemap_options class exists
?>