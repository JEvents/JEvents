/**
/**
 * @version    CVS: YOURSITES_VERSION
 * @package    com_yoursites
 * @author     Geraint Edwards
 * @copyright  2017-2020 GWE Systems Ltd
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// Polyfills for MSIE
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

function gslselect(selector) {

    let selectElements = typeof selector == 'string' ? document.querySelectorAll(selector) : new Array(selector);

    selectElements.forEach (
        function(selectElement) {

            if (selectElement.hasAttribute('hidden') || window.getComputedStyle(selectElement,'display') == 'none')
            {
                let optioncount = selectElement.getAttribute('data-optioncount');
                // if unchanged number of options then skip this!
                if (optioncount == selectElement.querySelectorAll('option, optgroup').length) {
                    return;
                }
            }

            let currentInlineDiv = selectElement.nextElementSibling;
            if (currentInlineDiv && currentInlineDiv.classList.contains('gslSelectReplacement')) {
                currentInlineDiv.parentNode.removeChild(currentInlineDiv);
            }

            selectElement.hidden = true;
            selectElement.classList.add('gsl-hidden');

            let initialValue = document.createTextNode("");

            let style = "";
            if (selectElement.multiple) {
                //initialValue.classList.add('gsl-button-group');
                // why checked and not selected I don't know!
                let checkedOptions = selectElement.querySelectorAll('option:checked');

                initialValue = document.createElement('div');
                checkedOptions.forEach(function (checkedOption) {
                    let multiSelected = document.createElement('div', {});
                    multiSelected.classList.add('gsl-button');
                    multiSelected.classList.add('gsl-button-default');
                    multiSelected.classList.add('gsl-button-xsmall');
                    multiSelected.style.marginRight = '2px';
                    multiSelected.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text))
                    multiSelected.setAttribute('data-value', checkedOption.value);
                    multiSelected.setAttribute('data-index', checkedOption.index);

                    // unselect item in the summary set of buttons
                    if (!checkedOption.disabled) {
                        multiSelected.addEventListener('click', function (e) {
                            selectElement.options[multiSelected.getAttribute('data-index')].selected = false;
                            let link = multiSelected.parentNode.parentNode.parentNode.querySelector('a.gsl-si-' + multiSelected.getAttribute('data-index'));
                            link.classList.remove('gsl-text-bold');

                            if (link.querySelector('span'))
                            {
                                link.removeChild(link.querySelector('span'));
                            }

                            multiSelected.parentElement.removeChild(multiSelected);

                            if (selectElement.options[link.getAttribute('data-index')].disabled) {
                                return;
                            }

                            let event = new Event('change');
                            selectElement.dispatchEvent(event);
                            selectElement.style.display = 'block';
                        });

                        let icon = document.createElement('span', {});
                        icon.setAttribute('gsl-icon', "icon: close;ratio: 0.5");
                        icon.classList.add('gsl-border-rounded');
                        icon.classList.add('gsl-box-shadow-small');
                        icon.classList.add('gsl-margin-small-left');
                        multiSelected.appendChild(icon);
                    }

                    initialValue.appendChild(multiSelected);
                    //initialValue.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text));
                });

                if (checkedOptions.length == 0 && selectElement.getAttribute('data-placeholder'))
                {
                    initialValue.appendChild(document.createTextNode(selectElement.getAttribute('data-placeholder')));
                }

                initialValue.style.padding    = '5px 0';
                initialValue.style.lineHeight = '25px';
            }
            else {
                let selectedOption = selectElement.options[selectElement.selectedIndex || 0];

                if (typeof selectedOption !== 'undefined') {
                    initialValue = document.createTextNode(selectedOption.getAttribute('data-content') || selectedOption.text);
                    style = selectedOption.getAttribute('style') || '';
                }
            }

            let inlineDiv = document.createElement('div', {});
            inlineDiv.classList.add('gsl-inline');
            inlineDiv.classList.add('gsl-child-width-1-1');
            inlineDiv.classList.add('gsl-margin-small-right');
            inlineDiv.classList.add('gsl-padding-remove');

            let filter = false;

            if (selectElement.multiple) {
                filter = document.createElement('div', {});
                filter.classList.add('gsl-select');
                // Spoof uikit sizing for now
                filter.setAttribute('multiple', '');
                filter.classList.add('gsl-text-left');

            } else
            {
                filter = document.createElement('button', {});
                filter.classList.add('gsl-button');
                filter.classList.add('gsl-button-default');
                filter.type = 'button';
                filter.style.overflow = "hidden";
                filter.classList.add('gsl-text-left');
                filter.classList.add('gsl-padding-remove-top');
                filter.classList.add('gsl-padding-remove-bottom');
            }
            filter.classList.add('gsl-padding-small');
            filter.classList.add('gsl-select');
            filter.classList.add('gsl-width-medium');

            if (style !== '')
            {
                filter.setAttribute('style',  style );
            }
            filter.appendChild(initialValue);

            inlineDiv.appendChild(filter);

            let dropDownDiv = document.createElement('div', {});
            dropDownDiv.classList.add('gsl-dropdown');
            dropDownDiv.classList.add('gsl-margin-remove-top');
            dropDownDiv.setAttribute('gsl-dropdown', "mode: click");

            inlineDiv.appendChild(dropDownDiv);

            let searchInput = document.createElement('input', {});
            searchInput.classList.add('gsl-input');
            searchInput.setAttribute('placeholder', 'Search ...');
            ['change', 'keyup'].forEach(function(e) {
                searchInput.addEventListener(e, function () {
                    let links = searchInput.parentNode.querySelectorAll('ul.gsl-dropdown-nav li a');
                    links.forEach(
                        function (link) {
                            if (link.innerHTML.toLowerCase().indexOf(searchInput.value.toLowerCase()) !== -1) {
                                link.parentNode.classList.remove('gsl-hidden');
                            } else {
                                link.parentNode.classList.add('gsl-hidden');
                            }
                        }
                    )
                });
            })
            dropDownDiv.appendChild(searchInput);

            let dropDownNav = document.createElement('ul', {});
            dropDownNav.classList.add('gsl-nav');
            dropDownNav.classList.add('gsl-dropdown-nav');

            selectElement.childNodes.forEach(
                function (option)
                {
                     gslselectSetupOptions(option, dropDownNav, dropDownDiv, selectElement, filter, 0);
                }
            );
            dropDownDiv.appendChild(dropDownNav);

            gslUIkit.util.on(dropDownDiv, 'show', function () {
                    searchInput.focus();
            });

            // Use Try/Catch for browsers with no support
/*
            try {
                let observer = new MutationObserver(mutationRecords => {
                    // Use traditional 'for loops' for IE 11
                    for(const mutation of mutationRecords) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            inlineDiv.parentNode.removeChild(inlineDiv);
                            observer.disconnect();
                            selectElement.setAttribute('gslChange', false);
                            gslselect(selectElement);
                        }
                    }

                });

                // observe new options
                observer.observe(selectElement, {
                    childList: true, // observe direct children
                    subtree: false, // and lower descendants too
                    characterDataOldValue: false, // pass old data to callback
                    attributes: false
                });

            }
            catch (e)
            {

            }
*/
            inlineDiv.classList.add('gslSelectReplacement');
            selectElement.insertAdjacentElement('afterend', inlineDiv);

            // Keep track of how many child Nodes there are so we can see if this has changed
            selectElement.setAttribute('data-optioncount', selectElement.querySelectorAll('option, optgroup').length);
/*
            if (!selectElement.getAttribute('gslChange')) {
                selectElement.setAttribute('gslChange', true);
                selectElement.addEventListener('gslchange', function () {
                    let dropDownDiv = selectElement.nextElementSibling;

                    let options = selectElement.querySelectorAll('option');
                    options.forEach(function (option) {
                        // find the relevant replacement item
                        let matchedLink = dropDownDiv.querySelector("a[data-value='" + option.value.replace(/'/g, "&#39;") + "']");
                        let event = new MouseEvent('click');
                        if (matchedLink && matchedLink.getAttribute('data-selected') == 'true' && option.selected == false)
                        {
                            matchedLink.dispatchEvent(event);
                        }
                        else if (matchedLink && matchedLink.getAttribute('data-selected') == 'false' && option.selected == true)
                        {
                            matchedLink.dispatchEvent(event);
                        }
                    });
                });
            }
*/
        }
    );
}

function gslselectSetupOptions(node, dropDownNav, dropDownDiv, selectElement, filter, depth)
{
    if (node.nodeType !== 1)
    {
        return;
    }
    let text = node.nodeName.toUpperCase() == 'OPTION' ? ((node.getAttribute('data-content') || node.text)) : node.getAttribute('label');
    text = " - ".repeat(depth) + text;

    let style = node.getAttribute('style') || '';
    let optionReplacement = document.createElement('li');
    optionReplacement.classList.add('gsl-box-shadow-hover-medium');

    let optionLink = document.createElement('a', {href: '#'});
    optionLink.innerHTML = text;
    if (style !== '')
    {
        optionLink.setAttribute('style',  style );
    }
    optionLink.setAttribute('data-value', node.value || '');
    optionLink.setAttribute('data-index', (node.index == 0 ? node.index : (node.index || -1)));
    optionReplacement.appendChild(optionLink);

    optionLink.classList.add('gsl-si-' + (node.index == 0 ? node.index : (node.index || -1)));

    if (node.nodeName.toUpperCase() == 'OPTION' && node.selected &&  !node.disabled) {
        optionReplacement.classList.add("gsl-active");
        optionLink.setAttribute('data-selected', "true");
        optionLink.classList.add('gsl-text-bold');

        if (selectElement.multiple) {
            let icon = document.createElement('span', {});
            icon.setAttribute('gsl-icon', "icon: close;ratio: 0.5");
            icon.classList.add('gsl-border-rounded');
            icon.classList.add('gsl-box-shadow-small');
            icon.classList.add('gsl-margin-small-left');

            optionLink.appendChild(icon);
        }
    }
    else
    {
        optionLink.setAttribute('data-selected', "false");
    }

    dropDownNav.appendChild(optionReplacement);

    if (node.nodeName.toUpperCase() == 'OPTGROUP')
    {
        optionReplacement.classList.add("gsl-disabled");
        optionLink.classList.add('gsl-text-bold');
        optionLink.classList.add('gsl-text-italic');
        optionLink.classList.add('gsl-text-emphasis');
        node.childNodes.forEach(
        function (option)
        {
            gslselectSetupOptions(option, dropDownNav, dropDownDiv, selectElement, filter, depth + 1);
        }
    );
        return;
    }
    else if (node.disabled ) {
        optionReplacement.classList.add("gsl-disabled");
        optionLink.classList.add('gsl-text-muted');
        optionLink.classList.add('gsl-text-italic');
        return;
    }

    // clicking the item in the dropdown list
    optionLink.addEventListener('click', function ()
    {
        if (selectElement.multiple) {
            optionLink.setAttribute('data-selected', optionLink.getAttribute('data-selected') == "false" ? "true" : "false");

            initialValue = document.createElement('div');
            dropDownNav.parentNode.querySelectorAll('a').forEach(function(link) {
                // if its an optgroup then there are no index attributes set!
                if (link.getAttribute('data-index') == '-1') {
                    return;
                }

                if (link.getAttribute('data-selected') == "true") {
                    if (selectElement.options[link.getAttribute('data-index')].disabled)
                    {
                        selectElement.options[link.getAttribute('data-index')].selected = false;
                        link.classList.remove('gsl-text-bold');
                        link.parentNode.classList.remove("gsl-active");
                        link.setAttribute('data-selected', "false");
                        if (link.querySelector('span'))
                        {
                            link.removeChild(link.querySelector('span'));
                        }

                    }
                    else {
                        initialValue.appendChild(document.createTextNode(link.innerHTML));
                        selectElement.options[link.getAttribute('data-index')].selected = true;
                        link.classList.add('gsl-text-bold');
                        link.parentNode.classList.add("gsl-active");
                        link.setAttribute('data-selected', "true");

                        let multiSelected = document.createElement('div', {});
                        multiSelected.classList.add('gsl-button');
                        multiSelected.classList.add('gsl-button-default');
                        multiSelected.classList.add('gsl-button-xsmall');
                        multiSelected.style.marginRight = '2px';
                        multiSelected.appendChild(document.createTextNode(optionLink.getAttribute('data-content') || optionLink.text))
                        multiSelected.setAttribute('data-value', optionLink.value);
                        multiSelected.setAttribute('data-index', optionLink.index);

                        if (!link.querySelector('span')) {
                            let icon = document.createElement('span', {});
                            icon.setAttribute('gsl-icon', "icon: close;ratio: 0.5");
                            icon.classList.add('gsl-border-rounded');
                            icon.classList.add('gsl-box-shadow-small');
                            icon.classList.add('gsl-margin-small-left');
                            link.appendChild(icon);
                        }

                    }
                } else
                {
                    selectElement.options[link.getAttribute('data-index')].selected = false;
                    link.classList.remove('gsl-text-bold');
                    link.parentNode.classList.remove("gsl-active");
                    link.setAttribute('data-selected', "false");

                    if (link.querySelector('span')) {
                        link.removeChild(link.querySelector('span'));
                    }
                }
            });

            initialValue = document.createElement('div');
            //initialValue.classList.add('gsl-button-group');
            // why checked and not selected I don't know!
            let checkedOptions = selectElement.querySelectorAll('option:checked');
            initialValue = document.createElement('div');
            checkedOptions.forEach(function (checkedOption) {
                let multiSelected = document.createElement('div', {});
                multiSelected.classList.add('gsl-button');
                multiSelected.classList.add('gsl-button-default');
                multiSelected.classList.add('gsl-button-xsmall');
                multiSelected.style.marginRight = '2px';
                multiSelected.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text))
                multiSelected.setAttribute('data-value', checkedOption.value);
                multiSelected.setAttribute('data-index', checkedOption.index);

                // unselect item
                multiSelected.addEventListener('click', function (e) {
                    selectElement.options[multiSelected.getAttribute('data-index')].selected = false;
                    let link = multiSelected.parentNode.parentNode.parentNode.querySelector('a.gsl-si-' + multiSelected.getAttribute('data-index'));
                    link.classList.remove('gsl-text-bold');
                    link.parentNode.classList.remove("gsl-active");
                    link.setAttribute('data-selected', "false");
                    multiSelected.parentElement.removeChild(multiSelected);

                    let event = new Event('change');
                    selectElement.dispatchEvent(event);
                    selectElement.style.display = 'block';
                });

                let icon = document.createElement('span', {});
                icon.setAttribute('gsl-icon', "icon: close;ratio: 0.5");
                icon.classList.add('gsl-border-rounded');
                icon.classList.add('gsl-box-shadow-small');
                icon.classList.add('gsl-margin-small-left');
                multiSelected.appendChild(icon);

                initialValue.appendChild(multiSelected);
                //initialValue.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text));
            });

            if (checkedOptions.length == 0 && selectElement.getAttribute('data-placeholder'))
            {
                initialValue.appendChild(document.createTextNode(selectElement.getAttribute('data-placeholder')));
            }

            initialValue.style.padding    = '5px 0';
            initialValue.style.lineHeight ='25px';
        }
        else
        {
            // remove all existing icons
            dropDownNav.parentNode.querySelectorAll('a').forEach(function(link) {
                link.classList.remove('gsl-text-bold');
                link.parentNode.classList.remove('gsl-active');
                link.setAttribute('data-selected', "false");
            })

            optionLink.setAttribute('data-selected', "false");
            initialValue = document.createTextNode(optionLink.innerHTML)
            selectElement.value = optionLink.getAttribute('data-value');

            optionLink.classList.add('gsl-text-bold');
            optionLink.parentNode.classList.remove('gsl-active');
            optionLink.setAttribute('data-selected', "false");

        }

        filter.setAttribute('style', optionLink.getAttribute('style'));
        filter.innerHTML = "";
        filter.appendChild(initialValue);

        gslUIkit.dropdown(dropDownDiv).hide(0);

        if (selectElement.options[optionLink.getAttribute('data-index')].disabled) {
            return;
        }

        let event = new Event('change');
        selectElement.dispatchEvent(event);
       // selectElement.style.display = 'block!important';
    });

}


document.addEventListener('DOMContentLoaded', function () {
    gslselect('.js-stools-field-filter select:not(#filter_tag2):not(.gsl-hidden)');
})
