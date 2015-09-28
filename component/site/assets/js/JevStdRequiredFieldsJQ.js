/* 
 * Filename: JevStdRequiredFieldsJQ.js
 * Class: jevstdrequiredfields
 * Author: Carlos M. Cámara from JEvents.net
 * Use: Check required fields
 */

var JevStdRequiredFields = {
	fields: new Array(),
	verify: function(form) {
		valid = true;
		
		form = jevjq(form);
		var messages = new Array();
		// This is a Javascript each over an array !
		JevStdRequiredFields.fields.each(function(item, i) {
			name = item.name;
			// should we skip this test because of category restrictions?
			if (typeof (JevrCategoryFields) != 'undefined' && JevrCategoryFields.skipVerify(name))
				return;
			var matches = new Array();
			/*
			form.serializeArray().forEach( function(  testitem, testi) {
				if (testitem.name == name || "custom_" + testitem.name == name || (testitem.id && testitem.id == name) || ("#" + testitem.id) == name || jevjq(testitem).hasClass(name.substr(1))) {
					matches.push(testitem);
				}
			});
			*/
			// Problem with multiple select is you may get the same element more than once!
			//var testitem = form.find("[name="+name+"]" , "[name='"+name+"']" ,"[name='custom_"+name+"']",  "#"+name, "."+name.substr(1));
			if (form.find("[name='"+name+"']").length){
				matches.push(form.find("[name='"+name+"']"));
			}
			else if ( form.find("[name='custom_"+name+"']").length){
				matches.push(form.find("[name='custom_"+name+"']"));
			}
			else if (form.find("#"+name).length ) {
				matches.push(form.find("#"+name));
			}
			else if (form.find("."+name.substr(1)).length ){
				matches.push(form.find("."+name.substr(1)) );
			}
			
			var value = "";
			if (matches.length == 1) {				
				value = matches[0].val();
				if (typeof value == "undefined" || value == null ){
					value = "";
				}
			}
			// A set of radio checkboxes
			else if (matches.length > 1) {
				matches.forEach(function(match, index) {
					if (match.checked)
						value = match.val();
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
			messages.forEach(function(msg, index) {
				message += msg + "\n";
			});
			alert(message);
		}
		return valid;
	}
}