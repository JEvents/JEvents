/**
 * @version    CVS: JEVENTS_VERSION
 * @package    com_jevents
 * @author     Geraint Edwards
 * @copyright  2017--JEVENTS_COPYRIGHT GWESystems Ltd
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// Build to see changes because files are in media folder! */
// see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise
// https://stackoverflow.com/questions/36016327/how-to-make-promises-work-in-ie11#36018899
'use strict';

// uikit popovers

// TODO - watch that these popups are block elements that could be within a span :(
// Tooltip based version
function ys_setuptooltip(selector) {

	// Setup new ones
	var hoveritems = document.querySelectorAll(selector);
	hoveritems.forEach(function (hoveritem) {

		let title = hoveritem.getAttribute('data-yspoptitle') || hoveritem.getAttribute('data-original-title') || hoveritem.getAttribute('title');
		let options = hoveritem.getAttribute('data-yspopoptions') || '{"mode" : "click, hover", "offset" : 20,"delay" : 20, "pos" : "top", "duration" : 200}';
		options = JSON.parse(options);

		options.container = "#jevents";
		options.title = title;

		if (hoveritem.hasAttribute('title')) {
			hoveritem.removeAttribute('title');
		}

		UIkit.tooltip(hoveritem, options);
	});
}

function ys_setuppopover(selector)
{

	// Setup new ones
	var hoveritems = document.querySelectorAll(selector);
	hoveritems.forEach(function (hoveritem) {

		let title = hoveritem.getAttribute('data-yspoptitle') || hoveritem.getAttribute('data-original-title') || hoveritem.getAttribute('title');
		let body = hoveritem.getAttribute('data-yspopcontent') || hoveritem.getAttribute('data-content')  || hoveritem.getAttribute('data-bs-content') || '';
		let options = hoveritem.getAttribute('data-yspopoptions') || '{"mode" : "click, hover", "offset" : 10, "animation" : "", "delayHide" : 2000, "pos" : "left"}';
		//options = '{ "offset" : 20,"delay" : 20, "pos" : "top", "duration" : 200}';
		options = JSON.parse(options);

		options.container = "#jevents";

		let phtml = '<div class="ys-popover-block">' +
			//'<i gsl-icon="icon:chevron-up; ratio:1" class="poparrow popbelow"></i>\n' +
			//'<i gsl-icon="icon:chevron-down; ratio:1" class="poparrow popabove"></i>\n' +
			//'<i gsl-icon="icon:chevron-left; ratio:1" class="poparrow popleft"></i>\n' +
			//'<i gsl-icon="icon:chevron-right; ratio:1" class="poparrow popright"></i>\n' +
			(title != '' ? '<div class="ys-popover-title">' + title + '</div>' : '') +
			(body != '' ? '<div class="ys-popover-body">' + body + '</div>' : '') +
			'</div>';
		options.title = phtml;
		if (hoveritem.hasAttribute('title')) {
			hoveritem.removeAttribute('title');
		}

		UIkit.tooltip(hoveritem, options);
	});
}

	document.addEventListener('DOMContentLoaded', function () {
		ys_setuppopover( ".ys-popover");
	});
	document.addEventListener('DOMContentLoaded', function () {
		ys_setuptooltip(".ys-tooltip");
	});



