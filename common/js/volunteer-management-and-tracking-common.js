(function( $ ) {
	'use strict';

	/**
	 * All of the JavaScript source code common to public, admin, login
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
	$(function() {
		// show/hide volunteer registration fields and change selected role
		$("#vmat_is_volunteer").on("change", function() {
			if(this.checked) {
				$(".vmat-registration-fields").show();
				$('option:selected', 'select[name="role"]').removeAttr('selected');
				$('select[name="role"]').find('option[value="volunteer"]').attr("selected",true);
			} else {
				$(".vmat-registration-fields").hide();
				$('option:selected', 'select[name="role"]').removeAttr('selected');
				$('select[name="role"]').find('option[value="subscriber"]').attr("selected",true);
			}
		});
		// link volunteer first name to user first name in add user form
		$("#first_name").on("change", function() {
			$("#vmat_first_name").val($(this).val());
		});
		// link volunteer first name to user first name in add user form
		$("#last_name").on("change", function() {
			$("#vmat_last_name").val($(this).val());
		});
	});

})( jQuery );
