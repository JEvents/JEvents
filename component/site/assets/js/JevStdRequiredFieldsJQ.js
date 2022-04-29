/*
 * Filename: JevStdRequiredFieldsJQ.js
 * Class: jevstdrequiredfields
 * Author: Carlos M. Cámara from JEvents.net
 * Use: Check required fields
 */

var JevStdRequiredFields = {
    fields: [],
    verify: function (form) {
        valid = true;

        // form = jevjq(form);
        var messages = [];
        // This is a Javascript each over an array !
        JevStdRequiredFields.fields.forEach(function (item, i) {
            var name = item.name;
            var value = "";

            // deprecated usage - phase out!
            if (typeof item.preAction === 'string' || typeof item.getValue === 'string') {
                if (item.preAction) {
                    try {
                        eval(item.preAction);
                    } catch (ex) {
                        //alert(ex.message);
                    }
                }
                if (item.getValue) {
                    try {
                        value = eval(item.getValue);
                    } catch (e) {
                        alert("failed " + e.message);

                    }
                }
            }
            if (typeof item.preAction === 'function') {
                try {
                    item.preAction();
                }
                catch (ex) {
                    console.log('preAction failed : ' + ex.message + ' \n\nfunction is ' + item.preAction);
                }
            }
            if (typeof item.getValue === 'function') {
                try {
                    value = item.getValue();
                }
                catch (e) {
                    console.log("getValue failed " + e.message + ' \n\nfunction is ' + item.getValue);
                }
            }

            var noncustomname = name.replace("custom_jform", "jform");
            // to test field id we must NOT have [ or ] in the name
            var nosquarename = name.replace(/\[/g, "");
            nosquarename = nosquarename.replace(/\]/g, "");
            // custm fields checkbox test - must replace [] and custom_
            var checkboxname = name.replace(/\[\]/g, "");
            if (checkboxname == name) {
                checkboxname = "ThisIsToBlockAction" + checkboxname;
            }
            checkboxname = checkboxname.replace("custom_jform", "jform");

            // should we skip this test because of category restrictions?
            if (typeof (JevrCategoryFields) != 'undefined' && JevrCategoryFields.skipVerify(name))
                return;
            var matches = [];
            // Problem with multiple select is you may get the same element more than once!
            if (form.querySelector("[name='" + name + "']")) {
                matches.push(form.querySelector("[name='" + name + "']"));
            } else if (form.querySelector("[name='custom_" + name + "']")) {
                matches.push(form.querySelector("[name='custom_" + name + "']"));
            }
            // must not have [] in field id
            else if (form.querySelector("#" + nosquarename)) {
                matches.push(form.querySelector("#" + nosquarename));
            }
            // must not have [] in field class name
            else if (form.querySelector("." + nosquarename.substr(1))) {
                matches.push(form.querySelector("." + nosquarename.substr(1)));
            }
            // names that start with the correct checkbox pattern
            else if (form.querySelectorAll("[name^='" + checkboxname + "']").length) {
                form.querySelectorAll("[name^='" + checkboxname + "']").forEach(function (ckbx ) {
                    matches.push(ckbx);
                });
            }
            // find other custom field elements (could be radio boxes so traverse the array)
            else if (form.querySelectorAll("[name='" + noncustomname + "']").length) {
                form.querySelectorAll("[name='" + noncustomname + "']").forEach(function (fld) {
                    matches.push(fld);
                });
            }

            if (matches.length == 1) {
                value = matches[0].value;
                if (typeof value == "undefined" || value == null) {
                    value = "";
                }
                // Joomla 4 editor can leave whitespaces e.g. \t in the value also some editors e.g. JCE don't push the changed
                if (typeof window.Joomla.editors.instances[name] !== 'undefined')
                {
                    value = window.Joomla.editors.instances[name].getValue();
                    value = value.trim();
                }
            }
            // A set of radio checkboxes
            else if (matches.length > 1) {
                matches.forEach(function (match, index) {
                    // match can be a DOM input element (not jQuery)
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
            messages.forEach(function (msg, index) {
                message += msg + "\n";
            });
            alert(message);
        }
        return valid;
    }
};
