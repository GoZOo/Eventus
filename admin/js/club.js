//Systeme to force user select at least one checkbox
jQuery('tr:last-child input[type=checkbox]').get(0).setCustomValidity("Veuillez cocher une de ces cases si vous désirez poursuivre.");
jQuery('tr:last-child input[type=checkbox]').change(() => {
    updateStateCheckbox();
});

function updateStateCheckbox(){
    if(jQuery("tr:last-child input[type=checkbox]:checked").length){
        jQuery("tr:last-child input[type=checkbox]").removeAttr('required');
        jQuery('tr:last-child input[type=checkbox]').get(0).setCustomValidity("");
    } else {        
        jQuery("tr:last-child input[type=checkbox]").attr('required', true);
        jQuery('tr:last-child input[type=checkbox]').get(0).setCustomValidity("Veuillez cocher une de ces cases si vous désirez poursuivre.");
    }
}

updateStateCheckbox();