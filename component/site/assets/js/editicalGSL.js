/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: editicalJQ.js 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008--JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

document.addEventListener('DOMContentLoaded', function () {
	editicalGslStyling(document.getElementById('jevents'));
});

function editicalGslStyling(container) {

	if (typeof container !== 'undefined' && document.querySelectorAll) {
		let elems = container.querySelectorAll(' .btn-group,  .btn-group-ysts');
		for (let e = 0; e < elems.length; e++) {
			elems[e].classList.remove('btn-group');
			elems[e].classList.remove('radio');
			elems[e].classList.add('gsl-button-group');
			let inputs = elems[e].querySelectorAll('.radio');
			for (let i = 0; i < inputs.length; i++) {
				inputs[i].classList.remove('radio');
			}
			inputs = elems[e].querySelectorAll("input[type='checkbox'], input[type='radio']");
			for (let i = 0; i < inputs.length; i++) {
				inputs[i].classList.add('gsl-hidden');
			}
			// don't restrict to label.btn since jticketing and others don't put btn as class for label in their plugin!!
			let labels = elems[e].querySelectorAll("label");
			for (let l = 0; l < labels.length; l++) {
				let label = labels[l];
				label.classList.remove('btn');
				label.classList.add('gsl-button');
				label.classList.add('gsl-button-default');
				label.classList.add('gsl-button-medium');

				let activeClass = false;
				let input = container.querySelector('#' + label.getAttribute('for'));
				if (input) {

					if (label.classList.contains('btn-danger')) {
						label.classList.remove('btn-danger');
						input.setAttribute('data-activeclass', 'danger');
					}
					if (label.classList.contains('btn-success')) {
						label.classList.remove('btn-success');
						input.setAttribute('data-activeclass', 'primary');
					}
					if (label.classList.contains('btn-warning')) {
						label.classList.remove('btn-warning');
						input.setAttribute('data-activeclass', 'warning');
					}

					if (input.checked || label.classList.contains('active')) {
						activeClass = input.getAttribute('data-activeclass');
						if (input.checked || label.classList.contains('active')) {
							if (activeClass) {
								label.classList.add('gsl-button-' + activeClass);
							} else if (input.value == 0) {
								label.classList.add('gsl-button-danger');
							} else {
								label.classList.add('gsl-button-primary');
							}
						} else {
							if (activeClass) {
								label.classList.remove('gsl-button-' + activeClass);
							}
							label.classList.remove('gsl-button-success');
							label.classList.remove('gsl-button-primary');
							label.classList.remove('gsl-button-danger');
						}
					}
				}

				label.classList.remove('active');
			}

			let buttons = elems[e].querySelectorAll(".btn, .btn-mini, button");
			for (let l = 0; l < buttons.length; l++) {
				let button = buttons[l];
				button.classList.remove('btn');
				button.classList.remove('btn-mini');
				button.classList.remove('button');
				button.classList.add('gsl-button');
				if (button.closest('.subform-repeatable'))
				{
					button.classList.add('gsl-button-mini');
				}
				else {
					button.classList.add('gsl-button-medium');
				}

				if (button.classList.contains('btn-danger')) {
					button.classList.remove('btn-danger');
					button.classList.add('gsl-button-danger');
				}
				else if (button.classList.contains('btn-success')) {
					button.classList.remove('btn-success');
					button.classList.add('gsl-button-primary');
				}
				else if (button.classList.contains('btn-primary')) {
					button.classList.remove('btn-primary');
					button.classList.add('gsl-button-primary');
				}
				else if (button.classList.contains('btn-warning')) {
					button.classList.remove('btn-warning');
					button.classList.add('gsl-button-danger');
				}
				else {
					button.classList.add('gsl-button-default');
				}
			}
		}

		elems = container.querySelectorAll(' .checkbox-group, .checkboxes');
		for (let e = 0; e < elems.length; e++) {
			elems[e].classList.remove('checkbox-group');
			elems[e].classList.remove('checkboxes');
			elems[e].classList.remove('checkbox');
			elems[e].classList.add('gsl-button-group');

			let inputs = elems[e].querySelectorAll('.checkbox');
			for (let i = 0; i < inputs.length; i++) {
				inputs[i].classList.remove('checkbox');
			}
			inputs = elems[e].querySelectorAll("input[type='checkbox'], input[type='radio']");
			for (let i = 0; i < inputs.length; i++) {
				inputs[i].classList.add('gsl-hidden');
			}
			let labels = elems[e].querySelectorAll("label");
			for (let l = 0; l < labels.length; l++) {
				let label = labels[l];
				label.classList.remove('btn');
				label.classList.add('gsl-button');
				label.classList.add('gsl-button-default');
				label.classList.add('gsl-button-medium');

				let activeClass = false;
				let input = container.querySelector('#' + label.getAttribute('for'));
				if (input) {
					activeClass = input.getAttribute('data-activeclass');
					if (input.checked || label.classList.contains('active')) {
						if (activeClass) {
							label.classList.add('gsl-button-' + activeClass);
						} else if (input.value == 0) {
							label.classList.add('gsl-button-danger');
						} else {
							label.classList.add('gsl-button-primary');
						}
					} else {
						if (activeClass) {
							label.classList.remove('gsl-button-' + activeClass);
						}
						label.classList.remove('gsl-button-primary');
						label.classList.remove('gsl-button-danger');
					}
				}

				label.classList.remove('active');
			}
		}

		elems = container.querySelectorAll("i[class^='icon-'], span[class^='icon-']");
		for (let e = 0; e < elems.length; e++) {
			if (elems[e].classList.contains('icon-user'))
			{
				elems[e].classList.remove('icon-user');
				elems[e].classList.add('gsl-icon');
				elems[e].classList.add('gsl-margin-small-right');
				elems[e].setAttribute('data-gsl-icon' , 'icon:user;ratio:0.7');
			}
			else if (elems[e].classList.contains('icon-calendar'))
			{
				elems[e].classList.remove('icon-calendar');
				elems[e].classList.add('gsl-icon');
				elems[e].setAttribute('data-gsl-icon' , 'icon:calendar;');
			}
			// repeatable fields
			else if (elems[e].classList.contains('icon-plus'))
			{
				elems[e].classList.remove('icon-plus');
				elems[e].classList.add('gsl-icon');
				elems[e].setAttribute('data-gsl-icon' , 'icon:plus;ratio:0.7');
			}
			// repeatable fields
			else if (elems[e].classList.contains('icon-minus'))
			{
				elems[e].classList.remove('icon-minus');
				elems[e].classList.add('gsl-icon');
				elems[e].setAttribute('data-gsl-icon' , 'icon:minus;ratio:0.7');
			}
		}

		// general cleanup
		let cleanups = ['input-group', 'input-group-append', 'input-append', 'row', , 'controls'];
		for (let c = 0; c < cleanups.length;c++) {
			elems = container.querySelectorAll("." + cleanups[c]);
			for (let e = 0; e < elems.length; e++) {
				elems[e].classList.remove(cleanups[c]);
			}
		}

		elems = container.querySelectorAll('.control-label');
		for (let e = 0; e < elems.length; e++) {
			elems[e].classList.remove('control-label');
			elems[e].classList.add('gsl-form-label');
			elems[e].classList.add('gsl-text-bold');
		}

		elems = container.querySelectorAll('.control-group');
		for (let e = 0; e < elems.length; e++) {
			elems[e].classList.remove('control-group');
			elems[e].classList.add('gsl-margin-small-bottom');
		}

		var inputs = container.querySelectorAll('button:not(.gsl-button)');

		inputs.forEach (function(elem){

			elem.classList.add('gsl-button');
			elem.classList.add('gsl-button-medium');
			elem.classList.remove('button2-left')

			if (elem.classList.contains('btn-danger')) {
				elem.classList.remove('btn-danger');
				elem.classList.add('gsl-button-danger');
			}
			else if (elem.classList.contains('btn-primary')) {
				elem.classList.remove('btn-primary');
				elem.classList.add('gsl-button-primary');
			}
			else if (elem.classList.contains('btn-success')) {
				elem.classList.remove('btn-success');
				elem.classList.add('gsl-button-primary');
			}
			else if (elem.classList.contains('btn-warning')) {
				elem.classList.remove('btn-warning');
				elem.classList.add('gsl-button-danger');
			}
			else
			{
				elem.classList.add('gsl-button-primary');
			}

		})

		var inputs = container.querySelectorAll('.button2-left a:not(.gsl-button)');
		inputs.forEach (function(elem) {
			var group = elem.closest('.button2-left');
			group.classList.remove('button2-left');
			group.classList.add('gsl-button-group');
			elem.classList.add('gsl-button');
			elem.classList.add('gsl-button-medium');
			elem.classList.add('gsl-button-default');
			elem.classList.add('gsl-margin-small-right');
		})

		var inputs = container.querySelectorAll('select:not(.gsl-select)');
		inputs.forEach (function(elem){

			if (elem.hasAttribute('hidden') || window.getComputedStyle(elem,'display') == 'none')
			{
				return;
			}
			elem.classList.add('gsl-select');
			//elem.classList.add('gsl-form-width-medium');
			elem.classList.add('gsl-width-medium');
			elem.classList.remove('inputbox')
			if (elem.getAttribute('size') == 1) {
				elem.removeAttribute('size');
			}

		})

		inputs = container.querySelectorAll('input[type="text"]:not(.gsl-input):not(.minicolors)');
		inputs.forEach (function(elem){
			elem.classList.add('gsl-input');
			var classList = elem.classList;
			if (!classList.contains('gsl-width-small') && !classList.contains('gsl-width-large')) {
				elem.classList.add('gsl-width-medium');
			}
			elem.classList.remove('inputbox')
			elem.classList.remove('input-medium')
			elem.removeAttribute('size');
		})

		inputs = container.querySelectorAll('textarea:not(.gsl-textarea)');
		inputs.forEach (function(elem){
			elem.classList.add('gsl-textarea');
			//elem.classList.add('gsl-form-width-medium');
			elem.classList.add('gsl-width-medium');
		})

		/* field calendar height match */
		let fieldcalendars = container.querySelectorAll('.field-calendar, .bootstrap-timepicker');
		fieldcalendars.forEach (function(fieldcalendar){
			let input = fieldcalendar.querySelector('input');
			let button = fieldcalendar.querySelector('button');
			if (input && button)
			{
				// can't use offset height its zero when not displayed
				button.style.height = window.getComputedStyle(input).getPropertyValue('height');
			}
		});
		// To review
		/*
		sr-only
		watch out for editor textareas being messed up
		*/

	}
};

/* Make core showon trigger fields work on keyup events as well as change events */
window.addEventListener('load', function() {
	if (document.querySelectorAll) {

		// repeatable fields
		let repeatables = document.getElementById('jevents').querySelectorAll('div.subform-repeatable');
		for (let r = 0; r < repeatables.length; r++)
		{
			jQuery(repeatables[r]).on('subform-row-add', function (event) {
				editicalGslStyling(repeatables[r]);
			});
		}

		// Moved to showon code itself!
		/*
		let showonFields = document.getElementById('jevents').querySelectorAll('[data-showon-gsl]');

		// Setup each 'showon' field onkeypress to mimic onchange
		for (let is = 0; is < showonFields.length; is++) {
			let target = showonFields[is];
			let jsondata = JSON.parse(target.getAttribute('data-showon-gsl')) || [],
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
				let namefields = document.getElementById('jevents').querySelectorAll('[name="' + field + '"], [name="' + field + '[]"]');
				for (nf = 0; nf < namefields.length; nf++) {
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
		*/
	}
});


