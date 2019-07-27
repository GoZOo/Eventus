export default class EventusFront {
    constructor() {
    }

    //Get element ref from dom
    get(str) {
        return document.querySelector(str)
    }

    //Get elements refs from dom
    getA(str) {
        return document.querySelectorAll(str)
    }
}