//Show reset log option
showResetLogs();

jQuery('#emailNotif').on('change', ()=> {
	showResetLogs();
});

function showResetLogs(){
	if (jQuery('#emailNotif').val()){
		jQuery('tbody tr:eq(2)').css({'display': 'table-row'});
	} else {
		jQuery('tbody tr:eq(2)').css({'display': 'none'});
	}	
}