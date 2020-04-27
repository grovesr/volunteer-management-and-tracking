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
		function scrollTo( selector ) {
			  var accordianMain = $('#wpwrap' );
			  var containerScrollTop = accordianMain.scrollTop();
			  var containerOffsetTop = accordianMain.offset().top;
			  var navOffset = 120;
			  var mainNode = $( selector );
			  var selectionOffsetTop = mainNode.offset().top;
			  $('html, body').animate({
			     scrollTop: containerScrollTop+selectionOffsetTop-containerOffsetTop-navOffset},
			     200);

			}
		$('#datepicker_start').datepicker(
				{
					dateFormat: 'yy-mm-dd',
					showAnim: 'slideDown',
					changeMonth: true,
					changeYear: true
				}
		);
        $('#datepicker_end').datepicker(
        		{
					dateFormat: 'yy-mm-dd',
					showAnim: 'slideDown',
					changeMonth: true,
					changeYear: true
				}
        );
        $('[id^="vmat_start_date_"]').datepicker(
				{
					dateFormat: 'yy-mm-dd',
					showAnim: 'slideDown',
					changeMonth: true,
					changeYear: true
				}
		);
        
        $('#help_accordian').on('shown.bs.collapse', function (e) {
        	scrollTo( '#' + $(e.target).attr('id') );
        });
        var hash = $(location).attr('hash');
        if( hash.indexOf( 'help' ) >= 0 ) {
        	$(hash.replace( '_help','' ).replace( '#','#collapse_' )).collapse('show');
        }
	});

})( jQuery );