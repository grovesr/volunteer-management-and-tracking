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
	$(window).load( function() {
		function check_if_save_needed( container='body') {
			var save_needed = false;
			if ( $( container + ' ' + '.vmat-check-before-save-changed').length > 0 ) {
				var message = 'Unsaved data! <br />"OK" to proceed and lose any changes.';
				$('#vmat_ok_cancel_modal .modal-body').html(message);
	        	$('.waiting').removeClass('waiting');
	        	$('#vmat_ok_cancel_modal').modal('show');
	        	save_needed = true;
			}
			return save_needed;
		}
		
		function check_before_unload() {
			return 'Unsaved data!';
		}
		
		function attach_vmat_general_handlers() {
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
		function attach_vmat_events_handlers() {
			attach_vmat_general_handlers();
			// do a filter on the events table
			$('button[value="filter_events"], button[value="search_events"]')
			.off( 'click', filter_events )
			.on( 'click', filter_events );
		}
		function attach_vmat_manage_volunteers_handlers() {
			attach_vmat_general_handlers();
			// enable bulk operation buttons
			$('#vmat_manage_volunteer_table .check-column input:checkbox').on( 'load change', function() {
				if($('#vmat_manage_volunteer_table .check-column input:checked').length > 0) {
					$('button[value^="bulk_hours_"]').prop('disabled', false);
				} else {
					$('button[value^="bulk_hours_"]').prop('disabled', true);
				}
		 	});
			$('#vmat_manage_volunteers_table .check-column input:checkbox').on( 'load change', function() {
				if($('#vmat_manage_volunteers_table .check-column input:checked').length > 0) {
					$('button[value="bulk_remove_volunteers"]').prop('disabled', false);
				} else {
					$('button[value="bulk_remove_volunteers"]').prop('disabled', true);
				}
		 	});
			// do a filter on the volunteers table
			$('button[value="filter_manage_volunteers"], button[value="search_manage_volunteers"]')
			.off( 'click', filter_manage_volunteers )
			.on( 'click', filter_manage_volunteers );
			// register a new volunteer and associate with the event
			$('button[value="show_update_volunteer_form"]')
			.off( 'click', show_update_volunteer_form )
			.on( 'click', show_update_volunteer_form );
			// cancel volunteer registration
			$('button[value="cancel_volunteer_update"]')
			.off( 'click', hide_update_volunteer_form )
			.on( 'click', hide_update_volunteer_form );
			$('button[value="update_volunteer"]')
			.off( 'click', update_volunteer )
			.on( 'click', update_volunteer );
			// open volunteer editor from volunteer management display
	        $('span.vmat-link[id^="show_update_volunteer_form"]')
	        .off( 'click', show_update_volunteer_form )
	        .on( 'click', show_update_volunteer_form );
	        $('#vmat_volunteer_table td.vmat-manage-column input:checkbox').on( 'load change', function() {
				var self = this;
				$('#vmat_volunteer_table .vmat-check-column input').prop('checked', $(self).prop('checked')).change();
		 	});
	        // send selected volunteers to server and take the appropriate action
	        $('button[value^="bulk_hours_"]')
	        .off( 'click', take_action_for_volunteer_hours )
	        .on( 'click', take_action_for_volunteer_hours );
			// send single volunteer to server and take appropriate action
	        $('span.vmat-link[id^="vmat_selected_hour_"]')
	        .off( 'click', take_action_for_volunteer_hour )
	        .on( 'click', take_action_for_volunteer_hour );
	        // remove selected volunteers
	        $('button[value^="bulk_remove_volunteers"]')
	        .off( 'click', remove_volunteers )
	        .on( 'click', remove_volunteers );
	        // associate event bookings with volunteers
	        $('button[value="associate_event_bookings_with_volunteers"]')
	        .off( 'click', associate_event_bookings_with_volunteers )
	        .on( 'click', associate_event_bookings_with_volunteers );
		}
		function attach_vmat_hours_volunteers_handlers() {
			attach_vmat_general_handlers();
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
			// do a search filter on the volunteers table
			$('button[value="search_volunteers"]')
			.off( 'click', search_volunteers )
			.on( 'click', search_volunteers );
			// do a search filter on the event volunteers table
			$('button[value="search_event_volunteers"]')
			.off( 'click', search_event_volunteers )
			.on( 'click', search_event_volunteers );
			// send selected volunteers to server and associate them with the event
			$('button[value="bulk_add_volunteers_to_event"]')
			.off( 'click', add_volunteers_to_event )
			.on( 'click', add_volunteers_to_event );
			// register and add new volunteer to event
			$('button[value="register_and_add_new_volunteer_to_event"]')
			.off( 'click', register_new_volunteer_for_event )
			.on( 'click', register_new_volunteer_for_event );
			// open a volunteer registration form
			$('button[value="show_register_and_add_new_volunteer_form"]')
			.off( 'click', show_register_new_volunteer_for_event_form )
			.on( 'click', show_register_new_volunteer_for_event_form );
			// cancel volunteer registration
			$('button[value="cancel_volunteer_registration_for_event"]')
			.off( 'click', hide_register_new_volunteer_for_event_form )
			.on( 'click', hide_register_new_volunteer_for_event_form );
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
	        $('.vmat-check-before-save')
	        .off( 'change', add_input_changed_class )
	        .on( 'change', add_input_changed_class );
	        // add hours for a new event
	        $('button[value^="show_select_event"]')
	        .off( 'click', show_event_selection_table )
	        .on( 'click', show_event_selection_table );
	        // cancel event selection table
	        $('button[value^="cancel_event_selection"]')
	        .off( 'click', hide_event_selection_table )
	        .on( 'click', hide_event_selection_table );
	        // filter events selection table
	        $('button[value="filter_manage_volunteer_events"], button[value="search_manage_volunteer_events"]')
			.off( 'click', filter_manage_volunteer_events )
			.on( 'click', filter_manage_volunteer_events );
	        // add a volunteer's hours to an event
	        $('#vmat_manage_volunteer_events_table span.vmat-link[data_action="add"]')
	        .off( 'click', add_manage_volunteer_to_event )
	        .on( 'click', add_manage_volunteer_to_event );
		}
		
		function clear_text_input() {
			var self = this;
			$(self).closest('div.clearable-input').find('input[type="text"]').val('').change();
		}
		
		function add_input_changed_class() {
			$(this).removeClass('vmat-input-error');
			$(this).addClass('vmat-check-before-save-changed');
			// check the multiselect for this row for bulk save
			$(this).closest('tr').find('th input').prop('checked', true).change();
			$(window)
			.off('beforeunload', check_before_unload )
			.on('beforeunload', check_before_unload );
		}
		
		function remove_input_changed_class() {
			$(this).removeClass('vmat-check-before-save-changed');
			// uncheck the multiselect for this row for bulk save
			$(this).closest('tr').find('th input').prop('checked', false).change();
			$(window).off('beforeunload', check_before_unload );
		}
		
		function set_admin_notice_color( id='', color_class='vmat-notice-info' ) {
			$( id + ' .vmat-notice').addClass(color_class);
		}
		
		function scrollTo( selector ) {
		  var accordianMain = $('#wpwrap' );
		  var containerScrollTop = accordianMain.scrollTop();
		  var containerOffsetTop = accordianMain.offset().top;
		  var navOffset = 45;
		  var mainNode = $( selector );
		  var selectionOffsetTop = mainNode.offset().top;
		  $('html, body').animate({
		     scrollTop: containerScrollTop+selectionOffsetTop-containerOffsetTop-navOffset},
		     200);

		}
		
		function show_admin_notice( notice_html ) {
			// the sizer maintains the size of the title block
			$('#vmat_admin_notice_sizer p').html( notice_html );
			// the actual notice is position absolute
			$('#vmat_admin_notice p').html( notice_html );
			$('#vmat_admin_notice .vmat-notice').css('visibility', 'visible');
			$('#vmat_admin_notice').show();
			if( notice_html.indexOf('ERROR') > -1 ) {
				set_admin_notice_color( '#vmat_admin_notice', 'vmat-notice-error');
				scrollTo( '#vmat_admin_notice' );
			} else if( notice_html.indexOf('WARNING') > -1 ) {
				set_admin_notice_color( '#vmat_admin_notice', 'vmat-notice-warning');
			} else if( notice_html.indexOf('INFO') > -1 ) {
				set_admin_notice_color( '#vmat_admin_notice', 'vmat-notice-info');
			} else if( notice_html.indexOf('SUCCESS') > -1 ) {
					set_admin_notice_color( '#vmat_admin_notice', 'vmat-notice-success');
			}
			$('#vmat_admin_title').hide();
		}
		
		function show_ajax_notice( target = '', notice_html='' ) {
			$('#' + target + ' p').html(notice_html);
			if( notice_html.indexOf('ERROR') > -1 ) {
				set_admin_notice_color( '#' + target, 'vmat-notice-error');
				scrollTo( '#' + target );
			} else if( notice_html.indexOf('WARNING') > -1 ) {
				set_admin_notice_color( '#' + target, 'vmat-notice-warning');
			} else if( notice_html.indexOf('INFO') > -1 ) {
				set_admin_notice_color( '#' + target, 'vmat-notice-info');
			} else if( notice_html.indexOf('SUCCESS') > -1 ) {
					set_admin_notice_color( '#' + target, 'vmat-notice-success');
			}
			$('#' + target + ' .vmat-notice').css('visibility', 'visible');
		}
		
		function clear_admin_notice(){
			$('.vmat-notice').removeClass('vmat-notice-info');
			$('.vmat-notice').removeClass('vmat-notice-warning');
			$('.vmat-notice').removeClass('vmat-notice-error');
			$('.vmat-notice').removeClass('vmat-notice-success');
			$('#vmat_admin_notice').hide();
			$('.vmat-notice').css('visibility', 'hidden');
			$('.vmat-notice p').html( '&nbsp;' );
			$('#vmat_admin_title').css('visibility', 'visible').show();
		}
		
		function clear_ajax_notices(){
			var ajax_notices = $('div[id$="status"] .vmat-notice');
			$(ajax_notices).removeClass('vmat-notice-info');
			$(ajax_notices).removeClass('vmat-notice-warning');
			$(ajax_notices).removeClass('vmat-notice-error');
			$(ajax_notices).removeClass('vmat-notice-success');
			$(ajax_notices).css('visibility', 'hidden');
			$('div[id$="status"] .vmat-notice p').html( '&nbsp;' );
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
			$('.waiting').removeClass('waiting');
			clear_ajax_notices();
		}
		
		function handle_events_action( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				var html = response.data.html;
				$('#' + response.data.target ).html(html);
				if ( response.data.ajax_notice.indexOf('ERROR') > -1 ) {
					show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				} else {
					clear_admin_notice();
				}
				
				attach_vmat_events_handlers();
			} else {
				// handle a wp_send_json_error response
				handle_server_failure_response(  response ) ;
			}
			$('.waiting').removeClass('waiting');
			if ( $('.vmat-check-before-save-changed').length == 0 ) {
				$(window).off('beforeunload');
			}
		}
		
		function handle_volunteers_action_for_event( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				if( 'display_target' in response.data && 'display_target_html' in response.data ) {
					$('#' + response.data.display_target ).html(response.data.display_target_html);
				}
				var html = response.data.html;
				if( response.data.target ) {
					$('#' + response.data.target ).html(html);
				}
				if ( response.data.action.indexOf('search') == -1 ) {
					show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				}
				$('tr').removeClass('vmat-action-in-progress');
				attach_vmat_hours_volunteers_handlers();
				attach_vmat_manage_volunteers_handlers();
			} else {
				// handle a wp_send_json_error response
				handle_server_failure_response(  response ) ; 
				// if failure, remove row highlight
				$('tr').removeClass('vmat-action-in-progress');
			}
			$('.waiting').removeClass('waiting');
			if ( $('.vmat-check-before-save-changed').length == 0 ) {
				$(window).off('beforeunload');
			}
		}
		
		function handle_register_new_volunteer_for_event( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				if ( response.data.ajax_notice.indexOf('ERROR') > -1  && response.data.action.indexOf('search' == -1 ) ) {
					show_ajax_notice( response.data.notice_id, create_error_notice(  Array( response.data.ajax_notice ) ) );
				} else {
					add_new_volunteer_to_event( response.data.volunteer_id )
				}
				
			} else {
				// handle a wp_send_json_error response
				handle_server_failure_response(  response ) ;
				$('.waiting').removeClass('waiting');
			}
			if ( $('.vmat-check-before-save-changed').length == 0 ) {
				$(window).off('beforeunload');
			}
		}
		
		function handle_update_volunteer( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				if( response.data.target == 'vmat_manage_volunteers_table' ) {
					hide_update_volunteer_form();
				}
			} else {
				// handle a wp_send_json_error response
				handle_server_failure_response(  response ) ;
			}
			$('.waiting').removeClass('waiting');
			if ( $('.vmat-check-before-save-changed').length == 0 ) {
				$(window).off('beforeunload');
			}
		}
		
		function handle_add_new_volunteer( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				if ( response.data.ajax_notice.indexOf('ERROR') > -1  || response.data.action.indexOf('search' == -1 ) ) {
					show_ajax_notice( response.data.notice_id, create_error_notice(  Array( response.data.ajax_notice ) ) );
				}
				
			} else {
				// handle a wp_send_json_error response
				handle_server_failure_response(  response ) ;
			}
			$('.waiting').removeClass('waiting');
			if ( $('.vmat-check-before-save-changed').length == 0 ) {
				$(window).off('beforeunload');
			}
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
							$(volunteer_data[volunteer_id][item].selector).val(event_data['iso_start_date']).change();
							break;
						case '_volunteer_num_days':
							$(volunteer_data[volunteer_id][item].selector).val(event_data['days']).change();
							break;
						case '_approved':
							$(volunteer_data[volunteer_id][item].selector).attr('checked', true).change();
							break;
						default:
						}
					});
				}) ;
				show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				$('tr').removeClass('vmat-action-in-progress');
				attach_vmat_hours_volunteers_handlers();
			} else {
				// handle a wp_send_json_error response
				handle_server_failure_response(  response ) ;
				// if failure, remove row highlight
				$('tr').removeClass('vmat-action-in-progress');
			}
			$('.waiting').removeClass('waiting');
			if ( $('.vmat-check-before-save-changed').length == 0 ) {
				$(window).off('beforeunload');
			}
		}
		
		function handle_failed_volunteers_action_for_event( jqxhr, status, error ) {
			// if failure, remove row highlight
			$('tr').removeClass('vmat-action-in-progress');
		}
		
		function highlight_failed_inputs( validated_data ) {
			// add highlights to any inputs that didn't validate
			Object.keys( validated_data ).forEach( function( dataset ) {
				Object.values( validated_data[dataset] ).forEach( function( item ) {
					if( ! item.valid ) {
						$( item.selector ).addClass( 'vmat-input-error' );
					}
				});
			});
		}
		
		function handle_paginate_action( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				var html = response.data.html;
				var target = response.data.target;
				$('#' + target).html(html);
				switch( target ) {
				case 'vmat_events_table':
					attach_vmat_events_handlers();
					break;
				case 'vmat_volunteers_table':
					attach_vmat_hours_volunteers_handlers();
					break;
				case 'vmat_manage_volunteers_table':
					attach_vmat_manage_volunteers_handlers();
					break;
				case 'vmat_manage_volunteer_events_table':
				case 'vmat_event_volunteers_table':
					attach_vmat_hours_volunteers_handlers();
					break;
				}
			} else {
				// handle a wp_send_json_error response
				handle_server_failure_response(  response ) ;
			}
			$('.waiting').removeClass('waiting');
			if ( $('.vmat-check-before-save-changed').length == 0 ) {
				$(window).off('beforeunload');
			}
		}
		
		function handle_server_failure_response( response ) {
			if( response.data && response.data && response.data.notice_id ) {
				if( typeof( response ) != 'string' ) {
					show_ajax_notice( response.data.notice_id, response.data.ajax_notice );
				} else {
					show_ajax_notice( response.data.notice_id, create_error_notice(  Array( response ) ) );
				}
			} else if( response.data && response.data && response.data.ajax_notice ) {
				show_admin_notice( response.data.ajax_notice );
			} else if ( typeof( response ) == 'string' ) {
				show_admin_notice( create_error_notice(  Array( response ) ) );
			}
		}
		
		function get_bulk_volunteer_data( event_id=0 ) {
	        var volunteer_data = {};
	        $('#vmat_event_volunteers_table input[id^="vmat_event_volunteer_cb_"]:checked').each( 
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
		    	data[key].required = $(data[key].selector).prop('required');
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
		
		function get_bulk_volunteer_hours_data( volunteer_id=0 ) {
	        var volunteer_data = {};
	        $('#vmat_manage_volunteer_table input[id^="vmat_hour_cb_"]:checked').each( 
	        	function(i,v) {
	        		var hour_id = $(v).prop('id').replace( 'vmat_hour_cb_', '' );
	        		var data = get_volunteer_hour_data( volunteer_id, hour_id );
        		    volunteer_data[hour_id] = data;
	        	}
	        );
	        return volunteer_data;
		}
		
		function get_volunteer_hour_data( volunteer_id=0, hour_id=0 ) {
			var data = {
					'_hours_per_day': {'selector': 'input#vmat_hours_per_day_' + volunteer_id + '_' + hour_id},
					'_volunteer_start_date': {'selector': 'input#vmat_start_date_' + volunteer_id + '_' + hour_id},
					'_volunteer_num_days' : {'selector': 'input#vmat_days_' + volunteer_id + '_' + hour_id},
					'_approved' : {'selector': 'input#vmat_hour_approved_' + volunteer_id + '_' + hour_id}
			};
		    Object.keys( data ).forEach( function( key ) {
		    	data[key].required = $(data[key].selector).prop('required');
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
		
		function get_volunteer_registration_data( form_id='' ) {
			var data = {
					'user_login': {'selector': '#user_login'},
					'email': {'selector': '#email'},
					'first_name': {'selector': '#first_name'},
					'last_name': {'selector': '#last_name'},
					'_vmat_volunteer_type': {'selector': 'input[name="_vmat_volunteer_type[]"]'},
					'_vmat_phone_cell': {'selector': '#_vmat_phone_cell'},
					'_vmat_phone_landline': {'selector': '#_vmat_phone_landline'},
					'_vmat_address_street': {'selector': '#_vmat_address_street'},
					'_vmat_address_city': {'selector': '#_vmat_address_city'},
					'_vmat_address_zipcode': {'selector': '#_vmat_address_zipcode'},
					'_vmat_volunteer_skillsets': {'selector': 'input[name="_vmat_volunteer_skillsets[]"]'},
					'_vmat_volunteer_interests': {'selector': 'input[name="_vmat_volunteer_interests[]"]'},
					'volunteer_id': {'selector': 'input[name="volunteer_id"]'}
			};
			Object.keys( data ).forEach( function( key ) {
				var field = $(data[key].selector);
				if( field.attr('type') == 'checkbox' ) {
					data[key].type = 'checkbox';
					if( field.attr('name').indexOf('[]') > -1) {
						// array of checkboxes
						var array_data = Array();
						var inputs = $(data[key].selector + ':checked');
						$(inputs).each(function() {
							array_data.push( $(this).val() );
						});
						data[key].val = array_data;
					} else {
						// single value
						data[key].val = 0;
						if( $(data[key].selector).prop('checked') ) {
							data[key].val = 1;
						}
					}
					
				} else {
					data[key].required = $(data[key].selector).prop('required');
					data[key].type = $(data[key].selector).attr('type');
					data[key].min = $(data[key].selector).attr('min');
					data[key].max = $(data[key].selector).attr('max');
					data[key].val = $(data[key].selector).val();
				}
			});
			return data;
		}
		
		function validate_inputs( data={}, items=[] ) {
			var errors = Array();
			Object.keys( data ).forEach( function( dataset ) {
				Object.values( items ).forEach( function( item ) {
					data[dataset][item].valid = true;
					var required = data[dataset][item].required;
					var val = data[dataset][item].val;
					var min = data[dataset][item].min;
					var max = data[dataset][item].max;
					var selector = data[dataset][item].selector;
					var name = $( selector ).attr('data_name');
					if( name === undefined ) {
						name = $( selector ).attr('name');
					}
					if( name === undefined ) {
						name = $( selector ).val();
					}
					if( required && ( val === undefined || val==='' ) ) {
						var err =  name + ' is required.';
						errors.push( err );
						data[dataset][item].valid = false;
					} else {
						switch( data[dataset][item].type ) {
						case 'hidden':
							break;
						case 'text':
							if( $( selector + ':invalid' ).length > 0 ) {
								var err = name + ' invalid value "' + data[dataset][item].val + '". Type="' + data[dataset][item].type + '"';
								if( ! ( min === undefined ) && ! ( max === undefined ) ) {
									err = err + ' valid range=[' + min + ' - ' + max + ']';
								}
								errors.push( err );
								data[dataset][item].valid = false;
							}
							break;
						case 'email':
							if( $( selector + ':invalid' ).length > 0 ) {
								var err = name + ' invalid value "' + data[dataset][item].val + '". Type="' + data[dataset][item].type + '"';
								if( ! ( min === undefined ) && ! ( max === undefined ) ) {
									err = err + ' valid range=[' + min + ' - ' + max + ']';
								}
								errors.push( err );
								data[dataset][item].valid = false;
							}
							break;
						case 'number':
							var result = check_numeric_input( selector );
							if( ! result.success || $( selector + ':invalid' ).length > 0 ) {
								var err = name + ' invalid value "' + data[dataset][item].val + '". Type="' + data[dataset][item].type + '"';
								if( ! ( min === undefined ) && ! ( max === undefined ) ) {
									err = err + ' valid range=[' + min + ' - ' + max + ']';
								}
								errors.push( err );
								data[dataset][item].valid = false;
							}
							break;
						case 'date':
							var result = check_date_input( selector );
							if( ! result.success || $( selector + ':invalid' ).length > 0 ) {
								var err = name + ' invalid value "' + data[dataset][item].val + '". Type="' + data[dataset][item].type + '"';
								if( ! ( min === undefined ) && ! ( max === undefined ) ) {
									err = err + ' valid range=[' + min + ' - ' + max + ']';
								}
								errors.push( err );
								data[dataset][item].valid = false;
							}
							break;
							break;
						case 'checkbox':
							break;
						default:
							
						}
					}
				}
				);
			} 
			); 
			return {
				'errors': errors,
				'data': data
			};
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
		
		/*
		 * Action functions begin here
		 */
		
		function show_event_selection_table() {                       //use in callback
	        // open event selection table
			// provide cancel to return to previous volunteer admin with no action
			clear_admin_notice();
			
			$('#vmat_manage_volunteer_table').hide();
			$('#vmat_manage_volunteer_events_table').show();
	    }
		
		function hide_event_selection_table() {                       //use in callback
	        // hide event selection table
			// provide cancel to return to previous volunteer admin with no action
			clear_admin_notice();
			
			$('#vmat_manage_volunteer_events_table').hide();
			$('#vmat_manage_volunteer_table').show();
	    }
		
		function show_update_volunteer_form() {                       //use in callback
	        // open volunteer registration form
			// provide cancel to return to previous volunteer admin with no action
			// provide Save button to save
			clear_admin_notice();
			
			$('#vmat_manage_volunteers_admin, #vmat_manage_volunteer_admin').hide();
			$('#vmat_update_volunteer_admin').show();
	    }
		
		function hide_update_volunteer_form() {                       //use in callback
	        // close volunteer registration form
			clear_admin_notice();
			
			$('#vmat_update_volunteer_admin').hide();
			$('#vmat_manage_volunteers_admin, #vmat_manage_volunteer_admin').show();
			
	    }
		
		function show_register_new_volunteer_for_event_form() {                       //use in callback
	        // open volunteer registration form
			// provide cancel to return to previous volunteer admin with no action
			// provide Add button to 
			clear_admin_notice();
			$('#vmat_volunteer_participation_admin').hide();
			$('#vmat_register_and_add_volunteer_admin').show();
		}
		
		function hide_register_new_volunteer_for_event_form() {                       //use in callback
	        // open volunteer registration form
			// provide cancel to return to previous volunteer admin with no action
			// provide Add button to 
			clear_admin_notice();
			$('#vmat_register_and_add_volunteer_admin').hide();
			$('#vmat_volunteer_participation_admin').show();
	    }
		
		function register_new_volunteer_for_event() {
			var self = this;
			if( check_if_save_needed() ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', register_new_volunteer_for_event_do_action.bind(self) );
			} else {
				register_new_volunteer_for_event_do_action(self);
			}
		}
		
		function register_new_volunteer_for_event_do_action( arg=null ) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
	        var event_id = $('input[name="event_id"]').val();
	        var volunteer_data = {};
	        // need extra level in the data structure to use the validate_inputs function
	        // which can be used across multiple sets of data. The volunteer registration is
	        // only dealing with a single set of data
	        volunteer_data['fields'] = get_volunteer_registration_data('vmat_register_volunteer_admin');
	        var items_to_validate = [
	        	'user_login',
				'email',
				'first_name',
				'last_name',
				'_vmat_volunteer_type',
				'_vmat_phone_cell',
				'_vmat_phone_landline',
				'_vmat_address_street',
				'_vmat_address_city',
				'_vmat_address_zipcode',
				'_vmat_volunteer_skillsets',
				'_vmat_volunteer_interests'
	        ];
	        var results = validate_inputs(volunteer_data, items_to_validate );
	        var messages = results.errors;
	        var validated_data = results.data;
	        clear_admin_notice();
	        
	        show_ajax_notice( 'volunteer_registration_status', 'working....' );
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        if( messages.length == 0 ) {
	        	var request = {
						_ajax_nonce: my_ajax_obj.nonce,
						action: "ajax_update_volunteer",
						event_id: event_id,
						volunteer_data: volunteer_data,
						notice_id: 'volunteer_registration_status',
						target: 'vmat_volunteer_participation_admin',
						register_notice_id: 'volunteer_registration_status',
					};
		        clear_admin_notice();
		        show_ajax_notice( 'volunteer_registration_status', 'working....' );
		        $.post( my_ajax_obj.ajax_url, request )
		        .done( handle_register_new_volunteer_for_event ) // handle any successful wp_send_json_success/error
		        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	        } else {
	        	var notice_html = create_error_notice( messages );
				show_ajax_notice( 'volunteer_registration_status', notice_html );
	        	handle_failed_volunteers_action_for_event();
	        	highlight_failed_inputs( validated_data );
	        	$('.waiting').removeClass('waiting');
	        }
	    }
		
		function update_volunteer() {
			// no need to check for unsaved data because this occurs in a view where there are
			// no inputs that could be lost
			var self = this;
	        var volunteer_data = {};
	        // need extra level in the data structure to use the validate_inputs function
	        // which can be used across multiple sets of data. The volunteer registration is
	        // only dealing with a single set of data
	        volunteer_data['fields'] = get_volunteer_registration_data('vmat_update_volunteer_admin');
	        var items_to_validate = [
	        	'user_login',
				'email',
				'first_name',
				'last_name',
				'_vmat_volunteer_type',
				'_vmat_phone_cell',
				'_vmat_phone_landline',
				'_vmat_address_street',
				'_vmat_address_city',
				'_vmat_address_zipcode',
				'_vmat_volunteer_skillsets',
				'_vmat_volunteer_interests'
	        ];
	        var results = validate_inputs(volunteer_data, items_to_validate );
	        var messages = results.errors;
	        var validated_data = results.data;
	        var target;
	        var notice_id;
	        if( 'volunteer_id' in validated_data.fields && validated_data.fields.volunteer_id.val !== undefined ) {
	        	// updated an existing volunteer
	        	target = 'vmat_manage_volunteer_table';
	        	notice_id = 'volunteer_update_status';
	        } else {
	        	// added a new volunteer
	        	target = 'vmat_manage_volunteers_table';
	        	notice_id = 'manage_volunteers_status';
	        }
	        clear_admin_notice();
	        show_ajax_notice( 'volunteer_update_status', 'working....' );
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        if( messages.length == 0 ) {
	        	var request = {
						_ajax_nonce: my_ajax_obj.nonce,
						action: "ajax_update_volunteer",
						volunteer_data: volunteer_data,
						target: target,
						notice_id: notice_id,
						register_notice_id: 'volunteer_update_status'
					};
		        clear_admin_notice();
		        $.post( my_ajax_obj.ajax_url, request )
		        .done(handle_update_volunteer)
		        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
		        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	        } else {
	        	var notice_html = create_error_notice( messages );
				show_ajax_notice( 'volunteer_update_status', notice_html );
	        	handle_failed_volunteers_action_for_event();
	        	highlight_failed_inputs( validated_data );
	        	$('.waiting').removeClass('waiting');
	        }
	    }
		
		function add_new_volunteer_to_event( volunteer_id ) {                       //use in callback
	        var event_id = $('input[name="event_id"]').val();
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_add_volunteers_to_event",
					volunteer_ids: Array( volunteer_id.toString() ),
					event_id: event_id,
					notice_id: 'event_volunteers_status',
					target: 'vmat_volunteer_participation_admin',
					display_target: 'vmat_event_display_admin'
				};
	        clear_admin_notice();
	        hide_register_new_volunteer_for_event_form();
	        show_ajax_notice( 'event_volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteer_to_event
	        .done( handle_volunteers_action_for_event );
	    }
		
		function filter_events() {
			// no need to check for unsaved data because this occurs in a view where there are
			// no inputs that could be lost
			var self = this;
	        var search = $('#vmat_events_table input[name="events_search"]').val();
	        var vmat_org = $('#vmat_events_table select[name="vmat_org"]').val();
	        var scope = $('#vmat_events_table select[name="scope"]').val();
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_filter_events",
					events_search: search,
					vmat_org: vmat_org,
					scope: scope,
					notice_id: 'events_status',
					target: 'vmat_events_table',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'events_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_events_action ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function filter_manage_volunteer_events() {
			// no need to check for unsaved data because this occurs in a view where there are
			// no inputs that could be lost
			var self = this;
	        var search = $('#vmat_manage_volunteer_events_table input[name="manage_volunteer_events_search"]').val();
	        var vmat_org = $('#vmat_manage_volunteer_events_table select[name="vmat_org"]').val();
	        var scope = $('#vmat_manage_volunteer_events_table select[name="scope"]').val();
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_filter_manage_volunteer_events",
					events_search: search,
					vmat_org: vmat_org,
					scope: scope,
					notice_id: 'manage_volunteer_events_status',
					target: 'vmat_manage_volunteer_events_table',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'manage_volunteer_events_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function search_volunteers() {
			// no need to check for unsaved data because this occurs in a view where there are
			// no inputs that could be lost
			var self = this;
	        var search = $('#vmat_volunteers_table input[name="volunteers_search"]').val();
	        var event_id = $('input[name="event_id"]').val();
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_search_volunteers",
					volunteers_search: search,
					event_id: event_id,
					notice_id: 'volunteers_status',
					target: 'vmat_volunteers_table',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function associate_event_bookings_with_volunteers() {
			// no need to check for unsaved data because this occurs in a view where there are
			// no inputs that could be lost
			var self = this;
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_associate_event_bookings_with_volunteers",
					notice_id: 'manage_volunteers_status',
					target: 'vmat_manage_volunteers_table',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'manage_volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function filter_manage_volunteers() {
			// no need to check for unsaved data because this occurs in a view where there are
			// no inputs that could be lost
			var self = this;
	        var search = $('#vmat_manage_volunteers_table input[name="manage_volunteers_search"]').val();
	        var vmat_org = $('#vmat_manage_volunteers_table select[name="vmat_org"]').val();
	        var vmat_vol_type = $('#vmat_manage_volunteers_table select[name="vmat_vol_type"]').val();
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_filter_manage_volunteers",
					volunteers_search: search,
					vmat_org: vmat_org,
					vmat_vol_type: vmat_vol_type,
					notice_id: 'manage_volunteers_status',
					target: 'vmat_manage_volunteers_table',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'manage_volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function search_manage_volunteer() {
			// no need to check for unsaved data because this occurs in a view where there are
			// no inputs that could be lost
			var self = this;
			var volunteer_id = $(self).attr('volunteer_id');
	        var search = $('#vmat_manage_volunteer_table input[name="manage_volunteer_search"]').val();
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_search_manage_volunteer",
					volunteer_id: volunteer_id,
					manage_volunteer_search: search,
					notice_id: 'manage_volunteer_status',
					target: 'vmat_manage_volunteer_table',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'manage_volunteer_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function search_event_volunteers() {
			var self = this;
			if( check_if_save_needed() ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', search_event_volunteers_do_action.bind(self) );
			} else {
				search_event_volunteers_do_action( self );
			}
		}
		
		function search_event_volunteers_do_action( arg=null ) { 
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
	        var search = $('#vmat_event_volunteers_table input[name="event_volunteers_search"]').val();
	        var event_id = $('input[name="event_id"]').val();
	        $(self).addClass('waiting');
			$('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_search_event_volunteers",
					event_volunteers_search: search,
					event_id: event_id,
					notice_id: 'event_volunteers_status',
					target: 'vmat_event_volunteers_table',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function add_manage_volunteer_to_event() {
			var self = this;
			if( check_if_save_needed( '#vmat_manage_volunteer_table') ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', add_manage_volunteer_to_event_do_action.bind(self) );
			} else {
				add_manage_volunteer_to_event_do_action( self );
			}
		}
		
		function add_manage_volunteer_to_event_do_action( arg=null ) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
	        var event_id = $(self).attr('event_id');
	        var volunteer_id = $(self).attr('volunteer_id');
			$( self ).closest('tr').addClass('vmat-action-in-progress');
			$(self).addClass('waiting');
	        $('html').addClass('waiting');
	        hide_event_selection_table();
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_add_manage_volunteer_to_event",
					volunteer_id: volunteer_id,
					event_id: event_id,
					notice_id: 'manage_volunteer_status',
					target: 'vmat_manage_volunteer_admin',
					display_target: 'vmat_volunteer_display_admin'
				};
	        clear_admin_notice();
	        show_ajax_notice( 'manage_volunteer_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteer_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function add_volunteer_to_event() {
			var self = this;
			if( check_if_save_needed() ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', add_volunteer_to_event_do_action.bind(self) );
			} else {
				add_volunteer_to_event_do_action( self );
			}
		}
		
		function add_volunteer_to_event_do_action( arg=null ) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				self = this;
			} else {
				self = arg;
			}
	        var event_id = $('input[name="event_id"]').val();
	        var volunteer_id = $(self).prop('id').replace('vmat_selected_volunteer_','');
			$( self ).closest('tr').addClass('vmat-action-in-progress');
			$(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_add_volunteers_to_event",
					volunteer_ids: Array( volunteer_id.toString() ),
					event_id: event_id,
					notice_id: 'volunteers_status',
					target: 'vmat_volunteer_participation_admin',
					display_target: 'vmat_event_display_admin'
				};
	        clear_admin_notice();
	        show_ajax_notice( 'volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteer_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	    }
		
		function add_volunteers_to_event() {
			var self = this;
			if( check_if_save_needed() ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', add_volunteers_to_event_do_action.bind(self) );
			} else {
				add_volunteers_to_event_do_action( self );
			}
		}
		
		function add_volunteers_to_event_do_action( arg=null ) { 
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				self = this;
			} else {
				self = arg;
			}                    
	        var event_id = $('input[name="event_id"]').val();
	        var volunteer_ids = Array();
			$('#vmat_volunteers_table input[id^="vmat_volunteer_cb_"]:checked').each( function(i,v)
	        		{
	        		    volunteer_ids.push($(v).prop('id').replace( 'vmat_volunteer_cb_', '' ));
	        		});
	        $('#vmat_volunteers_table input[id^="vmat_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: "ajax_add_volunteers_to_event",
					volunteer_ids: volunteer_ids,
					event_id: event_id,
					notice_id: 'volunteers_status',
					target: 'vmat_volunteer_participation_admin',
					display_target: 'vmat_event_display_admin'
				};
	        clear_admin_notice();
	        show_ajax_notice( 'volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function take_action_for_event_volunteers() {
			var self = this;
			var action = $(self).attr('value').replace( 'bulk_event_volunteers_', '');
			if( action === 'remove' && check_if_save_needed() ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', take_action_for_event_volunteers_do_action.bind(self) );
			} else {
				take_action_for_event_volunteers_do_action( self );
			}
		}
		
		function take_action_for_event_volunteers_do_action( arg=null ) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
			$(self).addClass('waiting');
	        $('html').addClass('waiting');
			var event_id = $('input[name="event_id"]').val();
	        var volunteer_data = get_bulk_volunteer_data( event_id );
	        
	        $('#vmat_event_volunteers_table input[id^="vmat_event_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
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
			var self = this;
			var action = $(self).attr('data_action');
			if( action === 'remove' && check_if_save_needed() ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', take_action_for_event_volunteer_do_action.bind(self) );
			} else {
				take_action_for_event_volunteer_do_action( self );
			}
		}
		
		function take_action_for_event_volunteer_do_action( arg=null ) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
			$(self).addClass('waiting');
	        $('html').addClass('waiting');
			var event_id = $('input[name="event_id"]').val();
			var action = $(self).attr('data_action');
			var volunteer_id = $(self).attr('volunteer_id');
			var volunteer_data = {};
	        volunteer_data[volunteer_id] = get_volunteer_data( event_id, volunteer_id );
	        $(self).closest('tr').addClass('vmat-action-in-progress');
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
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
		
		function take_action_for_volunteer_hours() {
			var self = this;
			var action = $(self).attr('value').replace( 'bulk_hours_', '');
			if( action == 'remove' && check_if_save_needed() ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', take_action_for_volunteer_hours_do_action.bind(self) );
			} else {
				take_action_for_volunteer_hours_do_action( self );
			}
		}
		
		function take_action_for_volunteer_hours_do_action( arg=null ) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
			$(self).addClass('waiting');
	        $('html').addClass('waiting');
			var volunteer_id = $(self).attr('volunteer_id');
	        var volunteer_data = get_bulk_volunteer_hours_data( volunteer_id );
	        
	        $('#vmat_manage_volunteer_table input[id^="vmat_hour_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
			// get the action from the select
	        var action = $(self).attr('value').replace( 'bulk_hours_', '');
			switch ( action ) {
			case 'remove':
				remove_volunteer_hours( volunteer_id, volunteer_data );
				break;
			case 'save':
				save_volunteer_hours_data( volunteer_id, volunteer_data );
				break;
			}
		}
		
		function take_action_for_volunteer_hour() {
			var self = this;
			var action = $(self).attr('data_action');
			if( action === 'remove' && check_if_save_needed() ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', take_action_for_volunteer_hour_do_action.bind(self) );
			} else {
				take_action_for_volunteer_hour_do_action( self );
			}
		}
		
		
		function take_action_for_volunteer_hour_do_action( arg=null ) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
			$(self).addClass('waiting');
	        $('html').addClass('waiting');
			var volunteer_id = $(self).attr('volunteer_id');
			var hour_id = $(self).attr('hour_id');
			var action = $(self).attr('data_action');
			var volunteer_data = {};
	        volunteer_data[hour_id] = get_volunteer_hour_data( volunteer_id, hour_id );
	        $(self).closest('tr').addClass('vmat-action-in-progress');
			switch ( action ) {
			case 'remove':
				remove_volunteer_hours( volunteer_id, volunteer_data );
				break;
			case 'save':
				save_volunteer_hours_data( volunteer_id, volunteer_data );
				break;
			}
		}
		
		function remove_volunteers_from_event( event_id=0, volunteer_data={} ) { 
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_remove_volunteers_from_event',
					volunteer_data: volunteer_data,
					event_id: event_id,
					notice_id: 'event_volunteers_status',
					target: 'vmat_volunteer_participation_admin',
					display_target: 'vmat_event_display_admin'
				};
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function save_event_volunteers_data( event_id=0, volunteer_data={} ) {   
	        var items_to_validate = [
	        	'_hours_per_day',
	        	'_volunteer_start_date',
	        	'_volunteer_num_days',
	        	'_approved'
	        ];
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_status', 'working....' );
	        var results = validate_inputs(volunteer_data, items_to_validate );
	        var messages = results.errors;
	        var validated_data = results.data;
	        var evpno = $('#vmat_event_volunteers_table input.current-page').val();
	        if( ! evpno ) {
	        	evpno = 1;
	        }
	        var search = $('#vmat_event_volunteers_table input[name="event_volunteers_search"]').val();
	        if( messages.length == 0 ) {
	        	var request = {
						_ajax_nonce: my_ajax_obj.nonce,
						action: 'ajax_save_event_volunteers_data',
						volunteer_data: volunteer_data,
						event_id: event_id,
						notice_id: 'event_volunteers_status',
						evpno: evpno,
						event_volunteers_search: search,
						target: 'vmat_event_volunteers_table'
					};
		        $.post( my_ajax_obj.ajax_url, request )
		        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
		        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
		        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	        } else {
	        	var notice_html = create_error_notice( messages );
				show_ajax_notice( 'event_volunteers_status', notice_html );
	        	handle_failed_volunteers_action_for_event();
	        	highlight_failed_inputs( validated_data );
		        $('.waiting').removeClass('waiting');;
	        }
		}
		
		function remove_volunteers() {
			var self = this;
			var message = 'Deleting a volunteer will also permanently delete all of that volunteer\'s event participation data! <br />"OK" to proceed and lose volunteer data.';
			$('#vmat_ok_cancel_modal .modal-body').html(message);
			$('#vmat_ok_cancel_modal').modal('show');
			$('#vmat_ok_cancel_modal button#vmat_ok')
			.off('click')
			.on('click', remove_volunteers_do_action.bind(self) );
		}
		
		function remove_volunteers_do_action( arg=null ) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
	        var volunteers = $('#vmat_manage_volunteers_table .check-column input:checked');
	        var volunteer_ids = Array();
	        $('#vmat_manage_volunteers_table input[id^="vmat_manage_volunteer_cb_"]:checked').each( function(i,v)
	        		{
	        		    volunteer_ids.push($(v).prop('id').replace( 'vmat_manage_volunteer_cb_', '' ));
	        		});
	        $('#vmat_manage_volunteers_table input[id^="vmat_manage_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
	        $(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_remove_volunteers',
					volunteer_ids: volunteer_ids,
					notice_id: 'manage_volunteers_status',
					target: 'vmat_manage_volunteers_table',
				};
	        clear_admin_notice();
	        show_ajax_notice( 'manage_volunteers_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}

		function remove_volunteer_hours( volunteer_id=0, volunteer_data={}, self ) {  
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_remove_hours_from_volunteer',
					volunteer_data: volunteer_data,
					volunteer_id: volunteer_id,
					notice_id: 'manage_volunteer_status',
					target: 'vmat_manage_volunteer_admin',
					display_target: 'vmat_volunteer_display_admin'
				};
	        clear_admin_notice();
	        show_ajax_notice( 'manage_volunteer_status', 'working....' );
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function save_volunteer_hours_data( volunteer_id=0, volunteer_data={}, self ) {  
	        var items_to_validate = [
	        	'_hours_per_day',
	        	'_volunteer_start_date',
	        	'_volunteer_num_days',
	        	'_approved'
	        ];
	        clear_admin_notice();
	        show_ajax_notice( 'manage_volunteer_status', 'working....' );
	        var results = validate_inputs(volunteer_data, items_to_validate );
	        var messages = results.errors;
	        var validated_data = results.data;
	        var hpno = $('#vmat_manage_volunteer_table input.current-page').val();
	        if( ! hpno ) {
	        	hpno = 1;
	        }
	        var search = $('#vmat_manage_volunteer_table input[name="manage_volunteer_search"]').val();
	        if( messages.length == 0 ) {
	        	var request = {
						_ajax_nonce: my_ajax_obj.nonce,
						action: 'ajax_save_volunteer_hours_data',
						volunteer_data: volunteer_data,
						volunteer_id: volunteer_id,
						notice_id: 'manage_volunteer_status',
						hpno: hpno,
						manage_volunteer_search: search,
						target: 'vmat_manage_volunteer_table',
						display_target: 'vmat_volunteer_display_admin'
					};
		        $.post( my_ajax_obj.ajax_url, request )
		        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
		        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
		        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
	        } else {
	        	var notice_html = create_error_notice( messages );
				show_ajax_notice( 'manage_volunteer_status', notice_html );
	        	handle_failed_volunteers_action_for_event();
	        	highlight_failed_inputs( validated_data );
	        	$('.waiting').removeClass('waiting');
	        }
		}
		
		function set_default_event_volunteers_data( event_id=0, volunteer_data={}, self ) {  
	        clear_admin_notice();
	        show_ajax_notice( 'event_volunteers_status', 'working....' );
        	var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_set_default_event_volunteers_data',
					volunteer_data: volunteer_data,
					event_id: event_id,
					notice_id: 'event_volunteers_status',
					target: 'vmat_event_volunteers_table'
				};
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_set_default_event_volunteers_data ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function paginate_tables() {
			var self = this;
			var context = $(self).closest('div[id$="table"]');
			if( check_if_save_needed( '#' + $(context).attr('id') ) ) {
				$('#vmat_ok_cancel_modal button#vmat_ok')
				.off('click')
				.on('click', paginate_tables_do_action.bind(self) );
			} else {
				paginate_tables_do_action( self );
			}
		}
		
		function paginate_tables_do_action( arg=null) {
			if( arg.originalEvent !== undefined) {
				// an event rather than a passed in element
				var self = this;
			} else {
				var self = arg;
			}
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
			$(self).addClass('waiting');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_paginate_vmat_admin_page',
					data: data,
				};
	        clear_admin_notice();
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_paginate_action ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		attach_vmat_events_handlers();
        attach_vmat_hours_volunteers_handlers();
        attach_vmat_manage_volunteers_handlers();
        // search text fields not getting reset to empty on page reloads for some reason
        
        $('div.clearable-input input[type="text"]').val('');
	});

})( jQuery );
