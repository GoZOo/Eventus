import Eventus from "../_eventus"

class Eventus_TeamDetailScreen extends Eventus {
	constructor(translations) {
		super(translations)

		this.urlOne = this.get('#urlOne')
		this.urlTwo = this.get('#urlTwo')
		this.rowrlOne = this.get('tbody tr:nth-child(4)')
		this.rowrlTwo = this.get('tbody tr:nth-child(5)')

		this.urlOne.addEventListener('change', () => {
			//Put content of input into <a>
			this.rowrlOne.querySelector('a').setAttribute('href', this.urlOne.value)
			//Display or no button if input has value
			this.rowrlOne.querySelector('a').style.display = this.urlOne.value ? 'inline-block' : 'none'
			//Check first url input, to display the second
			this.rowrlTwo.style.display = this.urlOne.value || this.urlTwo.value ? 'table-row' : 'none'
		})

		this.urlTwo.addEventListener('change', () => {
			//Put content of input into <a>
			this.rowrlTwo.querySelector('a').setAttribute('href', this.urlTwo.value)
			//Display or no button if input has value
			this.rowrlTwo.querySelector('a').style.display = this.urlTwo.value ? 'inline-block' : 'none'
			//Check first url input, to display the second
			this.rowrlTwo.style.display = this.urlOne.value || this.urlTwo.value ? 'table-row' : 'none'
		})

		this.urlOne.dispatchEvent(new Event('change'))
		this.urlTwo.dispatchEvent(new Event('change'))
	}
}
new Eventus_TeamDetailScreen(translations)