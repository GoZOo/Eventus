import Eventus from "../_eventus"

class Eventus_MatchScreen extends Eventus {
	constructor() {
		super()

		//Init to hide button for parents matches when son exists
		this.getA('table.parentMatches tr').forEach(x => {
			let classes = [...this.getA('table.sonMatches tr')].map(y => y.className).filter(z => z)
			if (classes.length > 0 && classes.includes(x.className)) x.querySelector('button').style.display = 'none'			
		})

		//Init name for inputs
		this.updateRow('sonMatches')
		this.updateRow('otherMatches')

		this.getA('table button').forEach(x => x.removeAttribute('disabled')) //Allow user to interact
	}

	//Add new Son match
	createSonMatch(id) {
		this.getA('.sonMatches').forEach(x => x.style.display = x.tagName === "BR" ? 'inline-block' : 'block') //Display son match table

		let clone = this.get(`table.parentMatches .${CSS.escape(id)}`).cloneNode(true) //Clone match to be added in son matches

		clone.querySelector('input[data-name="idSon"]').value = '' //Clear id element (herited from parent)

		//Element can be edited 
		clone.querySelectorAll('td input').forEach(x => {
			x.removeAttribute('disabled')
			//Simple workearound for firefox
			let type = x.getAttribute('type')
			x.setAttribute('type', 'text')
			x.setAttribute('type', type)
		})
		clone.querySelector('td button').setAttribute('title', 'Supprimer le match') //Set action
		clone.querySelector('td button').setAttribute('onclick', `window.eventusMatchScreen.removeMatch('${id}', 'sonMatches')`)
		clone.querySelectorAll('td button *').forEach(x => x.style.display = x.className === 'edit' ? 'none' : 'inline-block') //Switch button to display		
		this.get('table.sonMatches tbody').appendChild(clone) //Add clone

		this.get(`table.parentMatches .${CSS.escape(id)} button`).style.display = 'none' //Hide button to create a new son match

		this.updateRow('sonMatches')
	}

	//Add an other match
	addOtherMatch() {
		this.get('table.otherMatches').style.display = 'block' //Display son match table

		let tempId = Math.random().toString(36).substr(2, 9) //Generate temp id

		let clone = this.get('table.otherMatches tbody tr:nth-child(1)').cloneNode(true) //Clone an other match		
		clone.classList.add(tempId) //Set temp Id		
		clone.querySelectorAll('input').forEach(x => x.value = '') //Clear value input
		clone.querySelector('td button').setAttribute('onclick', `window.eventusMatchScreen.removeMatch('${tempId}', 'otherMatches')`) //Set action
		this.get('table.otherMatches tbody').appendChild(clone)//Add clone

		this.updateRow('otherMatches')
	}

	// Remove a son or an other match
	removeMatch(id, type) {
		switch (type) {
			case 'sonMatches':
				this.get(`table.sonMatches .${CSS.escape(id)}`).remove() //Remove row

				this.get(`table.parentMatches .${CSS.escape(id)} button`).style.display = 'inline-block' //Button to create child is visible

				if (this.getA('table.sonMatches tbody tr').length < 1) this.getA('.sonMatches').forEach(x => x.style.display = 'none') //If no more son matches, remove table

				break
			case 'otherMatches':
				if (this.getA('table.otherMatches tbody tr').length === 1) { //If only one row					
					this.getA('table.otherMatches tbody tr input').forEach(x => x.value = '') //Clear value input
				} else {
					this.get(`table.otherMatches .${CSS.escape(id)}`).remove() //Remove row
				}

				break
			default:
				break
		}

		this.updateRow(type)
	}

	//Update all inputs name for a given type 
	updateRow(type) {
		this.getA(`table.${type} tr`).forEach((x, i) => {
			x.querySelectorAll('[data-name]').forEach(z => {
				z.setAttribute('name', `${type}[${i}][${z.getAttribute('data-name')}]`)
			})
		})
	}
}

window.eventusMatchScreen = new Eventus_MatchScreen()
