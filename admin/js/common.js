//Pop validation on sum buttons
function validate(string) {
    return confirm((string ? string : translations.defMessage)) ? true : false;
}
//Add loading to button
function setLoading(btn) {
    jQuery('button').css({ 'pointer-events': 'none', 'cursor': 'not-allowed' });
    jQuery('a').css({ 'pointer-events': 'none', 'cursor': 'not-allowed' });
    jQuery(btn).addClass('ico-loading');
    jQuery(btn).html(translations.loading);
    jQuery(btn).blur();
}