jQuery("#totalClub").val(jQuery('.clubModel').length);
function addClub() {
    var nbrClub = parseInt(jQuery("#totalClub").val())+1;
    jQuery("#totalClub").val(nbrClub);

    jQuery('.clubModel:eq(0)').clone().insertBefore("#totalClub");

    jQuery('.clubModel:eq('+(nbrClub-1)+') [data-name]').each(function(i,el) {        
        if (jQuery(el).is("h2")){
            jQuery(el).text(jQuery(el).attr("data-name")+" "+nbrClub);
        } else if (jQuery(el).is("label")){
            jQuery(el).attr('for', jQuery(el).attr("data-name")+nbrClub);
        } else if (jQuery(el).is("input[type='text'], input[type='hidden']")){
            jQuery(el).attr('name', jQuery(el).attr("data-name")+nbrClub);
            jQuery(el).attr('id', jQuery(el).attr("data-name")+nbrClub);
            jQuery(el).attr('value', "");
        } else if (jQuery(el).is("input[type='checkbox']")){
            jQuery(el).attr('name', jQuery(el).attr("data-name")+nbrClub);
            jQuery(el).removeAttr('checked');
        }
    });	

	if (nbrClub > 1) {
        jQuery("#supprClub").css("display", "inline-block");
	} 
    if (nbrClub >= 5) {
        jQuery("#ajouterClub").css("display", "none"); 
	}
}

function delClub() {
	var nbrClub = parseInt(jQuery("#totalClub").val());

    jQuery('.clubModel:eq('+(nbrClub-1)+')').remove();

    nbrClub--;
    jQuery("#totalClub").val(nbrClub);
	
	if (nbrClub<=1) {
        jQuery("#supprClub").css("display", "none");
	} 
	if (nbrClub<5) {
        jQuery("#ajouterClub").css("display", "inline-block"); 
	}		 
}