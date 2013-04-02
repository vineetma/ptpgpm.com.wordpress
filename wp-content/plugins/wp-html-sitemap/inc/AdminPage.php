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
	
	This is the wp_html_sitemap_AdminPage PHP class file.
	
 */

if ( !class_exists( 'wp_html_sitemap_AdminPage' ) ) {

	/** wp_html_sitemap_AdminPage class
	 *
	 * PHP class for creating WP HTML Sitemap Admin/Options page.
	 * @author wmsedgar
	 *
	 */
	class wp_html_sitemap_AdminPage {
		/* public variables */
		
		/* private variables */
		private $button_ids = 			array();
		private $createSitemapButton = 	'createSitemap';
		private $deleteSitemapButton = 	'deleteSitemap';
		private $form_ids = 			array(
			'categories' =>				'wp_html_sitemap_categories',
			'forums' =>					'wp_html_sitemap_forums',
			'general' =>				'wp_html_sitemap_general',
			'sections' => 				'wp_html_sitemap_sections',
			'pages' => 					'wp_html_sitemap_pages',
			'posts' =>					'wp_html_sitemap_posts',
			'products' =>				'wp_html_sitemap_products',
			'topics' =>					'wp_html_sitemap_topics'
			);
		private $options = 				array(
			'categories' =>				array(),
			'forums' =>					array(),
			'general' =>				array(),
			'sections' =>				array(),
			'pages' =>					array(),
			'posts' =>					array(),
			'products' =>				array(),
			'topics' =>					array()
			);
		private $options_ids = 			array(
			'categories' =>				'wp-html-sitemap-categories',
			'forums' =>					'wp-html-sitemap-forums',
			'general' =>				'wp-html-sitemap-general',
			'sections' =>				'wp-html-sitemap-sections',
			'pages' =>					'wp-html-sitemap-pages',
			'posts' =>					'wp-html-sitemap-posts',
			'products' =>				'wp-html-sitemap-products',
			'topics' =>					'wp-html-sitemap-topics'
			);
		private $page_id = 				null;
		private $parentInstance = 		null;
		private $stars = 				null;
		
		/**
		 * old style constructor method for backward PHP compatibility
		 */ 
		public function wp_html_sitemap_AdminPage( $parentInstance ) {
			$this->__construct( $parentInstance );
		}
		
		/**
		 * public constructor method
		 */
		public function __construct( $parentInstance ) {
			$this->parentInstance = $parentInstance;
			$this->setButtonIds();
			$this->formCheck();	
			$this->header();
			$this->body();
		}
		
		/*
		 * private functions
		 */
		
		private function body() {
			$tab = $this->getCurrentTab();
			// display input sections and fields based on current tab
			switch ( $tab ) {
				case 'about':
					$this->outputTab( array( 'name'=>'about', 'html'=>'ABOUT', 'form'=>false ) );
					break;
				case 'authors':
					$this->outputTab( array( 'name'=>'authors', 'html'=>'AUTHOR', 'form'=>false ) );
					break;
				case 'categories':
					$this->outputTab( array( 'name'=>'categories', 'html'=>'CATEGORY', 'form'=>true ) );
					break;
				case 'forums':
					$this->outputTab( array( 'name'=>'forums', 'html'=>'FORUM', 'form'=>true ) );
					break;
				case 'general':
					$this->generalOptions();
					break;
				case 'pages':
					$this->outputTab( array( 'name'=>'pages', 'html'=>'PAGE', 'form'=>true ) );
					break;
				case 'posts':
					$this->outputTab( array( 'name'=>'posts', 'html'=>'POST', 'form'=>true ) );
					break;
				case 'products':
					$this->outputTab( array( 'name'=>'products', 'html'=>'PRODUCT', 'form'=>true ) );
					break;
				case 'sections':
					$this->sectionOptions();
					break;
				case 'topics':
					$this->outputTab( array( 'name'=>'topics', 'html'=>'TOPIC', 'form'=>true ) );
					break;
			}
			echo "</div>";
		}
		
		private function createSitemapForm() {
			?>
			<div id="form_container">
				<p><strong>NOTE:</strong> Make sure to save any settings updates prior to creating or updating your sitemap below.</p>
				<form id="create_sitemap_form" action="options-general.php?page=wp-html-sitemap&tab=general" method="post">
					<p class='submit'>
						<input 
							id="<?php echo $this->createSitemapButton; ?>"
							name="<?php echo $this->createSitemapButton; ?>"
							type="submit" 
							class="button-primary" 
							value="<?php esc_attr_e( 'Create Sitemap', 'wp-html-sitemap' ); ?>" 
						/>
						<input 
							id="<?php echo $this->deleteSitemapButton; ?>"
							name="<?php echo $this->deleteSitemapButton; ?>" 
							type="submit" 
							class="button-secondary" 
							value="<?php esc_attr_e( 'Delete Sitemap', 'wp-html-sitemap' ); ?>"
						/>
					</p>
				</form>
				<script type="text/javascript">
					// set createSitemapButton id as appropriate
					jQuery(document).ready(function() {
						//toggle the component with class msg_body
						wp_html_sitemap_UpdateSitemapForm();
						});
				</script>
			</div>
			<?php
		}
		
		private function deleteSitemap(){
			$page_id = $this->parentInstance->OPTIONS['wp-html-sitemap-general']['options']['page_id'];
			if ( !empty( $page_id ) ) {
				if ( wp_delete_post( $postid = $page_id, $force_delete = true ) ) {
					$this->parentInstance->OPTIONS['wp-html-sitemap-general']['options']['page_id'] = '';
					// update options array
					update_option( 
						$option_name = 'wp-html-sitemap-general', 
						$newvalue = $this->parentInstance->OPTIONS['wp-html-sitemap-general']['options'] 
						);
					echo '<div id="message" class="updated fade"><p><strong>Sitemap deleted.</strong></p></div>';
				}
			}
		}
		
		private function formCheck() {
			$tab = $this->getCurrentTab();
			if ( preg_match( "/general/", $tab ) || preg_match( "/section/", $tab ) ) {
				// check whether there are already options in the database
				if ( !$this->options[$tab] = get_option( $this->options_ids[$tab] ) ) {
					// otherwise set to defaults
					$this->options[$tab] = $this->parentInstance->OPTIONS[$this->options_ids[$tab]]['defaults'];
				}
				// check whether form was just submitted
				if ( isset( $_POST[$this->button_ids[$tab . '_save']] ) ) {
					// grab value sent for each field
					foreach ( $this->options[$tab] as $key => $value ) {
						if ( array_key_exists( $key, $_POST ) ) {
							$this->options[$tab][$key] = $_POST[$key];
							$this->parentInstance->OPTIONS[$this->options_ids[$tab]]['options'][$key] = $_POST[$key];
						}
					}
					// update options array
					update_option( $this->options_ids[$tab], $this->options[$tab] );
					echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
				} elseif ( isset( $_POST[$this->createSitemapButton] ) ) {
					try {
						if ( class_exists( 'wp_html_sitemap_map' ) ) {
							$map = new wp_html_sitemap_map( $parentInstance = $this );
							$this->page_id = $map->getID();
							if ( !empty( $this->page_id ) ) {
								$this->parentInstance->OPTIONS['wp-html-sitemap-general']['options']['page_id'] = $this->page_id;
								update_option( 
									$option_name = 'wp-html-sitemap-general',
									$newvalue = $this->parentInstance->OPTIONS['wp-html-sitemap-general']['options']
									);
							}
							if ( $_POST[$this->createSitemapButton] == 'Create Sitemap' ) {
								echo '<div id="message" class="updated fade"><p><strong>Sitemap created.</strong></p></div>';
							} else {
								echo '<div id="message" class="updated fade"><p><strong>Sitemap updated.</strong></p></div>';
							}
						} else {
							// failed to load wp_html_sitemap_map class
							throw new wp_html_sitemap_Exception( 'wp_html_sitemap_map class not loaded.' );
						}
					} catch ( wp_html_sitemap_Exception $e ) {
						echo $e->getError();
					}
				} elseif ( isset( $_POST[$this->deleteSitemapButton] ) ) {
					try {
						$this->deleteSitemap();
					} catch ( wp_html_sitemap_Exception $e ) {
						echo $e->getError();
					}
				}
			}
		}
		
		/**
		 *
		 * returns current tab
		 */
		private function getCurrentTab() {
			// return current tab, or general as default
			return ( isset ( $_GET['tab'] ) ? $_GET['tab'] : 'general' );
		}
		
		private function loadTabs() {
			// check to see what tab we're on
			if (isset ( $_GET['tab'])) :
				$current = $_GET['tab'];
			else :
				$current = 'section';
			endif;
			$links = array();
			foreach ( $this->parentInstance->TABS as $tab => $name ) {
				if ($tab == $current) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=wp-html-sitemap&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=wp-html-sitemap&tab=$tab'>$name</a>";
				endif;
			}
			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $links as $link ) {
				echo $link;
			}
			echo '</h2>';
		}
		
		/**
		 * 
		 * output header section of admin page body
		 */
		private function header(){
			?>
				<script type="text/javascript">
					// jquery for collapsing options areas
					jQuery(document).ready(function() {
						//toggle the component with class msg_body
						jQuery(".head_panel").click(function() {
					    	jQuery(this).next(".content_panel").slideToggle(300);
							});
						});
				</script>
				<div class="wrap">
					<div id="icon-plugins" class="icon32"></div>
						<h2>WP HTML Sitemap Administration</h2>
						WP HTML Sitemap v<?php echo WP_HTML_SITEMAP_VERSION; ?> configuration options.
			<?php
			$this->loadTabs();
		}
		
		/**
		 * 
		 * output html input, return true/false for success/failure
		 * @param $target = external file with html for output
		 * @param $replacement_array = array containing items to be searched/replaced
		 * @param $script = string output for script to be appended to html
		 * @throws wp_html_sitemap_Exception
		 */
		private function outputHtml( $target, $replacement_array = array(), $script = '' ) {
			// output html for about page
			if ( class_exists( 'wp_html_sitemap_Utilities' ) ) {
				try {
					echo wp_html_sitemap_Utilities::getFilteredHtml( $target, $replacement_array, $script );
					echo $script;
				} catch ( wp_html_sitemap_Exception $e ) {
					echo $e->getError();
				}
			} else {
				throw new wp_html_sitemap_Exception( 'wp_html_sitemap_Utilities class not loaded.' );
			}
		}
		
		private function outputSectionsForm() {
			?>
			
			<form id="<?php echo $this->form_ids['sections']; ?>" action="options-general.php?page=wp-html-sitemap&tab=sections" method="post">
				<div>
					<table id="duallistbox">
						<tr>
							<td>
								<div id="filter">
									Filter<br /><input type="text" id="box1Filter" /><button type="button" class="button-secondary" id="box1Clear">Clear</button><br />
								</div>
								<div id="sections">
									<p>Displayed<p>
										<select id="sections_displayed" name="sections_displayed[]" multiple="multiple">
										<?php
											foreach ( $this->options['sections']['sections_displayed'] as $key => $section) {
												echo '<option value="' . $section . '">' . $section . '</option>';
											}
										?>
						                </select><br />
						                <span id="box1Counter" class="countLabel"></span>
						            </div><br/>
					                <select id="box1Storage">
					                </select>
					            </td>
					            <td>
					                <div id="select_buttons">
						                <button id="to2" class="button-secondary" type="button">&nbsp;&gt;&nbsp;</button><button id="allTo2" class="button-secondary" type="button">&nbsp;&gt;&gt;&nbsp;</button><button id="allTo1" class="button-secondary" type="button">&nbsp;&lt;&lt;&nbsp;</button><button id="to1" class="button-secondary" type="button">&nbsp;&lt;&nbsp;</button>
						            </div>
						            <div id="order_buttons">
						            	<button id="moveup" class="button-secondary" type="button" onclick="wp_html_sitemap_MoveItemUp('sections_displayed')">&nbsp;&and;&nbsp;</button><button id="movedown" class="button-secondary" type="button" onclick="wp_html_sitemap_MoveItemDown('sections_displayed')">&nbsp;&or;&nbsp;</button>
						            </div>
					            </td>
					            <td>
					            	<div id="filter">
					                	Filter<br /><input type="text" id="box2Filter" /><button type="button" class="button-secondary" id="box2Clear">Clear</button><br />
					                </div>
					                <div id="sections">
						                <p>Excluded<p>
						                <select id="sections_excluded" name="sections_excluded[]" multiple="multiple">
						                <?php 
						                foreach ( $this->options['sections']['sections_excluded'] as $key => $section ) {
						                	echo '<option value="' . $section . '">' . $section . '</option>';
						                }					                
						                ?>
						                </select><br />
						                <span id="box2Counter" class="countLabel"></span>
						            </div><br/>
					                <select id="box2Storage">
					                </select>
					            </td>
					        </tr>
					    </table>
					   	<p><strong>NOTE:</strong> Order buttons ( &and; and &or; ) only affect <em>Displayed</em> field.</p>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function() { 
							// set up dual list boxes
							myBoxes = new dualListBox();
							myBoxes.configureBoxes( { 'box1View':'sections_displayed', 'box2View':'sections_excluded', 'useSorting':false } );
							});
					</script>
							
					<?php
					// create the buttons
					$this->settingButtons( 'sections' );
					?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
							<?php
		}
		
		private function outputGeneralSettingsForm() {
			settings_fields( $option_group = 'wp-html-sitemap-general' );
			/*
			 * @todo figure out how to pass additional params to output buffer callback
			* for string search and replace
			*/
			ob_start( 'wp_html_sitemap_Utilities::bufferFilter' );
			do_settings_sections( $page = 'wp-html-sitemap-general' );
			ob_end_flush();
			// create the buttons
			$this->settingButtons( 'general' );
			?> </form></div> <?php
			$this->createSitemapForm();
			?> </div></div></div></div> <?php
		}
		
		private function generalOptions() {
			try {
				// output html
				$this->starsHtml();
				$this->outputHtml( WP_HTML_SITEMAP_GENERAL_SETTINGS_HTML,
					$replacement_array = array( '[INSERT_RATING_STARS]' => $this->stars )
					);
				// output html for general settings page
				$this->outputHtml( WP_HTML_SITEMAP_GENERAL_SETTINGS_END_HTML );
				// output general settings form
				$this->outputGeneralSettingsForm();
			} catch ( wp_html_sitemap_Exception $e ) {
				echo $e->getError();
			}
		}
		
		private function outputForm( $args ) {
			?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php
		}
		
		private function outputTab( $args ) {
			try {
				// output html
				$this->outputHtml( constant( 'WP_HTML_SITEMAP_' . $args['html'] . '_SETTINGS_HTML' ) );
				if ( $args['form'] ) {
					$this->outputForm( array( 'name' => $args['name'] ) );
				}
			} catch ( wp_html_sitemap_Exception $e ) {
				echo $e->getError();
			}
		}
		
		private function sectionOptions() {
			try {
				// output html
				$this->outputHtml( WP_HTML_SITEMAP_SECTION_SETTINGS_HTML );
				// output sections form
				$this->outputSectionsForm();
			} catch ( wp_html_sitemap_Exception $e ) {
				echo $e->getError();
			}
		}
		
		private function setButtonIds() {
			$this->button_ids = 			array(
				'general_save' => 			$this->options_ids['general'] . '-save',
				'general_reset' => 			$this->options_ids['general'] . '-reset',
				'general_defaults' => 		$this->options_ids['general'] . '-defaults',
				'sections_save' => 			$this->options_ids['sections'] . '-save',
				'sections_reset' => 		$this->options_ids['sections'] . '-reset',
				'sections_defaults' => 		$this->options_ids['sections'] . '-defaults',
				'pages_save' => 			$this->options_ids['pages'] . '-save',
				'pages_reset' => 			$this->options_ids['pages'] . '-reset',
				'pages_defaults' => 		$this->options_ids['pages'] . '-defaults',
				'posts_save' => 			$this->options_ids['posts'] . '-save',
				'posts_reset' => 			$this->options_ids['posts'] . '-reset',
				'posts_defaults' => 		$this->options_ids['posts'] . '-defaults',
				);
		}
		
		private function settingButtons( $tab ) {
			?>
				<p class='submit'>
					<input 
						id="<?php echo $this->button_ids[$tab . '_save']; ?>"
						name="<?php echo $this->button_ids[$tab . '_save']; ?>"
						type="submit" 
						class="button-primary" 
						value="<?php esc_attr_e( 'Save Settings', 'wp-html-sitemap' ); ?>" 
					/>
					<input 
						id="<?php echo $this->button_ids[$tab . '_reset']; ?>"
						name="<?php echo $this->button_ids[$tab . '_reset']; ?>" 
						type="button" 
						class="button-secondary" 
						value="<?php esc_attr_e( 'Reset', 'wp-html-sitemap' ); ?>"
						onclick="wp_html_sitemap_FormReset(this.form)"
					/>
					<input 
						id="<?php echo $this->button_ids[$tab . '_defaults']; ?>"
						name="<?php echo $this->button_ids[$tab . '_defaults']; ?>"
						type="button" 
						class="button-secondary" 
						value="<?php esc_attr_e( 'Restore Defaults', 'wp-html-sitemap' ); ?>" 
						onclick="wp_html_sitemap_RestoreDefaults()"
					/>
				</p>
			<?php
		}
		
		private function starsHtml() {
			$stars_html = "";
			for ($i=0; $i<5; $i++) {
				$stars_html .= "<img src='"
				. WP_HTML_SITEMAP_IMAGES
				. "star_rating_small.png'>";
			}
			$this->stars = $stars_html;
		}
		
	} // end class wp_html_sitemap_AdminPage
	
} // end if class exists