import Eventus from "../_eventus"

class Eventus_SeekerScreen extends Eventus {
	constructor(translations) {
		super(translations)

		this.selectClub = this.get('form select#club')

		this.selectClub.addEventListener('change', () =>
			this.get('button[value="eventus_seek"]').style.display = this.selectClub.value ? 'block' : 'none'
		)

		this.selectClub.dispatchEvent(new Event('change'))
	}
}
new Eventus_SeekerScreen(translations)