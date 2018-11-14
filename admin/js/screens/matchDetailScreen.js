updateRow("sonMatches");
updateRow("otherMatches");
for (var i = 0; i < jQuery("table.parentMatches tr").length; i++) {
	for (var j = 0; j < jQuery("table.sonMatches tr").length; j++) {
		if (jQuery("table.parentMatches tr:eq(" + i + ")").attr("class") == jQuery("table.sonMatches tr:eq(" + j + ")").attr("class")) {
			jQuery("table.parentMatches tr:eq(" + i + ") button").css("display", "none");
		}
	}
}

/* Add new Son match */
function editMatch(id) {
	jQuery('.sonMatches').css('display', 'block');
	jQuery('br.sonMatches').css('display', 'inline-block');

	jQuery("table.sonMatches").append(jQuery("table.parentMatches ." + id).clone());

	jQuery('table.sonMatches tr:last-child input[data-name="idSon"]').each(function (i, el) {
		jQuery(el).attr("value", "");
	});
	jQuery('table.sonMatches tr:last-child td input').each(function (i, el) {
		jQuery(el).removeAttr("disabled");
	});
	jQuery('table.sonMatches tr:last-child td button').each(function (i, el) {
		jQuery(el).attr("title", "Supprimer le match");
		jQuery(el).attr("onclick", 'deleMatch(' + id + ', "sonMatches")');
	});
	jQuery('table.sonMatches tr:last-child td button div').each(function (i, el) {
		if (jQuery(el).hasClass("edit")) {
			jQuery(el).css("display", "none");
		}
		if (jQuery(el).hasClass("delete")) {
			jQuery(el).css("display", "inline-block");
		}
	});

	jQuery("table.parentMatches ." + id + " button").css("display", "none");

	jQuery("input[name='nbrSonMatch']").attr("value", parseInt(jQuery("input[name='nbrSonMatch']").attr("value")) + 1);

	updateRow("sonMatches");
}

/* Add new Other match */
function addOtherMatch() {
	jQuery("input[name='nbrOtherMatch']").attr("value", parseInt(jQuery("input[name='nbrOtherMatch']").attr("value")) + 1);

	var tempId = Math.random().toString(36).substr(2, 9);

	jQuery("table.otherMatches").append(jQuery("table.otherMatches tr:eq(1)").clone());

	jQuery('table.otherMatches tr:last-child').each(function (i, el) {
		jQuery(el).attr("class", tempId);
	});
	jQuery('table.otherMatches tr:last-child input').each(function (i, el) {
		jQuery(el).attr("value", "");
	});
	jQuery('table.otherMatches tr:last-child td button').each(function (i, el) {
		jQuery(el).attr("onclick", 'deleMatch("' + tempId + '", "otherMatches")');
	});
	jQuery('table.otherMatches').css('display', 'block');

	updateRow("otherMatches");
}

/* Del Son/Other match */
function deleMatch(id, type) {
	if (type == "sonMatches") {
		jQuery("input[name='nbrSonMatch']").attr("value", parseInt(jQuery("input[name='nbrSonMatch']").attr("value")) - 1);

		jQuery('table.sonMatches .' + id).remove();

		jQuery('table.parentMatches .' + id + ' button').css("display", "inline-block");

		if (jQuery('table.sonMatches tr').length < 2) {
			jQuery('.sonMatches').css('display', 'none');
		}

		updateRow("sonMatches");
	} else if (type == "otherMatches") {
		jQuery("input[name='nbrOtherMatch']").attr("value", parseInt(jQuery("input[name='nbrOtherMatch']").attr("value")) - 1);

		if (jQuery('table.otherMatches tr').length == 2) {
			jQuery('table.otherMatches tr input').each(function (i, el) {
				jQuery(el).attr("value", "");
			});
		} else {
			jQuery('table.otherMatches .' + id).remove();
		}

		updateRow("otherMatches");
	}

}

/* Update Son/Other match */
function updateRow(type) {
	for (var i = 0; i < jQuery('table.' + type + ' tr').length; i++) {
		for (var k = 0; k < jQuery("table." + type + " tr:eq(" + i + ") > [data-name]").length; k++) {
			jQuery("table." + type + " tr:eq(" + i + ") > [data-name]:eq(" + k + ")").attr('name', jQuery("table." + type + " tr:eq(" + i + ") > [data-name]:eq(" + k + ")").attr("data-name") + i);
		}
		for (var j = 0; j < jQuery('table.' + type + ' tr:eq(' + i + ') td').length; j++) {
			jQuery("table." + type + " tr:eq(" + i + ") td:eq(" + j + ") [data-name]").attr('name', jQuery("table." + type + " tr:eq(" + i + ") td:eq(" + j + ") [data-name]").attr("data-name") + i);
		}
	}
}

