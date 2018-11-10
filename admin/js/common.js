function validate(string){
    return confirm((string ? string : 'Cette action est iréversible. Voulez-vous vraiment supprimer l\élément ?')) ? true : false;
}

function setLoading(btn){
    jQuery('.wrap button').css({ 'pointer-events' : 'none', 'cursor': 'not-allowed'});
    jQuery(btn).addClass('ico-loading');
    jQuery(btn).html('Chargement en cours...');
    jQuery(btn).blur();
}
