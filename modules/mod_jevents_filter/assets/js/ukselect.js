/**
 * @version    CVS: YOURSITES_VERSION
 * @package    com_yoursites
 * @author     Geraint Edwards
 * @copyright  2017-YOURSITES_COPYRIGHT GWE Systems Ltd
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */


function ukselect(selector) {

  let selectElements = document.querySelectorAll(selector);

  selectElements.forEach(
    function (selectElement) {

      selectElement.hidden = true;
      selectElement.classList.add('uk-hidden');

      let checkboxes = selectElement.classList.contains('uk-filter-checkboxes') || selectElement.id == "taglkup_fvs";
      let replacementSelector = checkboxes ? 'label' : 'a';

      let selectedOption = selectElement.options[selectElement.selectedIndex || 0];

      let filter = false;

      if (selectElement.multiple) {
        filter = document.createElement('div', {});
        if (!checkboxes) {
          filter.classList.add('uk-select');
          // Spoof uikit sizing for now
          filter.setAttribute('multiple', '');
        }
        filter.classList.add('uk-text-left');

      } else {
        filter = document.createElement('button', {});
        filter.classList.add('uk-button');
        filter.classList.add('uk-button-default');
        filter.type = 'button';
        filter.style.overflow = "hidden";
      }
      if (checkboxes) {
       // filter.classList.add('uk-input');
      } else {
        filter.classList.add('uk-select');
        filter.classList.add('uk-padding-small');
        filter.classList.add('uk-padding-remove-top');
        filter.classList.add('uk-padding-remove-bottom');
      }
      filter.classList.add('uk-background-default');
      //filter.classList.add('uk-width-medium');

      let initialValue = document.createTextNode("");

      let style = "";
      if (selectElement.multiple) {
        // why checked and not selected I don't know!
        let checkedOptions = selectElement.querySelectorAll('option:checked');
        initialValue = document.createElement('div');
        filter.setAttribute('hidden', 'hidden');
        checkedOptions.forEach(function (checkedOption) {
          if (checkboxes && checkedOption.value == 0 && selectElement.id == "taglkup_fvs") {
            return;
          }

          let multiSelected = document.createElement('div', {});
          multiSelected.classList.add('uk-badge');
       //   multiSelected.classList.add('uk-badge-default');
        //  multiSelected.classList.add('uk-badge-small');
          multiSelected.style.marginRight = '2px';
          multiSelected.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text))
          multiSelected.setAttribute('data-value', checkedOption.value);
          if (checkedOption.value == '' && checkboxes) {
            multiSelected.setAttribute('hidden', 'hidden');
          } else {
            filter.removeAttribute('hidden');
          }
          multiSelected.setAttribute('data-index', checkedOption.index);

          // unselect item
          if (!checkedOption.disabled) {
            multiSelected.addEventListener('click', function (e) {
              selectElement.options[multiSelected.getAttribute('data-index')].selected = false;
              let link = multiSelected.parentNode.parentNode.parentNode.querySelector(replacementSelector + '.uk-si-' + multiSelected.getAttribute('data-index'));
              if (link)
              {
                // deselect the option by using the same event as clicking the checkbox
                let event = new Event('click');
                link.dispatchEvent(event);
                return;
              }
            });

            let icon = document.createElement('span', {});
            icon.setAttribute('uk-icon', "icon: close;ratio: 0.5");
            icon.classList.add('uk-border-rounded');
            icon.classList.add('uk-box-shadow-small');
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
      inlineDiv.classList.add('uk-inline');
      inlineDiv.classList.add('uk-child-width-1-1');
      inlineDiv.classList.add('uk-margin-small-right');
      inlineDiv.classList.add('uk-padding-remove');

      if (style !== '') {
        filter.setAttribute('style', style);
      }
      filter.appendChild(initialValue);

      inlineDiv.appendChild(filter);

      let dropDownDiv = document.createElement('div', {});
      if (!checkboxes) {
        dropDownDiv.classList.add('uk-dropdown');
        dropDownDiv.classList.add('uk-margin-remove-top');
        dropDownDiv.setAttribute('uk-dropdown', "mode: click");
        dropDownDiv.style.maxHeight = "600px";
        dropDownDiv.style.overflowY = "auto";
      }
      inlineDiv.appendChild(dropDownDiv);

      let searchInput = document.createElement('input', {});
      searchInput.classList.add('uk-input');
      searchInput.setAttribute('placeholder', 'Search ...');
      ['input'].forEach(function (e) {
        searchInput.addEventListener(e, function (evt) {
          searchInput.removeAttribute('data-ukselect-active');
          let links = searchInput.parentNode.querySelectorAll('ul.uk-dropdown-nav li ' + replacementSelector);
          links.forEach(
            function (link) {
              if (link.innerHTML.toLowerCase().indexOf(searchInput.value.toLowerCase()) !== -1) {
                link.parentNode.classList.remove('uk-hidden');
              } else {
                link.parentNode.classList.add('uk-hidden');
              }
            }
          )
        });
      });
      ['keydown'].forEach(function (e) {
        searchInput.addEventListener(e, function (evt) {
          let ukActive = parseInt(searchInput.getAttribute('data-ukselect-active') || 0);
          let links = searchInput.parentNode.querySelectorAll('ul.uk-dropdown-nav li:not(.uk-hidden) ' + replacementSelector + ':not([data-value=""])');
          if (evt.code === "ArrowUp" || evt.code === "ArrowDown") {
            ukActive = ukActive + (evt.code === "ArrowUp" ? -1 : 1);
            ukActive = Math.min(links.length, ukActive);
            ukActive = Math.max(ukActive, 1);
            searchInput.setAttribute('data-ukselect-active', ukActive);
            ukActive = parseInt(ukActive);

            // unhightlight
            links.forEach(elem => {
              elem.classList.remove('uk-box-shadow-small');
              elem.classList.remove('uk-background-muted');
            });

            // highlight
            links[ukActive - 1].classList.add('uk-box-shadow-small');
            links[ukActive - 1].classList.add('uk-background-muted');
          }
          if (evt.code === "Enter" && ukActive > 0) {
            evt.preventDefault();
            evt.stopPropagation();
            searchInput.removeAttribute('data-ukselect-active');
            links.forEach(elem => {
              elem.classList.remove('uk-box-shadow-small');
              elem.classList.remove('uk-background-muted');
            });
            let clickEvent = new Event('click');
            links[ukActive - 1].dispatchEvent(clickEvent);
          }
        });
      });

      dropDownDiv.appendChild(searchInput);

      let dropDownNav = document.createElement('ul', {});
      dropDownNav.classList.add('uk-nav');
      dropDownNav.classList.add('uk-dropdown-nav');
      if (checkboxes) {
        dropDownNav.style.maxHeight = '150px';
        dropDownNav.style.overflowY = 'auto';
      }

      selectElement.querySelectorAll('option').forEach(
        function (option) {
          if (checkboxes && option.value == 0 && selectElement.id == "taglkup_fvs") {
            return;
          }

          let text = (option.getAttribute('data-content') || option.text);
          let style = option.getAttribute('style') || '';
          let optionReplacement = document.createElement('li');
          optionReplacement.classList.add('uk-box-shadow-hover-medium');

          let optionLink;
          if (checkboxes) {
            optionLink = document.createElement('label', {});
            optionLink.style.display = 'block';
            let optionCheckbox = document.createElement('input');
            optionCheckbox.setAttribute('type', 'checkbox');
            optionCheckbox.classList.add('uk-checkbox');
            optionCheckbox.style.marginRight = '10px';

            if (option.selected && !option.disabled) {
              optionCheckbox.checked = true;
            }

            if (option.value == "") {
              optionLink.setAttribute('hidden', 'hidden');
            }

            optionLink.appendChild(optionCheckbox);

            optionText = document.createTextNode(text);
            optionLink.appendChild(optionText);

          } else {
            optionLink = document.createElement('a', { href: '#' });
            optionLink.innerText = text;
          }
          if (style !== '') {
            optionLink.setAttribute('style', style);
          }
          optionLink.setAttribute('data-value', option.value);
          optionLink.setAttribute('data-index', option.index);
          optionReplacement.appendChild(optionLink);

          optionLink.classList.add('uk-si-' + option.index);

          if (option.selected && !option.disabled) {
            optionReplacement.classList.add("uk-active");
            optionLink.setAttribute('data-selected', "true");
            optionLink.classList.add('uk-text-bold');
          } else {
            optionLink.setAttribute('data-selected', "false");
          }

          dropDownNav.appendChild(optionReplacement);

          if (option.disabled) {
            return;
          }

          optionLink.addEventListener('click', function () {
            if (selectElement.multiple) {
              optionLink.setAttribute('data-selected', optionLink.getAttribute('data-selected') == "false" ? "true" : "false");
              initialValue = document.createElement('div');
              dropDownNav.parentNode.querySelectorAll(replacementSelector).forEach(function (link) {
                if (link.getAttribute('data-selected') == "true") {
                  if (selectElement.options[link.getAttribute('data-index')].disabled) {
                    selectElement.options[link.getAttribute('data-index')].selected = false;
                    link.classList.remove('uk-text-bold');
                  } else {
                    initialValue.appendChild(document.createTextNode(link.innerText));
                    selectElement.options[link.getAttribute('data-index')].selected = true;
                    link.classList.add('uk-text-bold');
                    let multiSelected = document.createElement('div', {});
                    multiSelected.classList.add('uk-badge');
                 //   multiSelected.classList.add('uk-badge-default');
                //    multiSelected.classList.add('uk-badge-small');
                    multiSelected.style.marginRight = '2px';
                    multiSelected.appendChild(document.createTextNode(optionLink.getAttribute('data-content') || optionLink.text))
                    multiSelected.setAttribute('data-value', optionLink.value);
                    multiSelected.setAttribute('data-index', optionLink.index);

                  }
                } else {
                  selectElement.options[link.getAttribute('data-index')].selected = false;
                  link.classList.remove('uk-text-bold');
                }
              });

              initialValue = document.createElement('div');
              //initialValue.classList.add('uk-badge-group');
              // why checked and not selected I don't know!
              let checkedOptions = selectElement.querySelectorAll('option:checked');
              initialValue = document.createElement('div');
              checkedOptions.forEach(function (checkedOption) {
                let multiSelected = document.createElement('div', {});
                multiSelected.classList.add('uk-badge');
       //         multiSelected.classList.add('uk-badge-default');
        //        multiSelected.classList.add('uk-badge-small');
                multiSelected.style.marginRight = '2px';
                multiSelected.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text))
                multiSelected.setAttribute('data-value', checkedOption.value);
                multiSelected.setAttribute('data-index', checkedOption.index);

                // unselect item
                multiSelected.addEventListener('click', function (e) {
                  selectElement.options[multiSelected.getAttribute('data-index')].selected = false;
                  let link = multiSelected.parentNode.parentNode.parentNode.querySelector(replacementSelector + '.uk-si-' + multiSelected.getAttribute('data-index'));
                  link.classList.remove('uk-text-bold');
                  multiSelected.parentElement.removeChild(multiSelected);

                  let event = new Event('change');
                  selectElement.dispatchEvent(event);
                  selectElement.style.display = 'block';
                });

                let icon = document.createElement('span', {});
                icon.setAttribute('uk-icon', "icon: close;ratio: 0.5");
                icon.classList.add('uk-border-rounded');
                icon.classList.add('uk-box-shadow-small');
                multiSelected.appendChild(icon);

                initialValue.appendChild(multiSelected);
                //initialValue.appendChild(document.createTextNode(checkedOption.getAttribute('data-content') || checkedOption.text));
              });

            } else {
              optionLink.setAttribute('data-selected', "true");
              initialValue = document.createTextNode(optionLink.innerText)
              selectElement.value = optionLink.getAttribute('data-value');
            }

            filter.setAttribute('style', optionLink.getAttribute('style'));
            filter.innerHTML = "";
            filter.appendChild(initialValue);

           // UIkit.dropdown(dropDownDiv).hide(0);

            if (selectElement.options[optionLink.getAttribute('data-index')].disabled) {
              return;
            }

            selectElement.hidden = false;
            // selectElement.classList.remove('uk-hidden');

            // Must always have something selected??
            if (selectElement.querySelectorAll('option:checked').length == 0 && selectElement.id == "taglkup_fvs")
            {
              selectElement.selectedIndex = 0;
            }
            else {
              let event = new Event('change');
              selectElement.dispatchEvent(event);
            }

            if (selectElement.id == "taglkup_fvs")
            {
               selectElement.form.submit();
            }
          });
        }
      );
      dropDownDiv.appendChild(dropDownNav);

      UIkit.util.on(dropDownDiv, 'show', function () {
        searchInput.focus();
        let links = searchInput.parentNode.querySelectorAll('ul.uk-dropdown-nav li ' + replacementSelector);
        // unhightlight
        links.forEach(elem => {
          elem.classList.remove('uk-box-shadow-small');
          elem.classList.remove('uk-background-muted');
        });


      });

      selectElement.insertAdjacentElement('afterend', inlineDiv);

    }
  );
};

document.addEventListener('DOMContentLoaded', function () {
  if (typeof UIkit !== 'undefined') {
    ukselect('.jevfiltermodule select');
  }
})
