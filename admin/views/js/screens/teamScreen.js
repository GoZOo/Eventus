import Eventus from "../_eventus"

class Eventus_TeamDetailScreen extends Eventus {
	constructor() {
		super()

		this.urlOne = this.get('#urlOne')
		this.urlTwo = this.get('#urlTwo')
		this.rowrlOne = this.get('tbody tr:nth-child(4)')
		this.rowrlTwo = this.get('tbody tr:nth-child(5)')

		this.urlOne.addEventListener('change', (el) => {
			//Put content of input into <a>
			this.rowrlOne.querySelector('a').setAttribute('href', this.urlOne.value)
			this.displayRowTwo()
		})

		this.urlTwo.addEventListener('change', () => {
			//Put content of input into <a>
			this.rowrlTwo.querySelector('a').setAttribute('href', this.urlTwo.value)
			this.displayRowTwo()
		})

		this.displayRowTwo()
	}

	//Check first url input, to display the second
	displayRowTwo() {
		this.rowrlTwo.style.display = this.urlOne.value || this.urlTwo.value ? 'table-row' : 'none'
	}
}
new Eventus_TeamDetailScreen()