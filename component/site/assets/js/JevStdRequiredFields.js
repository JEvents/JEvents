/* 
 * Filename: JevStdRequiredFields.js
 * Class: jevstdrequiredfields
 * Author: Carlos M. Cámara from JEvents.net
 * Use: Check required fields
 */

var JevStdRequiredFields = {
	fields: new Array(),
	verify: function(form) {
		valid = true;
		
		// make sure a MooTools item
		form = $(form);
		var messages = new Array();
		JevStdRequiredFields.fields.each(function(item, i) {
			name = item.name;
			// should we skip this test because of category restrictions?
			if (typeof (JevrCategoryFields) != 'undefined' && JevrCategoryFields.skipVerify(name))
				return;
			var matches = new Array();
			Array.from(form.elements).slice().each(function(testitem, testi) {
				if (testitem.name == name || "custom_" + testitem.name == name || testitem.id == name || ("#" + testitem.id) == name || $(testitem).hasClass(name.substr(1))) {
					matches.push(testitem);
				}
			});
			var value = "";
			if (matches.length == 1) {
				value = matches[0].value;
			}
			// A set of radio checkboxes
			else if (matches.length > 1) {
				matches.each(function(match, index) {
					if (match.checked)
						value = match.value;
				});
			}
			//if (elem) elem.value = item.value;
			if (value == item['default'] || value == "") {
				valid = false;
				// TODO add message together
				if (item.reqmsg != "") {
					messages.push(item.reqmsg);
				}
			}
		});
		if (!valid) {
			message = "";
			messages.each(function(msg, index) {
				message += msg + "\n";
			});
			alert(message);
		}
		return valid;
	}
}