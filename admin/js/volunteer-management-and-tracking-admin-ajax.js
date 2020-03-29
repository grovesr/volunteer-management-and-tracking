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
			// enable or disable bulk_manage_hours action button depending on
			// state of checkboxes
			$('#vmat_volunteers_table input:checkbox').on( 'load change', function() {
				if($('#vmat_volunteers_table .check-column input:checked').length > 0) {
					$('button[value="bulk_add_volunteers_to_event"]').prop('disabled', false);
				} else {
					$('button[value="bulk_add_volunteers_to_event"]').prop('disabled', true);
				}
		 	});
			$('#vmat_event_volunteers_table input:checkbox').on( 'load change', function() {
				if($('#vmat_event_volunteers_table .check-column input:checked').length > 0) {
					$('button[value="bulk_event_volunteers_action"]').prop('disabled', false);
				} else {
					$('button[value="bulk_event_volunteers_action"]').prop('disabled', true);
				}
		 	});
			$('#vmat_event_volunteers_table .check_column input:checkbox').on( 'load change', function() {
				if($('#vmat_event_volunteers_table td.manage-column input:checked').length > 0) {
					$('button[value="bulk_event_volunteers_action"]').prop('disabled', false);
				} else {
					$('button[value="bulk_event_volunteers_action"]').prop('disabled', true);
				}
		 	});
			// send selected volunteers to server and associate them with the event
			$('button[value="bulk_add_volunteers_to_event"]').on( 'click', add_volunteers_to_event);
			// send single volunteer to server and associate with event
	        $('span.vmat-link[id^="vmat_selected_volunteer_"]').on( 'click', add_volunteer_to_event);
	        // send selected volunteers to server and take the appropriate action
	        $('button[value="bulk_event_volunteers_action"]').on( 'click', take_action_for_event_volunteers);
			// send single volunteer to server and take appropriate action
	        $('span.vmat-link[id^="vmat_selected_event_volunteer_"]').on( 'click', take_action_for_event_volunteer);
	        // when changing inputs in the event volunteers table, change the look
	        $('.vmat-event-volunteer-input').on( 'change', add_input_changed_class );
		}
		
		function add_input_changed_class() {
			$(this).addClass('vmat-event-volunteer-input-changed');
			// check the multiselect for this row for bulk save
			$(this).closest('tr').find('th input').prop('checked', true).change();
		}
		
		function rewmove_input_changed_class() {
			$(this).removeClass('vmat-event-volunteer-input-changed');
			// uncheck the multiselect for this row for bulk save
			$(this).closest('tr').find('th input').prop('checked', false).change();
		}
		
		function show_admin_notice( notice_html ) {
			$('#vmat_admin_notice').html( notice_html );
			if( notice_html.indexOf('notice-error') > -1 ) {
				$('html, body').animate({scrollTop: '0px'}, 300);
			}
    		$(document).click( clear_admin_notice );
		}
		
		function clear_admin_notice(){
			$('#vmat_admin_notice').empty();
			$('#event_volunteers_added_status').html( '&nbsp;' );
			$('#event_volunteers_added_status').removeClass('ajax-added-success');
		}
		
		function handle_failed_ajax_call( jqxhr, status, err ) {
			// handle a failed ajax call
			var error = '<div class="notice notice-error is-dismissible">';
			var error = error + '<p><strong>Error</strong>: ' + status + ' ' + err + '</p>';
			var error = error + '<button type="button" class="notice-dismiss">';
			var error = error + '<span class="screen-reader-text">Dismiss this notice.</span>';
			var erorr = error + '</button>';
			var error = error + '</div>';
			show_admin_notice( error );
		}
		
		function handle_volunteers_action_for_event( response, status, jqxhr ) {
			if ( response.success ) {
				// handle a wp_send_json_success response
				var html=response.data.html;
				$('#vmat_manage_volunteers_admin').html(html);
				$('#event_volunteers_added_status').addClass('ajax-added-success');
				$('#event_volunteers_added_status').html(response.data.success_notice);
				$(document).click( clear_admin_notice );
				attach_vmat_volunteers_handlers();
			} else {
				// handle a wp_send_json_error response
				show_admin_notice( response.data.notice ); // 
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
		
		function get_selected_volunteer_data( event_id=0 ) {
	        var volunteer_data = {};
	        $('form#volunteers-filter input[id^="vmat_event_volunteer_cb_"]:checked').each( 
	        	function(i,v) {
        			var data = {};
        		    var volunteer_id = $(v).prop('id').replace( 'vmat_event_volunteer_cb_', '' );
        		    data._hours_per_day = $('input#vmat_hours_per_day_' + event_id + '_' + volunteer_id).val();
        		    data._volunteer_start_date = $('input#vmat_start_date_' + event_id + '_' + volunteer_id).val();
        		    data._volunteer_num_days = $('input#vmat_days_' + event_id + '_' + volunteer_id).val();
        		    data._approved = 0;
        		    if( $('input#vmat_hours_approved_' + event_id + '_' + volunteer_id).prop('checked') ) {
        		    	data._approved = 1;
        		    }
        		    volunteer_data[volunteer_id] = data;
	        	}
	        );
	        return volunteer_data;
		}
		
		function get_volunteer_data( event_id=0, volunteer_id=0 ) {
	        var volunteer_data = {};
			var data = {};
		    data._hours_per_day = $('input#vmat_hours_per_day_' + event_id + '_' + volunteer_id).val();
		    data._volunteer_start_date = $('input#vmat_start_date_' + event_id + '_' + volunteer_id).val();
		    data._volunteer_num_days = $('input#vmat_days_' + event_id + '_' + volunteer_id).val();
		    data._approved = 0;
		    if( $('input#vmat_hours_approved_' + event_id + '_' + volunteer_id).prop('checked') ) {
		    	data._approved = 1;
		    }
		    volunteer_data[volunteer_id] = data;
	        return volunteer_data;
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
				};
	        clear_admin_notice();
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
				};
	        clear_admin_notice();
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function take_action_for_event_volunteers() {
			var event_id = $('form#volunteers-filter input[name="event_id"]').val();
	        var volunteer_data = get_selected_volunteer_data( event_id );
			var action = $('select[name="event_volunteers_bulk_action"]').val();
			switch ( action ) {
			case 'default':
				set_default_volunteers_hours_for_event( event_id, volunteer_data );
				break;
			case 'approve':
				approve_volunteers_hours_for_event( event_id, volunteer_data );
				break;
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
	        var volunteer_data = get_volunteer_data( event_id, volunteer_id );
			switch ( action ) {
			case 'default':
				set_default_volunteers_hours_for_event( event_id, volunteer_data );
				break;
			case 'approve':
				approve_volunteers_hours_for_event( event_id, volunteer_data );
				break;
			case 'remove':
				remove_volunteers_from_event( event_id, volunteer_data );
				break;
			case 'save':
				save_event_volunteers_data( event_id, volunteer_data );
				break;
			}
		}
		
		function remove_volunteers_from_event( event_id=0, volunteer_data={} ) {  
	        var self = this;                      //use in callback
	        $('form#volunteers-filter input[id^="vmat_event_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_remove_volunteers_from_event',
					volunteer_data: volunteer_data,
					event_id: event_id,
				};
	        clear_admin_notice();
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function save_event_volunteers_data( event_id=0, volunteer_data={} ) {  
	        var self = this;                      //use in callback
	        $('form#volunteers-filter input[id^="vmat_event_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_save_event_volunteers_data',
					volunteer_data: volunteer_data,
					event_id: event_id,
				};
	        clear_admin_notice();
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function approve_volunteers_hours_for_event( event_id=0, volunteer_data={} ) {  
	        var self = this;                      //use in callback
	        $('form#volunteers-filter input[id^="vmat_event_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_approve_volunteers_hours_for_event',
					volunteer_data: volunteer_data,
					event_id: event_id,
				};
	        clear_admin_notice();
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		function set_default_volunteers_hours_for_event( event_id=0, volunteer_data={} ) {  
	        var self = this;                      //use in callback
	        $('form#volunteers-filter input[id^="vmat_event_volunteer_cb_"]:checked').closest('tr').addClass('vmat-action-in-progress');
	        $('html').addClass('waiting');
	        var request = {
					_ajax_nonce: my_ajax_obj.nonce,
					action: 'ajax_set_default_volunteers_hours_for_event',
					volunteer_data: volunteer_data,
					event_id: event_id,
				};
	        clear_admin_notice();
	        $.post( my_ajax_obj.ajax_url, request )
	        .done( handle_volunteers_action_for_event ) // handle any successful wp_send_json_success/error
	        .fail( handle_failed_volunteers_action_for_event ) // handle error specific to add_volunteers_to_event
	        .fail( handle_failed_ajax_call ); // fall through to handle general ajax failures
		}
		
		
        attach_vmat_volunteers_handlers();
	});

})( jQuery );