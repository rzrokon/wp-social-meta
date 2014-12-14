jQuery(document).ready(function($) {
	
	$('.docopy-slides').click(function(){
		
		var slide_count = $('#slide_count').val();
		slide_count++;
		var add_slide_markup = "<br><div class='title_boost'><br><div class='labelclass'>Slide <span class='slide_number'>"+slide_count+"</span></div><input readonly='readonly' id='img_url"+slide_count+"' value='' name='surface_slide_image"+slide_count+"'  class='kp_input_box' type='hidden'><input title='Upload' onclick='register_upload_button_event(jQuery(this));' class='kp_button_upload button' value='Add Image' type='button'><span style='padding-left:10px;'></span><input title='Remove' onclick='register_remove_button_event(jQuery(this));' class='kp_button_remove button' value='Remove Image' type='button'><img class='image_preview' style='max-width:300px; display:block; clear:both; margin-top:10px;' src='' title='Image URL' alt=''/></div>";
		$('.slide_images').append(add_slide_markup);
		$('#slide_count').val(slide_count);
		return false;

	});
});