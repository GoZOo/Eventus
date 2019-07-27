import EventusFront from "./_eventusFront"

class EventusFront_Results extends EventusFront {
    constructor() {
        super()

        this.widgets = this.getA('div.eventus_results')

        //Init buttons for each widgets
        this.widgets.forEach((x, i) =>
            x.querySelectorAll('div.club').forEach(y =>
                y.querySelectorAll('button.next-club').forEach(z =>
                    z.addEventListener('click', () => this.nextClub(i))
                )
            )
        )

        //Resize widget on window resize
        window.addEventListener("resize", () => {
            //Resize a first time
            this.widgets.forEach((x, i) => this.resizeClub(i))
            //...and second time (in case of responsive issue)
            setTimeout(() => this.widgets.forEach((x, i) => this.resizeClub(i)), 1000)
        })
        window.dispatchEvent(new Event('resize'))
    }

    //Go to next club
    nextClub(numWidget) {
        const { widget, clubs } = this.getWidget(numWidget)

        this.resizeClub(numWidget)

        widget.querySelectorAll('button.next-club').forEach(x => x.disabled = true) //Disable buttons

        //Move clubs from right to left
        clubs.forEach(x => {
            let pos = x.style.right
            pos = pos.substring(0, pos.length - 1) //Get position of each club
            x.style.right = `${(parseInt(pos) + 100)}%` //Change position
            if (x.style.right === '100%') x.style.opacity = '0' //Previous club disapear
        })

        //Put club on left back to right
        setTimeout(() => {
            clubs.forEach(x => {
                if (x.style.right === '100%') {
                    x.style.transition = 'none'
                    x.style.right = `-${((clubs.length - 1) * 100)}%`
                }
                x.style.opacity = '1'
            })
            this.getA('button.next-club').forEach(x => x.disabled = false) //Enable buttons
        }, 500)
    }

    resizeClub(numWidget) {
        const { widget, clubs } = this.getWidget(numWidget)

        //Get club to display and next one
        let block1 = null
        let block2 = null

        clubs.forEach(x => {
            x.style.transition = ''
            if (x.style.right === '0%') block1 = x
            if (x.style.right === '-100%') block2 = x
        })

        //Change size of widget : get heighest size by current and next one
        widget.style.height = block1.offsetHeight > block2.offsetHeight ? `${block1.offsetHeight}px` : `${block2.offsetHeight}px`

        //Then, put back correct size of current 
        setTimeout(() => {
            clubs.forEach(x => {
                x.style.transition = ''
                if (x.style.right == "0%") widget.style.height = `${x.offsetHeight}px`
            })
        }, 500)
    }

    getWidget(numWidget) {
        return {
            widget: this.widgets[numWidget], //Get widget
            clubs: this.widgets[numWidget].querySelectorAll('.club') //Get clubs in widget
        }
    }
}

window.temp = new EventusFront_Results()