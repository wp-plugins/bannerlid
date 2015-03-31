/**
* Functionality for sorting the banners with 
* JQuery UI .sortable()
*/
jQuery(document).ready(function(){

	/**
	* Sets the sortable preview banners as sortable and 
	* sets the order to a vraiable when the banners are
	* rearranged.
	*/ 
    jQuery( "#sortable" ).sortable({
        update: function(event, ui) {
        	var banner_order = jQuery(this).sortable('toArray').toString();
        	var order_string = banner_order.replace(/banner_/g, "");
        	jQuery('#positions').val(order_string);
         	//alert(banner_order);
        }
     });
	
});