// Button delete image 
if(!jQuery('#image_attachment_id').val()){
	jQuery('#delete_image_button').attr('disabled', 'true');
} else {	
	jQuery('#delete_image_button').removeAttr('disabled');
}
jQuery('#delete_image_button').click(()=>{
	jQuery('#image_attachment_id').val("");
	jQuery('#delete_image_button').attr('disabled', 'true');
});

//Second link url
setUrls();

jQuery('#urlOne').on('change', ()=> {
	setUrls();
});

function setUrls(){
	if (jQuery('#urlOne').val() || jQuery('#urlTwo').val()){
		jQuery('tbody tr:eq(4)').css({'display': 'table-row'});
	} else {
		jQuery('tbody tr:eq(4)').css({'display': 'none'});
	}	
}