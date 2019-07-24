import Eventus from "../_eventus"

class Eventus_MatchScreen extends Eventus {
	constructor() {
		super()

		this.getA('table.parentMatches tr').forEach(x => {
			let classes = [...this.getA('table.sonMatches tr')].map(y => y.className).filter(z => z)
			if (classes.length > 0 && classes.includes(x.className)) x.querySelector('button').style.display = 'none'
		})
	}

	//Add new Son match
	createSonMatch(id) {
		//Display son match
		this.get('table.sonMatches').style.display = 'block'
		this.get('br.sonMatches').style.display = 'inline-block'

		//Clone match
		let clone = this.get(`table.parentMatches .parent-${id}`).cloneNode(true)
		clone.classList.remove(`parent-${id}`)
		clone.classList.add(`son-${id}`)
		this.get('table.sonMatches tbody').appendChild(clone)

		//Erease id element
		this.getA('table.sonMatches tr:last-child input[data-name="idSon"]').forEach(x => x.setAttribute('value', ''))

		//Element can be edited 
		this.getA('table.sonMatches tr:last-child td input').forEach(x => {
			x.removeAttribute('disabled')
			//Simple workearound for firefox
			let type = x.getAttribute('type')
			x.setAttribute('type', 'text')
			x.setAttribute('type', type)
		})

		//Set actions
		this.getA('table.sonMatches tr:last-child td button').forEach(x => {
			x.setAttribute('title', 'Supprimer le match')
			x.setAttribute('onclick', `deleMatch(${id},'sonMatches')`)
		})

		//Switch button to display
		this.getA('table.sonMatches tr:last-child td button *').forEach(x => x.style.display = x.className === 'edit' ? 'none' : 'inline-block')

		//Hide button to create a new son match
		this.get(`table.parentMatches .parent-${id} button`).style.display = 'none'

		this.updateRow("sonMatches")
	}

	/* Update Son/Other match */
	updateRow(type) {
		this.getA(`table.${type} tr`).forEach((x, i) => {
			x.querySelectorAll('[data-name]').forEach(z => {
				z.setAttribute('name', `${type}[${i}][${z.getAttribute('data-name')}]`)
			})
		})
	}
}

window.eventusMatchScreen = new Eventus_MatchScreen()


// //Init rows
// updateRow("sonMatches");
// updateRow("otherMatches");

// //Init : hide button te create child son if it already exist
// for (var i = 0; i < jQuery("table.parentMatches tr").length; i++) {
// 	for (var j = 0; j < jQuery("table.sonMatches tr").length; j++) {
// 		if (jQuery("table.parentMatches tr:eq(" + i + ")").attr("class") == jQuery("table.sonMatches tr:eq(" + j + ")").attr("class")) {
// 			jQuery("table.parentMatches tr:eq(" + i + ") button").css("display", "none");
// 		}
// 	}
// }

// /* Add new Son match */
// function editMatch(id) {
// 	//Display son match
// 	jQuery('.sonMatches').css('display', 'block');
// 	jQuery('br.sonMatches').css('display', 'inline-block');

// 	//Clone match
// 	jQuery("table.sonMatches").append(jQuery("table.parentMatches ." + id).clone());

// 	//Erease id element
// 	jQuery('table.sonMatches tr:last-child input[data-name="idSon"]').each(function (i, el) {
// 		jQuery(el).attr("value", "");
// 	});

// 	//Element can be edited 
// 	jQuery('table.sonMatches tr:last-child td input').each(function (i, el) {
// 		jQuery(el).removeAttr("disabled");
// 		//Simple workearound for firefox
// 		let type = jQuery(el).attr("type");
// 		jQuery(el).attr("type", "text").attr("type", type);
// 	});

// 	//Set actions
// 	jQuery('table.sonMatches tr:last-child td button').each(function (i, el) {
// 		jQuery(el).attr("title", "Supprimer le match");
// 		jQuery(el).attr("onclick", 'deleMatch(' + id + ', "sonMatches")');
// 	});

// 	//Switch button to display
// 	jQuery('table.sonMatches tr:last-child td button div').each(function (i, el) {
// 		if (jQuery(el).hasClass("edit")) {
// 			jQuery(el).css("display", "none");
// 		}
// 		if (jQuery(el).hasClass("delete")) {
// 			jQuery(el).css("display", "inline-block");
// 		}
// 	});

// 	//Hide button to create a new son match
// 	jQuery("table.parentMatches ." + id + " button").css("display", "none");

// 	updateRow("sonMatches");
// }

// /* Add new Other match */
// function addOtherMatch() {
// 	var tempId = Math.random().toString(36).substr(2, 9);

// 	jQuery("table.otherMatches").append(jQuery("table.otherMatches tr:eq(1)").clone());

// 	jQuery('table.otherMatches tr:last-child').each(function (i, el) {
// 		jQuery(el).attr("class", tempId);
// 	});
// 	jQuery('table.otherMatches tr:last-child input').each(function (i, el) {
// 		jQuery(el).attr("value", "");
// 	});
// 	jQuery('table.otherMatches tr:last-child td button').each(function (i, el) {
// 		jQuery(el).attr("onclick", 'deleMatch("' + tempId + '", "otherMatches")');
// 	});
// 	jQuery('table.otherMatches').css('display', 'block');

// 	updateRow("otherMatches");
// }

// /* Del Son/Other match */
// function deleMatch(id, type) {
// 	if (type == "sonMatches") {

// 		jQuery('table.sonMatches .' + id).remove();

// 		jQuery('table.parentMatches .' + id + ' button').css("display", "inline-block");

// 		if (jQuery('table.sonMatches tr').length < 2) {
// 			jQuery('.sonMatches').css('display', 'none');
// 		}

// 		updateRow("sonMatches");
// 	} else if (type == "otherMatches") {

// 		if (jQuery('table.otherMatches tr').length == 2) {
// 			jQuery('table.otherMatches tr input').each(function (i, el) {
// 				jQuery(el).attr("value", "");
// 			});
// 		} else {
// 			jQuery('table.otherMatches .' + id).remove();
// 		}

// 		updateRow("otherMatches");
// 	}
// }

// /* Update Son/Other match */
// function updateRow(type) {
// 	[...document.querySelectorAll(`table.${type} tr`)].map((x, i) => {
// 		[...x.querySelectorAll('[data-name]')].map(z => {
// 			z.setAttribute('name', `${type}[${i}][${z.getAttribute('data-name')}]`)
// 		})
// 	})
// }

