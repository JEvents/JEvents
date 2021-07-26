/* copyright GWE Systems Ltd : 2015 All rights reserved */

var jevConditional = {
    setupJevConditions: function (conditional, fielddefault, condlabel, condparam, conditions, fieldparam, condarray, fielddefaultarray) {
        var condition = jQuery("#" + condparam + conditions);

        var radioElements = condition.find('input[type=radio]');
        if (radioElements.length) {
            radioElements.each(function (i, re) {
                // Need both for Chosen replacements
                jQuery(re).on("click", function () {
                    jevConditional.jevCondition(conditional, fielddefault, condlabel, condparam, conditions, fieldparam, condarray, fielddefaultarray)
                });
                jQuery(re).on("change", function () {
                    jevConditional.jevCondition(conditional, fielddefault, condlabel, condparam, conditions, fieldparam, condarray, fielddefaultarray)
                });
            });
        }
        else if (condition.prop("tagName") == "SELECT") {
            var condition_chzn = jQuery("#" + condition.attr("id") + "_chzn");
            if (condition_chzn.length) {
                condition_chzn.each(function (i, cc) {
                    jQuery(cc).on("click", function () {
                        jevConditional.jevCondition(conditional, fielddefault, condlabel, condparam, conditions, fieldparam, condarray, fielddefaultarray)
                    });
                });
            }
            else
                condition.on("change", function () {
                    jevConditional.jevCondition(conditional, fielddefault, condlabel, condparam, conditions, fieldparam, condarray, fielddefaultarray)
                });
        }
        else {
            condition.on("change", function () {
                jevConditional.jevCondition(conditional, fielddefault, condlabel, condparam, conditions, fieldparam, condarray, fielddefaultarray)
            });
        }
        jevConditional.jevCondition(conditional, fielddefault, condlabel, condparam, conditions, fieldparam, condarray, fielddefaultarray)
    },
    jevCondition: function (conditional, fielddefault, condlabel, condparam, conditions, fieldparam, condarray, fielddefaultarray) {
        var condition = jQuery('#' + condparam + conditions);
        var eventsno = jQuery('#' + fieldparam + conditional);
        if (!condition.length || !eventsno.length) {
            //alert('no match ' +  '#'+condparam + conditions + " " +condition.length +" " + '#'+fieldparam+conditional + " "+eventsno.length);
            return;
        }
        // Joomla 3.x named element is inside control and also control-group elements
        var hiddencontrol = eventsno.parent().parent();
        // Joomla 2.5
        if (hiddencontrol.prop("tagName") == "UL") {
            hiddencontrol = eventsno.parent();
        }

        var conditionsarray = condarray;
        if (condition.prop('type') == "checkbox") {
            condition.val(condition.prop('checked') ? 1 : 0);
        }

        var radioElements = condition.find('input[type=radio]');
        if (radioElements.length) {

        }
        radioElements.each(function (i, re) {
            if (jQuery(re).prop('checked')) {
                //alert(jQuery(re).parent().html() + " "+jQuery(re).prop('checked')+ " "+jQuery(re).val
                condition.val(jQuery(re).val());
            }
        });

        if (condition.prop('multiple') && condition.prop("tagName") == "SELECT") {
            condition.find('option:selected').each(function (i, co) {
                if (conditionsarray.indexOf(jQuery(co).val()) >= 0) {
                    conditionsarray.push(jQuery(co).val());
                }
            });
        }

        var checkboxElements = condition.prop('type') == "checkbox" ? new Array(condition) : condition.find('input[type=checkbox]');
        if (checkboxElements.length > 0) {
            condition.val([]);
            checkboxElements.each(function (i, cbe) {
                if (jQuery(cbe).prop('checked') && conditionsarray.indexOf(jQuery(cbe).val()) >= 0) {
                    condition.val(conditionsarray[jQuery(cbe).val()]);
                }
            });
        }

        // If condition is valid then show the row
        conditionmet = false;
        if (conditionsarray.length == 1 && conditionsarray[0].indexOf('!') == 0)
        {
            notconditionsarray = [conditionsarray[0].substr(1)];
            if (notconditionsarray.indexOf(condition.val()) < 0) {
                if (hiddencontrol.prop("tagName") == "TR") {
                    hiddencontrol.css("display", "table-row");
                } else if (hiddencontrol.prop("tagName") == "SPAN") {
                    hiddencontrol.css("display", "inline");
                } else {
                    //hiddencontrol.css("display", "block");
                    hiddencontrol.prop('hidden', false);
                }
                conditionmet = true;
            }
        }
        else if (conditionsarray.indexOf(condition.val()) >= 0) {
            if (hiddencontrol.prop("tagName") == "TR") {
                hiddencontrol.css("display", "table-row");
            }
            else if (hiddencontrol.prop("tagName") == "SPAN") {
                hiddencontrol.css("display", "inline");
            }
            else {
                //hiddencontrol.css("display", "block");
                hiddencontrol.prop('hidden', false);
            }
            conditionmet = true;
        }

        // else hide the row and revert the value to its default
        if (!conditionmet) {
            // Is the dependent field is a select list, or radio list
            if (eventsno.find('option').length) {
                var defaultarray = fielddefaultarray;
                eventsno.find('option').each(function (i, eno) {
                    if (defaultarray.indexOf(jQuery(eno).val()) >= 0) {
                        jQuery(eno).prop('selected', true);
                    }
                    else {
                        jQuery(eno).prop('selected', false);
                    }
                });
            }
            else {
                eventsno.val(fielddefault);
            }
            //hiddencontrol.css("display", "none");
            hiddencontrol.prop('hidden', true);
        }

        try {
            jQuery(eventsno).trigger("liszt:updated");
        }
        catch (e) {
        }
    }
};
