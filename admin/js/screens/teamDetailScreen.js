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