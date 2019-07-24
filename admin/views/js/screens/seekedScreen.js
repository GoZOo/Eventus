import Eventus from "../_parent"

class Eventus_SeekedScreen extends Eventus {
	constructor() {
		super()

		this.checkbox = this.get("table.seekedTable thead tr th input[type='checkbox']")
		this.checkboxes = this.getA("table.seekedTable tbody tr td input[type='checkbox']")

		//If main checkbox is checked, each are checked
		this.checkbox.addEventListener('change', () => this.checkboxes.forEach(x => x.checked = this.checkbox.checked))

		//If one checkboxes is unchecked, main also
		this.checkboxes.forEach(x => x.addEventListener('change', () => this.checkbox.checked = false))		
	}
}
new Eventus_SeekedScreen()