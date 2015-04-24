/**
* This files sets up front end form checking
* and adds jquery ui style features.
*/
jQuery(function($) {

	jQuery(document).ready(function() {
	    jQuery('#end_date').datepicker({
	        dateFormat : 'yy-mm-dd'
	    });
	    jQuery('#publish_date').datetimepicker({
	        dateFormat : 'yy-mm-dd',
	        timeFormat: "hh:mm:ss"
	    });
	    jQuery('#unpublish_date').datetimepicker({
	        dateFormat : 'yy-mm-dd',
	        timeFormat: "hh:mm:ss"
	    });
	});
 
});