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
	if (document.querySelectorAll) {
		let elems = document.querySelectorAll('#gslc .btn-group, #gslc .btn-group-ysts');
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
			let labels = elems[e].querySelectorAll("label.btn");
			for (let l = 0; l < labels.length; l++) {
				let label = labels[l];
				label.classList.remove('btn');
				label.classList.add('gsl-button');
				label.classList.add('gsl-button-default');
				label.classList.add('gsl-button-small');

				let activeClass = false;
				let input = document.getElementById(label.getAttribute('for'));
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
							} else if (this.value == 0) {
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
		}

		elems = document.querySelectorAll('#gslc .checkbox-group');
		for (let e = 0; e < elems.length; e++) {
			elems[e].classList.remove('checkbox-group');
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
			let labels = elems[e].querySelectorAll("label.btn");
			for (let l = 0; l < labels.length; l++) {
				let label = labels[l];
				label.classList.remove('btn');
				label.classList.add('gsl-button');
				label.classList.add('gsl-button-default');
				label.classList.add('gsl-button-small');

				let activeClass = false;
				let input = document.getElementById(label.getAttribute('for'));
				if (input) {
					activeClass = input.getAttribute('data-activeclass');
					if (input.checked || label.classList.contains('active')) {
						if (activeClass) {
							label.classList.add('gsl-button-' + activeClass);
						} else if (this.value == 0) {
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
	}
});

/* Make core showon trigger fields work on keyup events as well as change events */
window.addEventListener('load', function() {
	if (document.querySelectorAll) {

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
	}
});
