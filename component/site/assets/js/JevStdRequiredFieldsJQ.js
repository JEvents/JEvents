/* 
 * Filename: JevStdRequiredFieldsJQ.js
 * Class: jevstdrequiredfields
 * Author: Carlos M. Cámara from JEvents.net
 * Use: Check required fields
 */

var JevStdRequiredFields = {
    fields: new Array(),
    verify: function (form) {
        valid = true;

        form = jevjq(form);
        var messages = new Array();
        // This is a Javascript each over an array !
        JevStdRequiredFields.fields.forEach(function (item, i) {
            var name = item.name;
            var value = "";            
            if (item.preAction) {
                try {
                    eval(item.preAction);
                }
                catch (ex){
                    //alert(ex.message);
                }
            }
            if (item.getValue) {
                try {
                    value = eval(item.getValue);
                }
                catch (e){
                    alert("failed "+e.message);
                    
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
            if (form.find("[name='" + name + "']").length) {
                matches.push(form.find("[name='" + name + "']"));
            } else if (form.find("[name='custom_" + name + "']").length) {
                matches.push(form.find("[name='custom_" + name + "']"));
            }
            // must not have [] in field id
            else if (form.find("#" + nosquarename).length) {
                matches.push(form.find("#" + nosquarename));
            }
            // must not have [] in field class name
            else if (form.find("." + nosquarename.substr(1)).length) {
                matches.push(form.find("." + nosquarename.substr(1)));
            }
            // names that start with the correct checkbox pattern
            else if (form.find("[name^='" + checkboxname + "']").length) {
                form.find("[name^='" + checkboxname + "']").each(function (idx, ckbx) {
                    matches.push(jQuery(ckbx));
                });
            }
            // find other custom field elements (could be radio boxes so traverse the array)
            else if (form.find("[name='" + noncustomname + "']").length) {
                form.find("[name='" + noncustomname + "']").each(function (idx, fld) {
                    matches.push(jQuery(fld));
                });
                //matches.push(form.find("[name='" + noncustomname + "']"));
            }

            if (matches.length == 1) {
                value = matches[0].val();
                if (typeof value == "undefined" || value == null) {
                    value = "";
                }
            }
            // A set of radio checkboxes
            else if (matches.length > 1) {
                matches.forEach(function (match, index) {
                    // match can be a DOM input element (not jQuery)
                    if (jQuery(match).attr('checked'))
                        value = jQuery(match).val();
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
}