/**
* This file provides frontend interactions for the media 
* selectors. It hooks into Wordpress's media javascript
* and prvides methods for what to do with the selected 
* media.
*/
jQuery(function($) {

	/**
	* Opens the media modal window which allows users
	* to add images from the Wordpress gallery or upload
	* images to the Wordpress gallery. 
	*/
    function open_media_window() {
	    if (this.window === undefined) {
	        this.window = wp.media({
                title: 'Insert a media',
                //library: {type: 'image'},
                multiple: false,
                button: {text: 'Insert'}
	        });
	 		
	 		/**
	 		* Details what to do when an image has been 
	 		* selected.
	 		*/
	        var self = this;
	        this.window.on('select', function() {
	            var first = self.window.state().get('selection').first().toJSON();
	            var image_url = first.url;
	           	$('#file_url').val(image_url);
	           	updateBannerPreview(image_url);
	        });
	    }
	    this.window.open();
	    return false;
	}

	/**
	 * Puts our preview image in it's spot from the given 
	 * url. 
	 * !!!! Need to do some file type checking here
	 */ 
	function updateBannerPreview(url){
		url = url.toLowerCase();
		var n = url.indexOf(".swf");
		if(n > 0){
			var output = '<object style="max-width: 350;"><param name="movie" value="'+url+'"></embed></object>';
			$('#banner_preview').html(output);

			$('#banner_preview').before('<div class="bannerlid-info-panel"><strong>Warning! </strong>Selecting SWF files can cause performance and compatibility issues on your site</div>');
		
		} else {
			$('.bannerlid-info-panel').remove();
			$('#banner_preview').html('<img src="'+url+'" alt="Preview" style="max-width: 350;" />');
		}
	}

	/**
	* Apply click handler to our add media button(s)
	*/
    $(document).ready(function(){

        $('#add-media').click(open_media_window);
        $('#file_url').on('change', function(){
        	updateBannerPreview($(this).val());
        })
    });
 
});