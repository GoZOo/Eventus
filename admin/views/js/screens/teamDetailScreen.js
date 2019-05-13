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

//Set link in button
jQuery('input[id*="url"]').on('change', ()=> {
	if (jQuery('#urlOne').val()){
		jQuery('tbody tr:eq(3) button').attr('onclick', "window.open('"+ jQuery('tbody tr:eq(3) input').val() +"', '_blank')");		
	}
	
	if (jQuery('#urlTwo').val()){
		jQuery('tbody tr:eq(4) button').attr('onclick', "window.open('"+ jQuery('tbody tr:eq(4) input').val() +"', '_blank')");
	} 
});