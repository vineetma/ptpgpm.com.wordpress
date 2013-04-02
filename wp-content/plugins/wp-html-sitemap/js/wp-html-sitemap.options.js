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
	
	This is the WP HTML Sitemap options javscript file - wp-html-sitemap.options.js
	
*/

// initialize global variable for php options array
if ( typeof( phpOptionsArray ) === 'undefined' ) {
	// replace escaped characters in json string
	var js_json_str = phpOPTIONS.json_str.replace(/&quot;/g, '"');
	// load with php options array
	if ( wp_html_sitemap_FunctionExists( 'parseJSON', 'jQuery' ) ) {
		phpOptionsArray = jQuery.parseJSON( js_json_str );
	} else {
		wp_html_sitemap_BrowserIssueAlert( 'jQuery.parseJSON' );
	}
}

/**
 * wp_html_sitemap_BrowserIssueAlert
 * 
 * function to send browser support issue alert to UI
 * 
 * inputs:
 * 
 * missingFunction		name of missing function
 * 
 * outputs:
 * 
 * none
 * 
 */
function wp_html_sitemap_BrowserIssueAlert( missingFunction ) {
	alert( 'Browser compatibility issue: ' + missingFunction + ' does not exist.' );
} // end function wp_html_sitemap_BrowserIssueAlert

/**
 * 
 * @param format
 * @param list
 * @param default_value
 * @returns
 */
function wp_html_sitemap_CheckFormat( format, list, default_value ) {
	
	if ( format == null || format == '' ) {
		return default_value;
	} 
	for ( item in list ) {
		if ( list[item] == format || item == format ) {
			return format;
		}
	}
	return default_value;
}

/**
 * wp_html_sitemap_FormReset
 * 
 * function to reset options form fields
 * 
 * inputs:
 * 
 * 		myForm string - target form element id
 * 
 * outputs:
 * 
 * 		none
 * 
 */
function wp_html_sitemap_FormReset( myForm ) {
	myForm.reset();
	// get current URL
	var doc = document.location.href;
	// grab tab name from URL
	if ( doc.match( /tab=general/ ) ) {
		tab = 'general';
	} else if ( doc.match( /tab=sections/ ) ) {
		tab = 'sections';
	} else {
		tab = 'general';
	}
	if ( tab == 'general' ) {
		wp_html_sitemap_UpdateTemplateField();
	}
} // end function wp_html_sitemap_FormReset

/**
 * wp_html_sitemap_FunctionExists
 * 
 * function to verify existence of js or jQuery function
 * 
 * inputs:
 * 
 * functionName		name of function to be checked
 * type				either js or jQuery
 * 
 * outputs:
 * 
 * 0 if false (does not exist), otherwise true (exists)
 * 
 */
function wp_html_sitemap_FunctionExists( functionName, type ) {
	switch ( type ) {
		case 'jQuery':
			// jQuery function exists test
			if ( functionName in jQuery ) {
				return true;
			} else {
				return false;
			}
			break;
		case 'js':
			// javascript function exists test
			break;
	}
} // end function googlyzer_FunctionExists

function wp_html_sitemap_MoveItemUp( element_id ) {
	jQuery('#' + element_id + ' option:selected').each(function(){
		jQuery(this).insertBefore(jQuery(this).prev());
	  	});
}

function wp_html_sitemap_MoveItemDown( element_id ) {
	jQuery('#' + element_id + ' option:selected').each(function(){
		jQuery(this).insertAfter(jQuery(this).next());
	  	});
}

function wp_html_sitemap_IsArray( obj ) {
    return Object.prototype.toString.call( obj ) === '[object Array]';
}

/**
 * wp_html_sitemap_RestoreDefaults
 * 
 * function to restore default values to wp_html_sitemap options page
 * 
 * inputs:
 * 
 * 		none
 * 
 * outputs:
 * 
 * 		none
 * 
 */
function wp_html_sitemap_RestoreDefaults() {
	// get current URL
	var doc = document.location.href;
	// grab tab name from URL
	if ( doc.match( /tab=general/ ) ) {
		tab = 'general';
	} else if ( doc.match( /tab=sections/ ) ) {
		tab = 'sections';
	} else {
		tab = 'general';
	}
	// switch statement for different tabs
	switch ( tab ) {
		case 'general' :
			// loop for each section on tab
			for ( var field in phpOptionsArray['wp-html-sitemap-general']['defaults'] ) {
				myDefault = phpOptionsArray['wp-html-sitemap-general']['defaults'][field];
				var f = document.getElementById( field );
				if ( field != 'page_id' ) {
					f.value = myDefault;
				}
				if ( f.type === 'dropdown' ) {
					jQuery("#" + field + " option[value='" + myDefault + "']").attr("selected", "selected");
				} else if ( f.type === 'checkbox' && myDefault === 'enabled' ) {
					f.checked = 'checked';
				} else {
					f.checked = '';
				}
			}
			wp_html_sitemap_UpdateTemplateField();
			break;
		case 'sections' :
			// loop for each section on tab
			for ( var field in phpOptionsArray['wp-html-sitemap-sections']['defaults'] ) {
				myDefault = phpOptionsArray['wp-html-sitemap-sections']['defaults'][field];
				if ( wp_html_sitemap_IsArray( myDefault ) ) {
					var select  = jQuery('#' + field);
					select.empty();
					jQuery.each(myDefault, function(a, b){
				        select.append("<option>" + b + "</option>");
				     	});
				} else {
					var f = document.getElementById( field );
					f.value = myDefault;
					if ( f.type === 'checkbox' && myDefault === 'enabled' ) {
						f.checked = 'checked';
					}
				}
			}
			break;
		case 'pages':
			break;
		case 'posts':
			break;
	}
} // end function wp_html_sitemap_RestoreDefaults

/**
 * wp_sitemap_html_ToggleElements
 * 
 * updates html element values based on id
 * 
 * inputs:
 * 
 * 		myArray			standard array of html element id's to be disabled/enabled
 * 
 * outputs:
 * 
 * 		none
 * 
 */
function wp_html_sitemap_ToggleElements( change, myArray ) {
	// loop for each element in list
	for ( var key in myArray ) {
		// grab element by id
		var e = document.getElementById( myArray[key] );
		// if element exists
		if ( typeof e !== 'undefined' ) {
			if ( change === 'enable' ) {
				e.disabled = false;
			} else {
				e.disabled = true;
			}
		}
	}
}

/**
 * wp_html_sitemap_UpdateElements
 * 
 * updates html element values based on id
 * 
 * inputs:
 * 
 * 		myArray			associative array of html element id's (keys) and new values
 * 
 * outputs:
 * 
 * 		none
 * 
 */
function wp_html_sitemap_UpdateElements( myArray ) {
	// loop for each element in list
	for ( var key in myArray ) {
		// grab element by id
		var e = document.getElementById( key );
		// if element exists
		if ( typeof e !== 'undefined' ) {
			// if it's a checkbox
			if ( key.match( /enable/ ) ) {
				// update checkbox
				e.checked = myArray[key];
			// otherwise it's a standard input field
			} else {
				// update value
				e.value = myArray[key];
			}
		}
	}
}

function wp_html_sitemap_UpdateList( list, selections, target ) {
	// first remove all options from dropdown list
	while ( list.options.length ) list.options[0] = null;
	// then refill dropdown list with new items
	jQuery.each( selections, function( key, value ) {
		var oOption = document.createElement( 'OPTION' );
		list.options.add( oOption );
		oOption.innerHTML = value;
		oOption.value = key;
		// set selected if matches desired selection
		});
	// set selected option to default initially
	for ( var i=0; i<list.options.length; i++ ) {
		if ( list.options[i].text == target || list.options[i].value == target ) {
			list.options[i].selected = true;
		}
	}
}

function wp_html_sitemap_UpdateSitemapForm() {
	// check to see if page_id field contains an id
	var page_id = document.getElementById( 'page_id' );
	// if it does, update value of main button, and
	if ( page_id.value ) {
		wp_html_sitemap_UpdateElements( { 
			'createSitemap':'Update Sitemap', 
			} );
		wp_html_sitemap_ToggleElements( 'enable', new Array( 'deleteSitemap' ) );
	} else {
		wp_html_sitemap_UpdateElements( { 
			'createSitemap':'Create Sitemap', 
			} );
		wp_html_sitemap_ToggleElements( 'disable', new Array( 'deleteSitemap' ) );
	}
	wp_html_sitemap_UpdateTemplateField();
}

function wp_html_sitemap_UpdateTemplateField() {
	var template = document.getElementById( 'template' );
	var type = document.getElementById( 'type' );
	var table_cell = template.parentNode;
	var table_row = table_cell.parentNode;
	var th_labels = table_row.getElementsByTagName( "th" );
	var target = phpOptionsArray['wp-html-sitemap-general']['options']['template'];
	// update page template / post format label
	for ( var i=0; i<th_labels.length; i++ ) {
		if ( type.options[type.selectedIndex].value == 'post' ) {
			th_labels[i].innerHTML = 'Post Format';
			target = wp_html_sitemap_CheckFormat( target, phpOptionsArray['post_formats'], "Standard" );
			wp_html_sitemap_UpdateList( template, phpOptionsArray['post_formats'], target );
		} else {
			th_labels[i].innerHTML = 'Page Template';
			target = wp_html_sitemap_CheckFormat( target, phpOptionsArray['page_templates'], "Default" );
			wp_html_sitemap_UpdateList( template, phpOptionsArray['page_templates'], target );
		}
	}
}