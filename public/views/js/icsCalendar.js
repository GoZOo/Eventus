import EventusFront from "./_eventusFront.js"

class EventusFront_IcsCalendar extends EventusFront {
    constructor() {
        super()

        //Get both buttons
        this.buttonTwo = this.getA('.rowIcs a:nth-child(2)')

        this.buttonTwo.forEach((element)  => {

            //Settings for second button
            element.removeAttribute('href')
            element.style.cursor = 'pointer'

            //Listener click on second button
            element.addEventListener('click', () => {
                let dummy = document.createElement('input')
                document.body.appendChild(dummy)
                dummy.setAttribute('value', element.parentNode.querySelector('a:nth-child(1)').href)
                dummy.select()
                document.execCommand('copy')
                document.body.removeChild(dummy)
                element.parentNode.parentNode.querySelector('#succes-copy-ics').style.display = 'block'
            })
        });
    }
}
new EventusFront_IcsCalendar()