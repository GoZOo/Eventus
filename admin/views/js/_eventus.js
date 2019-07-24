export default class Eventus {
    constructor(translations) {
        this.translations = translations
    }

    //Get element ref from dom
    get(str) {
        return document.querySelector(str)
    }

    //Get elements refs from dom
    getA(str) {
        return document.querySelectorAll(str)
    }

    //Pop validation on sum buttons
    validate(string) {
        return confirm((string ? string : this.translations.defMessage)) ? true : false;
    }

    //Add loading to button
    setLoading(btn) {
        this.get('*').style.pointerEvents = 'none'
        this.get('*').style.cursor = 'not-allowed'
        btn.classList.add('ico-loading')
        btn.innerHTML = this.translations.loading || 'Loading'
        btn.blur()
    }
}

window.eventus = new Eventus(translations)