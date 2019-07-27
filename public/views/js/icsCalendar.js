import EventusFront from "./_eventusFront"

class EventusFront_IcsCalendar extends EventusFront {
    constructor() {
        super()

        //Get both buttons
        this.buttonOne = this.get('.rowIcs a:nth-child(1)')
        this.buttonTwo = this.get('.rowIcs a:nth-child(2)')

        //Settings for second button
        this.buttonTwo.removeAttribute('href')
        this.buttonTwo.style.cursor = 'pointer'

        //Listener click on second button
        this.buttonTwo.addEventListener('click', () => {
            this.get('#succes-copy-ics').style.display = 'block'
            let dummy = document.createElement('input')
            document.body.appendChild(dummy)
            dummy.setAttribute('value', this.buttonOne.href)
            dummy.select()
            document.execCommand('copy')
            document.body.removeChild(dummy)
        })
    }
}
new EventusFront_IcsCalendar()