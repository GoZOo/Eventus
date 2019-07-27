export default class Eventus {
    constructor(translations) {
        this.translations = translations

        //Method listen by this class
        this.listeningTo = ['validate', 'setLoading', 'goBack']

        //Init listener
        this.listen()
    }

    //Setup
    listen() {
        this.getA('#wpbody-content button').forEach(el => {
            if (el.hasAttribute('data-bind')) {
                const { action, content } = eval(`(${el.getAttribute('data-bind')})`)
                if (this.listeningTo.includes(action)) el.addEventListener('click', this[action].bind(this, content))
            }
        })
    }

    //Get element ref from dom
    get(str) {
        return document.querySelector(str)
    }

    //Get elements refs from dom
    getA(str) {
        return document.querySelectorAll(str)
    }

    //Get a random id
    getRandId() {
        return Math.random().toString(36).substr(2, 9)
    }

    //Pop validation on sum buttons
    validate(string, ev) {
        return confirm((string ? string : this.translations.defMessage)) ? true : ev.preventDefault();
    }

    //Add loading to button
    setLoading(el) {
        this.get('*').style.pointerEvents = 'none'
        this.get('*').style.cursor = 'not-allowed'
        el.classList.add('ico-loading')
        el.innerHTML = this.translations.loading || 'Loading'
        el.blur()
    }

    //Go back in history
    goBack() {
        window.history.back()
    }
}