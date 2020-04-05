(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$( function() {
		function attach_vmat_volunteers_handlers() {
			// enable or disable bulk action button depending on
			// state of checkboxes
			$('#vmat_volunteers_table input:checkbox').on( 'load change', function() {
				if($('#vmat_volunteers_table .check-column input:checked').length > 0) {
					$('button[value="bulk_add_volunteers_to_event"]').prop('disabled', false);
				} else {
					$('button[value="bulk_add_volunteers_to_event"]').prop('disabled', true);
				}
		 	});
			$('#vmat_event_volunteers_table .check-column input:checkbox').on( 'load change', function() {
				if($('#vmat_event_volunteers_table .check-column input:checked').length > 0) {
					$('button[value^="bulk_event_volunteers_"]').prop('disabled', false);
				} else {
					$('button[value^="bulk_event_volunteers_"]').prop('disabled', true);
				}
		 	});
			
			$('#vmat_event_volunteers_table td.vmat-manage-column input:checkbox').on( 'load change', function() {
				var self = this;
				$('#vmat_event_volunteers_table .vmat-check-column input').prop('checked', $(self).prop('checked')).change();
		 	});
			
			// send selected volunteers to server and associate them with the event
			$('button[value="bulk_add_volunteers_to_event"]')
			.off('click', add_volunteers_to_event )
			.on( 'click', add_volunteers_to_event );
			// send single volunteer to server and associate with event
	        $('span.vmat-link[id^="vmat_selected_volunteer_"]')
	        .off( 'click', add_volunteer_to_event )
	        .on( 'click', add_volunteer_to_event );
	        // send selected volunteers to server and take the appropriate action
	        $('button[value^="bulk_event_volunteers_"]')
	        .off( 'click', take_action_for_event_volunteers )
	        .on( 'click', take_action_for_event_volunteers );
			// send single volunteer to server and take appropriate action
	        $('span.vmat-link[id^="vmat_selected_event_volunteer_"]')
	        .off( 'click', take_action_for_event_volunteer )
	        .on( 'click', take_action_for_event_volunteer );
	        // when changing inputs in the event volunteers table, change the look
	        $('.vmat-event-volunteer-input')
	        .off( 'change', add_input_changed_class )
	        .on( 'change', add_input_changed_class );
	        // paginate tables using ajax
	        $('span.vmat-ajax-paginate')
	        .off( 'click', paginate_tables )
	        .on( 'click', paginate_tables );
	        $('input.vmat-ajax-paginate')
	        .off( 'change', paginate_tables )
	        .on( 'change', paginate_tables );
	        // clear text field
	        $('.clearable-input > [data-clear-input]')
	        .off( 'click', clear_text_input )
	        .on( 'click', clear_text_input );
	        // clear vmat-notices when clicked anywhere
			$('span[vmat-notice-dismiss]')
			.off( 'click', clear_admin_notice )
			.on( 'click', clear_admin_notice );
		}
		
		function clear_text_input() {
			var self = this;
			$(self).closest('div.clearable-input').find('input[type="text"]').val('').change();
		}
		
		function add_input_changed_class() {
			$(this).addClass('vmat-event-volunteer-input-changed');
			// check the multiselect for this row for bulk save
			$(this).closest('tr').find('th input').prop('checked', true).change();
		}
		
		function remove_input_changed_class() {
			$(this).removeClass('vmat-event-volunteer-input-changed');
			// uncheck the multiselect for this row for bulk save
			$(this).closest('tr').find('th input').prop('checked', false).change();
		}
		
		function set_admin_notice_color( id='', color_class='vmat-notice-info' ) {
			$( id + ' .vmat-notice').addClass(color_class);
		}
		
		function show_admin_notice( notice_html ) {
			$('#vmat_admin_notice p').html( notice_html );
			$('#vmat_admin_notice .vmat-notice').css('visibility', 'visible');
			if( notice_html.indexOf('SUCCESS') > -1 ) {
					set_admin_notice_color( '#vmat_admin_notice', 'vmat-notice-success');
			} else if( notice_html.indexOf('INFO') > -1 ) {
				set_admin_notice_color( '#vmat_admin_notice', 'vmat-notice-info');
			} else if( notice_html.indexOf('WARNING') > -1 ) {
				set_admin_notice_color( '#vmat_admin_notice', 'vmat-notice-warning');
			} else if( notice_html.indexOf('ERROR') > -1 ) {
				set_admin_notice_color( '#vmat_admin_notice', 'vmat-notice-error');
				$('html, body').animate({scrollTop: '0px'}, 300);
			}
			
		}
		
		function show_ajax_notice( target = '', notice_html='' ) {
			$('#' + target + ' p').html(notice_html);
			if( notice_html.indexOf('SUCCESS') > -1 ) {
					set_admin_notice_color( '#' + target, 'vmat-notice-success');
			} else if( notice_html.indexOf('INFO') > -1 ) {
				set_admin_notice_color( '#' + target, 'vmat-notice-info');
			} else if( notice_html.indexOf('WARNING') > -1 ) {
				set_admin_notice_color( '#' + target, 'vmat-notice-warning');
			} else if( notice_html.indexOf('ERROR') > -1 ) {
				set_admin_notice_color( '#' + target, 'vmat-notice-error');
			}
			$('#' + target + ' .vmat-notice').css('visibility', 'visible');
		}
		
		function clear_admin_notice(){
			$('.vmat-notice').removeClass('vmat-notice-info');
			$('.vmat-notice').removeClass('vmat-notice-warning');
			$('.vmat-notice').removeClass('vmat-notice-error');
			$('.vmat-notice').removeClass('vmat-notice-success');
			$('.vmat-notice').css('visibility', 'hidden');
			$('.vmat-notice p').html( '&nbsp;' );
		}
		
		function create_error_notice( messages = Array() ) {
			var error = '';
			messages.forEach( function( msg ) {
				error = error + '<strong>ERROR</strong>: ' + msg + '<br />';
			}
			);
			error = error.slice( 0, -6 );
			return error;
		}
		
		function handle_failed_ajax_call( jqxhr, status, err ) {
			// handle a failed ajax call
			show_admin_notice( create_error_notice( Array( status + ' ' + err ) ) );
		}
		
		function handle_volunteers_action_for_event( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				var html = response.data.html;
				$('#' + response.data.target ).html(html);
				show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				$('tr').removeClass('vmat-action-in-progress');
				attach_vmat_volunteers_handlers();
			} else {
				// handle a wp_send_json_error response
				if( typeof( response ) != 'string' ) {
					show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				} else {
					show_ajax_notice( create_error_notice( response.data.notice_id, Array( response.data.ajax_notice ) ) );
				} 
				// if failure, remove row highlight
				$('tr').removeClass('vmat-action-in-progress');
			}
			$('html').removeClass('waiting');
		}
		
		function handle_set_default_event_volunteers_data( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				var volunteer_data = response.data.volunteer_data;
				var event_data = response.data.event_data;
				Object.keys( volunteer_data ).forEach( function (volunteer_id ) {
					Object.keys( volunteer_data[volunteer_id] ).forEach( function( item )  {
						switch( item ) {
						case '_hours_per_day':
							$(volunteer_data[volunteer_id][item].selector).val(event_data['hours_per_day']).change();
							break;
						case '_volunteer_start_date':
							$(volunteer_data[volunteer_id][item].selector).val(event_data['start_date']).change();
							break;
						case '_volunteer_num_days':
							$(volunteer_data[volunteer_id][item].selector).val(event_data['days']).change();
							break;
						case '_approved':
							$(volunteer_data[volunteer_id][item].selector).attr('checked', false).change();
							break;
						default:
						}
					});
				}) ;
				show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				$('tr').removeClass('vmat-action-in-progress');
				attach_vmat_volunteers_handlers();
			} else {
				// handle a wp_send_json_error response
				if( typeof( response ) != 'string' ) {
					show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				} else {
					show_ajax_notice( create_error_notice( response.data.notice_id, Array( response.data.ajax_notice ) ) );
				} 
				// if failure, remove row highlight
				$('tr').removeClass('vmat-action-in-progress');
			}
			$('html').removeClass('waiting');
		}
		
		function handle_failed_volunteers_action_for_event( jqxhr, status, error ) {
			// if failure, remove row highlight
			$('tr').removeClass('vmat-action-in-progress');
			$('html').removeClass('waiting');
		}
		
		function handle_paginate_action( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				var html = response.data.html;
				var target = response.data.target;
				$('#' + target).html(html);
				attach_vmat_volunteers_handlers();
			} else {
				// handle a wp_send_json_error response
				if( typeof( response ) != 'string' ) {
					show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				} else {
					show_ajax_notice( response.data.notice_id, create_error_notice( Array( response.data.ajax_notice ) ) );
				}
				// if failure, remove row highlight
				$('tr').removeClass('vmat-action-in-progress');
			}
			$('html').removeClass('waiting');
		}
		
		function get_bulk_volunteer_data( event_id=0 ) {
	        var volunteer_data = {};
	        $('form#volunteers-filter input[id^="vmat_event_volunteer_cb_"]:checked').each( 
	        	function(i,v) {
	        		var volunteer_id = $(v).prop('id').replace( 'vmat_event_volunteer_cb_', '' );
	        		var data = get_volunteer_data( event_id, volunteer_id );
        		    volunteer_data[volunteer_id] = data;
	        	}
	        );
	        return volunteer_data;
		}
		
		function get_volunteer_data( event_id=0, volunteer_id=0 ) {
			var data = {
					'_hours_per_day': {'selector': 'input#vmat_hours_per_day_' + event_id + '_' + volunteer_id},
					'_volunteer_start_date': {'selector': 'input#vmat_start_date_' + event_id + '_' + volunteer_id},
					'_volunteer_num_days' : {'selector': 'input#vmat_days_' + event_id + '_' + volunteer_id},
					'_approved' : {'selector': 'input#vmat_hours_approved_' + event_id + '_' + volunteer_id}
			};
		    Object.keys( data ).forEach( function( key ) {
		    	data[key].type = $(data[key].selector).attr('type');
		    	data[key].min = $(data[key].selector).attr('min');
		    	data[key].max = $(data[key].selector).attr('max');
		    	if( data[key].type == 'checkbox') {
		    		data[key].val = 0;
		    		if( $(data[key].selector).prop('checked') ) {
		    			data[key].val = 1;
		    		}
		    	} else {
		    		data[key].val = $(data[key].selector).val();
		    	}
		    });
		    return data;
		}
		
		function validate_inputs( data={}, items=[] ) {
			var errors = Array();
			Object.keys( data ).forEach( function( datum ) {
				Object.values( items ).forEach( function( item ) {
					var val = data[datum][item].val;
					var min = data[datum][item].min;
					var max = data[datum][item].max;
					var selector = data[datum][item].selector;
					
					switch( data[datum][item].type ) {
					case 'text':
						break;
					case 'number':
						var result = check_numeric_input( selector );
						if( ! result.success ) {
							var err = $( selector ).attr('data_name') + ' invalid value "' + data[datum][item].val + '". Type="' + data[datum][item].type + '"';
							if( ! ( min === undefined ) && ! ( max === undefined ) ) {
								err = err + ' valid range=[' + min + ' - ' + max + ']';
							}
							errors.push( err );
						}
						break;
					case 'date':
						var result = check_date_input( selector );
						if( ! result.success ) {
							var err = $( selector ).attr('data_name') + ' invalid value "' + data[datum][item].val + '". Type="' + data[datum][item].type + '"';
							if( ! ( min === undefined ) && ! ( max === undefined ) ) {
								err = err + ' valid range=[' + min + ' - ' + max + ']';
							}
							errors.push( err );
						}
						break;
						break;
					case 'checkbox':
						break;
					default:
						
					}
				}
				);
			} 
			); 
			return errors;
		}
		
		function check_numeric_input( element, change=false ) {
			var result = {};
			var val = $(element).val();
			var required = $(element).prop('required');
			result.success = true;
			if( ! isNaN( val ) && ! ( val === '' ) ) {
 				// numeric input
				var val = Number( val );
				var min_val = undefined;
				var max_val = undefined;
				if( $(element).attr("min") ) {
					min_val = Number( $(element).attr("min") );
				}
				if( $(element).attr("max") ) {
					max_val = Number( $(element).attr("max") );
				}
				if ( ! ( min_val === undefined ) && ! ( max_val === undefined ) ) {
					if( val < min_val ) {
						// out of range. reset to min_val
						if( change ) {
							$(element).val( min_val );
							val = min_val;
						}
						result.success = false;
					}
					if( val > max_val ) {
						// out of range. reset to min_val
						if( change ) {
							$(element).val( max_val );
							val = max_val;
						}
						result.success = false;
					}
				}				
			} else {
				if( ( required && val === '' ) || isNaN( val ) ) {
					// required and empty or not a number
					if( change ) {
						$(element).val( min_val );
						val = min_val;
					}
					result.success = false;
				}
			}
			result.val = val
			return result;
		}
		
		function is_date( input ) {
		  if ( Object.prototype.toString.call(input) === "[object Date]" ) {
			  return true;
		  }
		  return false;   
		};
		
		function check_date_input( element, change=false ) {
			// simple test, needs improvement
			var result = {};
			var val = $(element).val();
			var required = $(element).prop('required');
			result.success = true;
			if( ! is_date( new Date( val ) ) || ( required && ( val === '' ) ) ) {
				result.success = false;
			}
			result.val = val
			return result;
		}
		
		function add_volunteer_to_event() {                       //use in callback
	        var event_id = $('form#volunteers-filter input[name="event_id"]').val();
	        var volunteer_id = $(this).prop('id').replace('vmat_selected_volunteer_','');
	        $( this ).closest('tr').addClass('vmat-action-in-progress');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_add_volunteers_to_event",
					volunteer_ids: Array( volunteer_id ),
					event_id: event_id,
					notice_id: 'event_volunteers_added_status',
					target: 'vmat_manage_volunteers_admin',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_added_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteer_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function add_volunteers_to_event() {  
	        var self = this;                      //use in callback
	        var event_id = $('form#volunteers-filter input[name="event_id"]').val();
	        var volunteer_ids = Array();
	        $('form#volunteers-filter input[id^="vmat_volunteer_cb_"]:checked').each( function(i,v)
	        		{
	        		    volunteer_ids.push($(v).prop('id').replace( 'vmat_volunteer_cb_', '' ));
	        		});
	        $('form#volunteers-filter input[id^="vmat_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_add_volunteers_to_event",
					volunteer_ids: volunteer_ids,
					event_id: event_id,
					notice_id: 'event_volunteers_added_status',
					target: 'vmat_manage_volunteers_admin',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_added_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function take_action_for_event_volunteers() {
			var self = this;
			var event_id = $('form#volunteers-filter input[name="event_id"]').val();
	        var volunteer_data = get_bulk_volunteer_data( event_id );
	        $('form#volunteers-filter input[id^="vmat_event_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
			var action = $(self).attr('value').replace( 'bulk_event_volunteers_', '');
			switch ( action ) {
			case 'remove':
				remove_volunteers_from_event( event_id, volunteer_data );
				break;
			case 'save':
				save_event_volunteers_data( event_id, volunteer_data );
				break;
			}
		}
		
		function take_action_for_event_volunteer() {
			var event_id = $('form#volunteers-filter input[name="event_id"]').val();
			var action = $(this).attr('data_action');
			var volunteer_id = $(this).attr('volunteer_id');
			var volunteer_data = {};
	        volunteer_data[volunteer_id] = get_volunteer_data( event_id, volunteer_id );
	        $(this).closest('tr').addClass('vmat-action-in-progress');
			switch ( action ) {
			case 'remove':
				remove_volunteers_from_event( event_id, volunteer_data );
				break;
			case 'save':
				save_event_volunteers_data( event_id, volunteer_data );
				break;
			case 'default':
				set_default_event_volunteers_data( event_id, volunteer_data );
				break;
			}
		}
		
		function remove_volunteers_from_event( event_id=0, volunteer_data={} ) {  
	        var self = this;                      //use in callback
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_remove_volunteers_from_event',
					volunteer_data: volunteer_data,
					event_id: event_id,
					notice_id: 'event_volunteers_updated_status',
					target: 'vmat_manage_volunteers_admin',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_updated_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function save_event_volunteers_data( event_id=0, volunteer_data={} ) {  
	        var self = this;                      //use in callback
	        $('html').addClass('waiting');
	        var items_to_validate = [
	        	'_hours_per_day',
	        	'_volunteer_start_date',
	        	'_volunteer_num_days',
	        	'_approved'
	        ];
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_updated_status', 'working....' );
	        var messages = validate_inputs(volunteer_data, items_to_validate );
	        if( messages.length == 0 ) {
	        	var request = {
						_ajax_nonce: my_ajax_obj.nonce,
						action: 'ajax_save_event_volunteers_data',
						volunteer_data: volunteer_data,
						event_id: event_id,
						notice_id: 'event_volunteers_updated_status',
						target: 'vmat_event_volunteers_table'
					};
		        $.post( my_ajax_obj.ajax_url, request )
		        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
		        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
		        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	        } else {
	        	var notice_html = create_error_notice( messages );
				show_ajax_notice( 'event_volunteers_updated_status', notice_html );
	        	handle_failed_volunteers_action_for_event();
	        }
		}
		
		function set_default_event_volunteers_data( event_id=0, volunteer_data={} ) {  
	        var self = this;                      //use in callback
	        $('html').addClass('waiting');
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_updated_status', 'working....' );
        	var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_set_default_event_volunteers_data',
					volunteer_data: volunteer_data,
					event_id: event_id,
					notice_id: 'event_volunteers_updated_status',
					target: 'vmat_event_volunteers_table'
				};
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_set_default_event_volunteers_data ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function paginate_tables() {
			var self = this;
			var data = {};
			$(self).attr('ajax_data_attributes').split(',')
			.forEach(function(value) {
				var key_value = value.split(':');
				data[key_value[0]] = key_value[1];
				}
			);
			if( $(self).is('input') ) {
				var check = check_numeric_input( self, true );
				data[data['page_name']] = check.val;
			}
			switch( data['admin_page'] ) {
			case 'vmat_admin_hours':
				$('html').addClass('waiting');
		        var request = {
						_ajax_nonce: my_ajax_obj.nonce,
						action: 'ajax_paginate_vmat_admin_hours',
						data: data,
					};
		        clear_admin_notice();
		        $.post( my_ajax_obj.ajax_url, request )
		        .done( handle_paginate_action ) // handle any successful wp_send_json_success/error
		        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
				break;
			}
		}
		
		
        attach_vmat_volunteers_handlers();
	});

})( jQuery );
