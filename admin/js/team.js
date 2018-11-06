// Button radio 
changeSexe();

jQuery('#club').on('change', ()=> {
	jQuery('input[type="radio"]').prop('checked', false);
	changeSexe();
});

function changeSexe(){
	if (jQuery('#club').find(":selected").attr('data-boy') == 1){
		jQuery('input[type="radio"]:eq(0)').removeAttr('disabled');
	} else {
		jQuery('input[type="radio"]:eq(0)').attr('disabled', 'true');
		jQuery('input[type="radio"]:eq(0)').attr('checked', false);
	}

	if (jQuery('#club').find(":selected").attr('data-girl') == 1){
		jQuery('input[type="radio"]:eq(1)').removeAttr('disabled');
	} else {
		jQuery('input[type="radio"]:eq(1)').attr('disabled', 'true');
		jQuery('input[type="radio"]:eq(1)').attr('checked', false);
	}

	if (jQuery('#club').find(":selected").attr('data-mixed') == 1){
		jQuery('input[type="radio"]:eq(2)').removeAttr('disabled');
	} else {
		jQuery('input[type="radio"]:eq(2)').attr('disabled', 'true');
		jQuery('input[type="radio"]:eq(2)').attr('checked', false);
	}
}

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