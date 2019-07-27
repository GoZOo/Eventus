import Eventus from "../_eventus"

class Eventus_AdminScreen extends Eventus {
	constructor(translations) {
		super(translations)
		this.inputMail = this.get('#emailNotif')

		//Listener to display choice reset logs
		this.inputMail.addEventListener('change', () =>
			this.get('tbody tr:nth-child(4)').style.display = this.inputMail.value ? 'table-row' : 'none'
		)
		this.inputMail.dispatchEvent(new Event('change'))
	}
}
new Eventus_AdminScreen(translations)