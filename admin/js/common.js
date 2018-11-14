//Pop validation on sum buttons
function validate(string) {
    return confirm((string ? string : 'Cette action est iréversible. Voulez-vous vraiment supprimer l\élément ?')) ? true : false;
}

//Add loading to button
function setLoading(btn) {
    jQuery('.wrap button').css({ 'pointer-events': 'none', 'cursor': 'not-allowed' });
    jQuery(btn).addClass('ico-loading');
    jQuery(btn).html('Chargement en cours...');
    jQuery(btn).blur();
}

//Listener to open button link in new tab
jQuery("button.button-primary").on('mousedown', (e) => {
    if (e.which == 2 || e.which == 4) {
        e.preventDefault();
        let url = jQuery(e.currentTarget).attr('onclick');
        url = location.protocol + '//' + location.host + location.pathname + url.substring(url.indexOf("?"), url.lastIndexOf("'"))
        window.open(url, '_blank').focus();
    }
});