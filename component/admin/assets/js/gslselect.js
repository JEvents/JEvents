/**
/**
 * @version    CVS: YOURSITES_VERSION
 * @package    com_jevents
 * @author     Geraint Edwards
 * @copyright  2017-2020 GWE Systems Ltd
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

function gslselect() {

let selectElements = document.querySelectorAll('.js-stools-field-filter select:not(#filter_tag2)');

selectElements.forEach (
    function(selectElement) {

        if(window.getComputedStyle(selectElement).getPropertyValue('display') == 'none' || window.getComputedStyle(selectElement.parentNode).getPropertyValue('display') == 'none')
        {
            return;
        }
        selectElement.hidden = true;
        selectElement.classList.add('gsl-hidden');

        let selectedOption = selectElement.options[selectElement.selectedIndex || 0];

        let initialValue = document.createTextNode("");

        let style = "";
        if (selectElement.multiple) {
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
                if (!checkedOption.disabled) {
                    multiSelected.addEventListener('click', function (e) {
                        selectElement.options[multiSelected.getAttribute('data-index')].selected = false;
                        let link = multiSelected.parentNode.parentNode.parentNode.querySelector('a.gsl-si-' + multiSelected.getAttribute('data-index'));
                        link.classList.remove('gsl-text-bold');
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
                    multiSelected.appendChild(icon);
                }

                initialValue.appendChild(multiSelected);
                //initialValue.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text));
            });
        } else {
            if (typeof selectedOption !== 'undefined') {
                initialValue = document.createTextNode(selectedOption.getAttribute('data-content') || selectedOption.text);
                style = selectedOption.getAttribute('style') || '';
            }
        }

        selectElement.querySelectorAll('option:checked')

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
        }
        filter.classList.add('gsl-padding-small');
        filter.classList.add('gsl-padding-remove-top');
        filter.classList.add('gsl-padding-remove-bottom');
        filter.classList.add('gsl-select');
        filter.classList.add('gsl-background-default');
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

        if (selectElement.options.length > 2) {
            let searchInput = document.createElement('input', {});
            searchInput.classList.add('gsl-input');
            searchInput.setAttribute('placeholder', 'Search ...');
            ['change', 'keyup'].forEach(function (e) {
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
        }

        let dropDownNav = document.createElement('ul', {});
        dropDownNav.classList.add('gsl-nav');
        dropDownNav.classList.add('gsl-dropdown-nav');

        selectElement.querySelectorAll('option').forEach(
            function (option)
            {
                let text = (option.getAttribute('data-content') || option.text);
                let style = option.getAttribute('style') || '';
                let optionReplacement = document.createElement('li');
                optionReplacement.classList.add('gsl-box-shadow-hover-medium');

                let optionLink = document.createElement('a', {href: '#'});
                optionLink.innerHTML = text;
                if (style !== '')
                {
                    optionLink.setAttribute('style',  style );
                }
                optionLink.setAttribute('data-value', option.value);
                optionLink.setAttribute('data-index', option.index);
                optionReplacement.appendChild(optionLink);

                optionLink.classList.add('gsl-si-' + option.index);

                if (option.selected &&  !option.disabled) {
                    optionReplacement.classList.add("gsl-active");
                    optionLink.setAttribute('data-selected', "true");
                    optionLink.classList.add('gsl-text-bold');
                }
                else
                {
                    optionLink.setAttribute('data-selected', "false");
                }

                dropDownNav.appendChild(optionReplacement);

                if (option.disabled) {
                    return;
                }

                optionLink.addEventListener('click', function ()
                {
                    if (selectElement.multiple) {
                        optionLink.setAttribute('data-selected', optionLink.getAttribute('data-selected') == "false" ? "true" : "false");
                        initialValue = document.createElement('div');
                        dropDownNav.parentNode.querySelectorAll('a').forEach(function(link) {
                            if (link.getAttribute('data-selected') == "true") {
                                if (selectElement.options[link.getAttribute('data-index')].disabled)
                                {
                                    selectElement.options[link.getAttribute('data-index')].selected = false;
                                    link.classList.remove('gsl-text-bold');
                                }
                                else {
                                    initialValue.appendChild(document.createTextNode(link.innerHTML));
                                    selectElement.options[link.getAttribute('data-index')].selected = true;
                                    link.classList.add('gsl-text-bold');
                                    let multiSelected = document.createElement('div', {});
                                    multiSelected.classList.add('gsl-button');
                                    multiSelected.classList.add('gsl-button-default');
                                    multiSelected.classList.add('gsl-button-xsmall');
                                    multiSelected.style.marginRight = '2px';
                                    multiSelected.appendChild(document.createTextNode(optionLink.getAttribute('data-content') || optionLink.text))
                                    multiSelected.setAttribute('data-value', optionLink.value);
                                    multiSelected.setAttribute('data-index', optionLink.index);

                                }
                            } else
                            {
                                selectElement.options[link.getAttribute('data-index')].selected = false;
                                link.classList.remove('gsl-text-bold');
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
                                multiSelected.parentElement.removeChild(multiSelected);

                                let event = new Event('change');
                                selectElement.dispatchEvent(event);
                                selectElement.style.display = 'block';
                            });

                            let icon = document.createElement('span', {});
                            icon.setAttribute('gsl-icon', "icon: close;ratio: 0.5");
                            icon.classList.add('gsl-border-rounded');
                            icon.classList.add('gsl-box-shadow-small');
                            multiSelected.appendChild(icon);

                            initialValue.appendChild(multiSelected);
                            //initialValue.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text));
                        });

                    }
                    else
                    {
                        optionLink.setAttribute('data-selected', "true");
                        initialValue = document.createTextNode(optionLink.innerHTML)
                        selectElement.value = optionLink.getAttribute('data-value');
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
                    selectElement.style.display = 'block';
                });
            }
        );
        dropDownDiv.appendChild(dropDownNav);

        gslUIkit.util.on(dropDownDiv, 'show', function () {
            var searchInput = dropDownDiv.querySelector('input.gsl-input');
            if (typeof searchInput !== 'undefined') {
                searchInput.focus();
            }
        });

        selectElement.insertAdjacentElement('afterend', inlineDiv);

    }
);
};

document.addEventListener('DOMContentLoaded', function () {
    gslselect();
})
