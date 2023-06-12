/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only define the Joomla namespace if not defined.
Joomla = window.Joomla || {};

!(function(document) {
    "use strict";

    /**
     * JField 'showon' feature.
     */
    window.jQuery && (function($) {

        /**
         * Method to check condition and change the target visibility
         * @param {jQuery}  target
         * @param {Boolean} animate
         */
        function linkedoptions(target, animate) {
            var showfield = true,
                jsondata  = target.data('showon-gsl') || target.data('showon-uk') || [],
                itemval, condition, fieldName, $fields;

            // Check if target conditions are satisfied
            for (var j = 0, lj = jsondata.length; j < lj; j++) {
                condition = jsondata[j] || {};
                fieldName = condition.field;
                $fields   = $('[name="' + fieldName + '"], [name="' + fieldName + '[]"]');

                condition['valid'] = 0;

                // Test in each of the elements in the field array if condition is valid
                $fields.each(function() {
                    var $field = $(this);

                    if ($field.prop("tagName").toLowerCase() == "select")
                    {
                        var x = 1;
                    }
                    // If checkbox or radio box the value is read from properties
                    if (['checkbox', 'radio'].indexOf($field.attr('type')) !== -1) {
                        if (!$field.prop('checked')) {
                            // unchecked fields will return a blank and so always match a != condition so we skip them
                            return;
                        }
                        itemval = $field.val();
                    }
                    else {
                        // select lists, textarea etc. Note that multiple-select list returns an Array here
                        // se we can always tream 'itemval' as an array
                        itemval = $field.val();
                        // a multi-select <select> $field  will return null when no elements are selected so we need to define itemval accordingly
                        if (itemval == null && $field.prop("tagName").toLowerCase() == "select") {
                            itemval = [];
                        }
                    }

                    // Convert to array to allow multiple values in the field (e.g. type=list multiple)
                    // and normalize as string
                    if (typeof itemval == 'string') {
                        itemval = [ itemval ];
                    }
                    else if (typeof itemval !== 'object') {
                        itemval = JSON.parse('["' + itemval + '"]');
                    }

                    // for (var i in itemval) loops over non-enumerable properties and prototypes which means that != will ALWAYS match
                    // see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/for...in
                    // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/getOwnPropertyNames
                    // use native javascript Array forEach - see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach
                    // We can't use forEach because its not supported in MSIE 8 - once that is dropped this code could use forEach instead and not have to use propertyIsEnumerable
                    //
                    // Test if any of the values of the field exists in showon conditions
                    for (var i in itemval) {
                        // See https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/propertyIsEnumerable
                        // Needed otherwise we pick up unenumerable properties like length etc. and !: will always match one of these  !!
                        if (!itemval.propertyIsEnumerable(i)) {
                            continue;
                        }
                        // ":" Equal to one or more of the values condition
                        if (jsondata[j]['sign'] == '=' && jsondata[j]['values'].indexOf(itemval[i]) !== -1) {
                            jsondata[j]['valid'] = 1;
                        }
                        // "!:" Not equal to one or more of the values condition
                        if (jsondata[j]['sign'] == '!=' && jsondata[j]['values'].indexOf(itemval[i]) === -1) {
                            jsondata[j]['valid'] = 1;
                        }

                    }

                });

                // Verify conditions
                // First condition (no operator): current condition must be valid
                if (condition['op'] === '') {
                    if (condition['valid'] === 0) {
                        showfield = false;
                    }
                }
                // Other conditions (if exists)
                else {
                    // AND operator: both the previous and current conditions must be valid
                    if (condition['op'] === 'AND' && condition['valid'] + jsondata[j - 1]['valid'] < 2) {
                        showfield = false;
                    }
                    // OR operator: one of the previous and current conditions must be valid
                    if (condition['op'] === 'OR' && condition['valid'] + jsondata[j - 1]['valid'] > 0) {
                        showfield = true;
                    }
                }
            }

            // If conditions are satisfied show the target field(s), else hide.
            // Note that animations don't work on list options other than in Chrome.
            if (animate && !target.is('option')) {

                // In fact animations cause drop downs to appear behind each other e.g. on select list 1* in event editing when using chosen!
                if (!target[0].classList.contains('gsl-animation-slide-bottom')) {
                   // target[0].classList.add('gsl-animation-slide-bottom');
                }
                if (!target[0].classList.contains('uk-animation-slide-bottom')) {
                    // target[0].classList.add('uk-animation-slide-bottom');
                }

                if (showfield)
                {
                    var isGrid = target[0].classList.contains('gsl-grid')
                        || target[0].parentNode.classList.contains('gsl-grid')
                        || target[0].classList.contains('uk-grid')
                        || target[0].parentNode.classList.contains('uk-grid')
                        || target[0].classList.contains('control-group');
                    target[0].style.display = isGrid ? 'flex' : (target[0].nodeName.toLowerCase() == 'tr' ? 'table-row' : 'block');
                }
                else
                {
                    target[0].style.display='none';
                }
            } else {
                if (showfield)
                {
                    var isGrid = target[0].classList.contains('gsl-grid')
                        || target[0].parentNode.classList.contains('gsl-grid')
                        || target[0].classList.contains('uk-grid')
                        || target[0].parentNode.classList.contains('uk-grid')
                        || target[0].classList.contains('control-group');
                    target[0].style.display = isGrid ? 'flex' : (target[0].nodeName.toLowerCase() == 'tr' ? 'table-row' : 'block');
                }
                else
                {
                    target[0].style.display='none';
                }

                if (target.is('option')) {
                    target.attr('disabled', showfield ? false : true);
                    // If chosen active for the target select list then update it
                    var parent = target.parent();
                    if ($('#' + parent.attr('id') + '_chzn').length) {
                        parent.trigger("liszt:updated");
                        parent.trigger("chosen:updated");
                    }
                }
            }

            try {
                var showonChanged = new CustomEvent('gslshowon', {
                    detail: {
                        name: 'showonChanged'
                    }
                });

                document.dispatchEvent(showonChanged);

            }
            catch (e) {

            }
        }

        /**
         * Method for setup the 'showon' feature, for the fields in given container
         * @param {HTMLElement} container
         */
        function setUpShowon(container) {
            container = container || document;

            var $showonFields = $(container).find('[data-showon-gsl],[data-showon-uk]');
            // Setup each 'showon' field
            for (var is = 0, ls = $showonFields.length; is < ls; is++) {
                // Use anonymous function to capture arguments
                (function() {
                    var $target = $($showonFields[is]), $jsondata = $target.data('showon-gsl') || $target.data('showon-uk') || [],
                        $field, $fields = $();

                    // Collect an all referenced elements
                    for (var ij = 0, lj = $jsondata.length; ij < lj; ij++) {
                        $field   = $jsondata[ij]['field'];
                        $fields = $fields.add($('[name="' + $field + '"], [name="' + $field + '[]"]'));
                    }

                    // Check current condition for element
                    linkedoptions($target);

                    // Attach events to referenced element, to check condition on change
                    // input event see https://w3c.github.io/uievents/#event-type-input
                    // we don't use 'input' as a trigger here since it fires too many events when editing the event title
                    $fields.on('change ', function() {
                        linkedoptions($target, true);
                    });
                })();

                // Setup each 'showon' field onkeypress to mimic onchange
                if (document.querySelectorAll) {
                    let showonFields = container.querySelectorAll('[data-showon-gsl],[data-showon-uk]');

                    let target = showonFields[is];
                    let jsondata = JSON.parse(target.getAttribute('data-showon-gsl')) || JSON.parse(target.getAttribute('data-showon-uk')) || [],
                        fields = [];

                    if (typeof jsondata['AND'] !== 'undefined') {
                        jsondata = jsondata['AND'];
                    } else if (typeof jsondata['OR'] !== 'undefined') {
                        jsondata = jsondata['OR'];
                    } else {
                        jsondata = jsondata;
                    }

                    // Collect an all referenced elements
                    for (let ij = 0; ij < jsondata.length; ij++) {
                        let field = jsondata[ij]['field'];
                        let namefields = document.querySelectorAll(
                            '#jevents [name="' + field + '"], #jevents [name="' + field + '[]"], ' +
                            '.jevlocations #config [name="' + field + '"], .jevlocations #config [name="' + field + '[]"]'
                        );
                        for (let nf = 0; nf < namefields.length; nf++) {
                            fields.push(namefields[nf]);
                        }
                    }

                    for (let f = 0; f < fields.length; f++) {
                        let type = fields[f].getAttribute('type');
                        if (type == 'text' || type == 'radio' || type == 'checkbox') {
                            if (fields[f].value.length > 0) {
                                fields[f].setAttribute('data-keyuplistener', 1);
                            }
                            fields[f].addEventListener('keyup', function (event) {
                                let keyuplistener = this.getAttribute('data-keyuplistener') || -1;

                                if (keyuplistener > 0 && this.value.length == 0) {
                                    keyuplistener = -1;
                                }
                                if (keyuplistener == 0 && this.value.length > 0) {
                                    keyuplistener = -1;
                                }
                                if (keyuplistener > -1) {
                                    return;
                                }

                                this.setAttribute('data-keyuplistener', this.value.length);

                                // Can't use new Event() because of MSIE :(
                                // Create the event.
                                let changeEvent = document.createEvent('Event');

                                // Define that the event name is 'build'.
                                changeEvent.initEvent('change', false, false);

                                // target can be any Element or other EventTarget.
                                this.dispatchEvent(changeEvent);

                            });
                        }
                    }
                }

            }
        }

        /**
         * Initialize 'showon' feature
         */
        $(document).ready(function() {
            window.setTimeout(setUpShowon, 100);

            // Setup showon feature in the subform field
            $(document).on('subform-row-add', function(event, row) {
                var $row      = $(row),
                    $elements = $row.find('[data-showon-gsl]'),
                    $elementsuk = $row.find('[data-showon-uk]'),
                    baseName  = $row.data('baseName'),
                    group     = $row.data('group'),
                    search    = new RegExp('\\[' + baseName + '\\]\\[' + baseName + 'X\\]', 'g'),
                    replace   = '[' + baseName + '][' + group + ']',
                    $elm, showon;

                // Fix showon field names in a current group
                for (var i = 0, l = $elements.length; i < l; i++) {
                    $elm   = $($elements[i]);
                    showon = $elm.attr('data-showon-gsl').replace(search, replace);

                    $elm.attr('data-showon-gsl', showon);
                }
                for (var i = 0, l = $elementsuk.length; i < l; i++) {
                    $elm   = $($elementsuk[i]);
                    showon = $elm.attr('data-showon-uk').replace(search, replace);

                    $elm.attr('data-showon-uk', showon);
                }

                setUpShowon(row);
            });
        });

    })(jQuery);

})(document, Joomla);
