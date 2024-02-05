/**
 * @version    CVS: JEVENTS_VERSION
 * @package    com_jevents
 * @author     Geraint Edwards
 * @copyright  2017--JEVENTS_COPYRIGHT GWESystems Ltd
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

'use strict';
var j3 = true;
j3 = typeof j3php == "undefined" ? j3 : j3php;
//alert('j3 is ' + j3 + ' from js');

// Polyfills
// from:https://github.com/jserz/js_piece/blob/master/DOM/ChildNode/remove()/remove().md
(function (arr) {
	arr.forEach(function (item) {
		if (item.hasOwnProperty('remove')) {
			return;
		}
		Object.defineProperty(item, 'remove', {
			configurable: true,
			enumerable: true,
			writable: true,
			value: function remove() {
				if (this.parentNode === null) {
					return;
				}
				this.parentNode.removeChild(this);
			}
		});
	});
})([Element.prototype, CharacterData.prototype, DocumentType.prototype]);
// Polyfills for MSIE
if (window.NodeList && !NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

// Helper function to get an element's exact position
function getPosition(el) {
	let xPos = 0;
	let yPos = 0;

	while (el) {
		if (el.tagName == "BODY") {
			// deal with browser quirks with body/window/document and page scroll
			let xScroll = el.scrollLeft || document.documentElement.scrollLeft;
			let yScroll = el.scrollTop || document.documentElement.scrollTop;

			xPos += (el.offsetLeft - xScroll + el.clientLeft);
			yPos += (el.offsetTop - yScroll + el.clientTop);
		} else {
			// for all other non-BODY elements
			xPos += (el.offsetLeft - el.scrollLeft + el.clientLeft);
			yPos += (el.offsetTop - el.scrollTop + el.clientTop);
		}

		el = el.offsetParent;
	}
	return {
		x: xPos,
		y: yPos
	};
}


let ystsMaxUploadSize = 0;

let YsInstaller = {
	getLoadingOverlay: function () {
		return document.getElementById("loading");
	},
	showLoading: function () {
		this.getLoadingOverlay().style.display = "block";
	},
	hideLoading: function () {
		this.getLoadingOverlay().style.display = "none";
	}
};

/*
 *
 * Right panel watcher
 *
 */

function ystsPositionElements()
{

	let headerOffsetHeight = document.getElementById('top-head').offsetHeight;

	if (document.getElementById('ysts_system_messages')) {
		let thpos = window.getComputedStyle(document.getElementById('top-head')).getPropertyValue('position');
		if (thpos == "fixed" ) {
			document.getElementById('ysts_system_messages').style.marginTop = (10 + headerOffsetHeight) + 'px';
		} else {
			document.getElementById('ysts_system_messages').style.marginTop = '10px';
		}
	}

	// Remainder is not used in the frontend
	if (!document.getElementById('left-col'))
	{
		return;
	}

	// Setup position of offcanvas and left column to match our main wrapper - can't just rely on height of Isis menu bar
	let offsetTop = document.getElementById('gslc').offsetTop;
	document.getElementById('left-col').style.top              = offsetTop + 'px';
	document.getElementById('offcanvas-right-panel').style.top = offsetTop + 'px';
	document.getElementById('offcanvas-left-panel').style.top  = offsetTop + 'px';
	document.querySelector('#offcanvas-right-panel .gsl-offcanvas-bar button.gsl-close').style.top  = 15 + offsetTop + 'px';

	// Handle admin templates that set padding on narrow devices
	let offsetLeft = document.getElementById('gslc').parentElement.offsetLeft;

	// No need to test if (offsetLeft > 0) { since we want to reset to zero afterwards
	document.getElementById('gslc').style.marginLeft = "-" + offsetLeft + 'px';
	document.getElementById('gslc').style.marginRight = "-" + offsetLeft + 'px';

	// Place the navigation drop downs in the right place
	let leftDropDowns = document.querySelectorAll('.left-nav .gsl-dropdown');

	for (let l = 0; l < leftDropDowns.length; l ++) {
		if (window.innerWidth < 960) {
			let data = JSON.parse(leftDropDowns[l].getAttribute('gsl-dropdown'));
			data.pos = 'bottom-left';
			leftDropDowns[l].setAttribute('gsl-dropdown', JSON.stringify(data));
			gslUIkit.dropdown(leftDropDowns[l], data);
		}
		else
		{
			let data = JSON.parse(leftDropDowns[l].getAttribute('gsl-dropdown'));
			data.pos = 'right-top';
			leftDropDowns[l].setAttribute('gsl-dropdown', JSON.stringify(data));
			gslUIkit.dropdown(leftDropDowns[l], data);
		}
	}

	if (typeof leftMenuTrigger != "undefined" && leftMenuTrigger == 3)
	{
		var leftColWidth = document.querySelector('#left-col .left-nav').offsetWidth;
		var leftColDisplay = getComputedStyle(document.querySelector('#left-col .left-nav li')).display;
		if (leftColDisplay == "inline-block")
		{
			document.getElementById('right-col').style.marginLeft = '0px';
			if (document.getElementById('top-head')) {
				document.getElementById('top-head').style.marginLeft = '0px';
				document.querySelector('#top-head .ys-titlebar').style.marginLeft = '0px';
			}
		}
		else
		{
			document.getElementById('right-col').style.marginLeft = leftColWidth + 'px';
			if (document.getElementById('top-head')) {
				document.getElementById('top-head').style.marginLeft = (leftColWidth-50) + 'px';
				document.querySelector('#top-head .ys-titlebar').style.marginLeft = '-5px';
			}
		}
	}
}

document.addEventListener('DOMContentLoaded', function () {

	gslUIkit.container = document.getElementById('gslc');

	let joomla4 = false;
	// Joomla 4 template
	if (document.querySelector('#sidebar-wrapper.sidebar-menu #sidebarmenu') || document.querySelector('#wrapper.d-flex.wrapper0'))
	{
		joomla4 = true;

		//let joomlaelements = document.querySelectorAll('#sidebar-wrapper.sidebar-wrapper.sidebar-menu, #subhead.subhead ');
		let joomlaelements = document.querySelectorAll('#subhead.subhead, #subhead-container.subhead');
		for (let j = 0; j < joomlaelements.length; j++) {
			joomlaelements[j].style.display = 'none';
		}
		// Need to remove the duplicates since they can break javascript
		joomlaelements = document.querySelectorAll('#header.header-item');
		for (let j = 0; j < joomlaelements.length; j++) {
			joomlaelements[j].remove();
		}

		// Toggle closed the left menu in Joomla 4
		let joomlaLeftMenu = document.getElementById('menu-collapse');
		if (joomlaLeftMenu)
		{
			const wrapper = document.getElementById('wrapper');

			if (wrapper.classList.contains('closed')) {
			}
			else
			{
				document.getElementById('gslc').classList.add('joomla-menu-open');

				/*
				// if we choose to close the left menu at each page load then we can use this
				var evt = new MouseEvent("click", {
					bubbles: true,
					cancelable: true,
					view: window
				});
				 joomlaLeftMenu.dispatchEvent(evt);
				 */
			}

			// Use Try/Catch for browsers with no support
			try {
				let observer = new MutationObserver(mutationRecords => {
					// console.log(mutationRecords);
					const wrapper = document.getElementById('wrapper');
					if (wrapper.classList.contains('closed')) {
						document.getElementById('gslc').classList.remove('joomla-menu-open');
					} else {
						document.getElementById('gslc').classList.add('joomla-menu-open');
					}
				});

				// observe everything except attributes
				observer.observe(wrapper, {
					childList: false, // observe direct children
					subtree: false, // and lower descendants too
					characterDataOldValue: false, // pass old data to callback
					attributes: true
				});
			}
			catch (e)
			{

			}

			// respond to Joomla left menu opening and closing
			joomlaLeftMenu.addEventListener('click', function()
			{
				const wrapper = document.getElementById('wrapper');
				if (wrapper.classList.contains('closed')) {
					document.getElementById('gslc').classList.remove('joomla-menu-open');
				}
				else {
					document.getElementById('gslc').classList.add('joomla-menu-open');
				}
			});
		}
		else
		{
			document.getElementById('gslc').classList.add('no-joomla-menu');
		}

		// Hide the sidebar
		var sidebarWrapper = document.getElementById('sidebar-wrapper');
		if (sidebarWrapper) {
			sidebarWrapper.classList.add('gsl-hide-sidebar');
		}
		document.getElementById('gslc').classList.add('gsl-hide-sidebar');
		/*
		document.getElementById('sidebar-wrapper').addEventListener('mouseout', () => {
			document.getElementById('sidebar-wrapper').classList.add('gsl-hide-sidebar');
			document.getElementById('gslc').classList.add('gsl-hide-sidebar');
		});
		 */
		document.querySelector('#right-col > .gsl-content').addEventListener('mouseover', () => {
			var sidebarWrapper = document.getElementById('sidebar-wrapper');
			if (sidebarWrapper) {
				sidebarWrapper.classList.add('gsl-hide-sidebar');
			document.getElementById('gslc').classList.add('gsl-hide-sidebar');
			}

			let wrapper = document.getElementById('menu-collapse');
			if (wrapper && document.getElementById('menu-collapse-icon').classList.contains('fa-toggle-on'))
			{
				wrapper.click();
			}
		});
		/*
		document.querySelector('#gslc .returntojoomla').addEventListener('mouseover', () => {
			let sbw = document.getElementById('sidebar-wrapper');
			if (sbw) {
				sbw.classList.remove('gsl-hide-sidebar');
			}
			document.getElementById('gslc').classList.remove('gsl-hide-sidebar');
			let wrapper = document.getElementById('menu-collapse');
			if (wrapper && document.getElementById('menu-collapse-icon').classList.contains('fa-toggle-off'))
			{
				wrapper.click();
			}
		});
		 */
	}
	else {
		// Clean up ISIS stuff etc.
		let joomlaelements = document.querySelectorAll('#isisJsData, body.admin header.header, .btn.btn-subhead, .subhead-collapse .subhead');
		for (let j = 0; j < joomlaelements.length; j++) {
			joomlaelements[j].remove();
		}
	}

	// remove phoca top menu
	if (document.querySelector('.ph-topmenu-navbar'))
	{
		document.querySelector('.ph-topmenu-navbar').style.display = 'none';
	}

	// Hide toggled left-menu if in click mode
	document.querySelectorAll('#right-col > .gsl-content, #right-col > #top-head').forEach(elem => {
		elem.addEventListener('mouseover', () => {
			if (typeof leftMenuTrigger != "undefined" && leftMenuTrigger == 3) {
				return;
			}
			if (document.getElementById('left-col') && document.getElementById('left-col') && !document.getElementById('left-col').classList.contains('hide-label')) {
				var elements = document.querySelectorAll('#left-col, #left-col .left-nav, .ysts-page-title');
				elements.forEach(function(element)
				{
					if (element.classList.contains('hide-label'))
					{
						element.classList.remove('hide-label');
					}
					else
					{
						element.classList.add('hide-label');
					}
				})
			}
		});
	});

	ystsPositionElements()

	// Handle 'flip' events - delay by 100 to make sure its a late event response!
	window.addEventListener('resize' , function () { setTimeout( function ()
	{
		ystsPositionElements();
	})} , 100);

	// Toggle actions
	let ystoolbar_wrappers = document.querySelectorAll(".ystoolbar_wrapper");

	// Isis scroll fix for positioning of elements underneath the subhead
	// MSIE workaround
	if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > 0) {
		var evt = document.createEvent('UIEvents');
		evt.initUIEvent('resize', true, false, window, 0);
		window.dispatchEvent(evt);
	}
	else {
		let resizeEvent = new Event('resize');
		window.dispatchEvent(resizeEvent);
	}

	/* Toggle site info */
	let toggles = document.querySelectorAll(".toggleSiteInfo");
	if (toggles.length)
	{
		for (let t = 0; t < toggles.length; t++)
		{
			toggles[t].addEventListener('click', function()
			{
				let content = document.querySelector(".item" + this.dataset.toggleid).innerHTML;
				document.querySelector("#offcanvas-right-panel .offcanvas-content").innerHTML = content;
			});
		}
	}

	// Process tooltips
	ys_setuptooltip(".hasYsTooltip");

	// toggle radio buttons and check box highlighting
	let inputNodes = document.querySelectorAll('input.gsl-hidden');
	for (let i = 0; i < inputNodes.length; i++) {
		inputNodes[i].addEventListener('change', function() {
			changeHiddenInput(this);
		});
	}

	// repeatable fields
	let jevContainer	= document.getElementById('jevents');
	if (jevContainer !== null) {
		let repeatables = jevContainer.querySelectorAll('div.subform-repeatable');
		for (let r = 0; r < repeatables.length; r++) {
			jQuery(repeatables[r]).on('subform-row-add', function (event, row) {
				if (typeof row !== 'undefined') {
					if (typeof editicalGslStyling == 'function') {
						editicalGslStyling(row);
					}
					let inputNodes = row.querySelectorAll('input.gsl-hidden');
					for (let i = 0; i < inputNodes.length; i++) {
						inputNodes[i].addEventListener('change', function () {
							changeHiddenInput(this);
						});
					}
				}
			});
		}
	}


});

function changeHiddenInput(input)
{
	let parentNode = input.parentNode;
	if (! parentNode.classList.contains('gsl-button-group'))
	{
		parentNode = input.parentNode.parentNode;
	}
	if (parentNode.classList.contains('gsl-button-group'))
	{
		let inputNodes = parentNode.querySelectorAll('input');
		for (let i = 0; i < inputNodes.length; i++)
		{
			let label = parentNode.querySelector('[for="' + inputNodes[i].id + '"]');
			if (label)
			{
				let activeClass = inputNodes[i].getAttribute('data-activeclass');
				if (inputNodes[i].checked)
				{
					if (activeClass)
					{
						label.classList.add('gsl-button-' + activeClass);
					}
					else if (input.value == 0)
					{
						label.classList.add('gsl-button-danger');
					}
					else
					{
						label.classList.add('gsl-button-primary');
					}
				}
				else
				{
					if (activeClass)
					{
						label.classList.remove('gsl-button-' + activeClass);
					}
					label.classList.remove('gsl-button-primary');
					label.classList.remove('gsl-button-danger');
				}
			}
		}
	}

}

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

		options.container = "#gslc";
		options.title = title;

		if (hoveritem.hasAttribute('title')) {
			hoveritem.removeAttribute('title');
		}

		gslUIkit.tooltip(hoveritem, options);
	});
}

function ys_setuppopover(selector)
{

	// Setup new ones
	var hoveritems = document.querySelectorAll(selector);
	hoveritems.forEach(function (hoveritem) {

		let title = hoveritem.getAttribute('data-yspoptitle') || hoveritem.getAttribute('data-original-title') || hoveritem.getAttribute('title');
		let body = hoveritem.getAttribute('data-yspopcontent') || hoveritem.getAttribute('data-content')  || hoveritem.getAttribute('data-bs-content') || '';
		let options = hoveritem.getAttribute('data-yspopoptions') || '{"mode" : "click, hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}';
		//options = '{ "offset" : 20,"delay" : 20, "pos" : "top", "duration" : 200}';
		options = JSON.parse(options);

		options.container = "#gslc";

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

		gslUIkit.tooltip(hoveritem, options);
	});
}

function ys_positionchevron_tooltip()
{
	var tt = document.querySelector('.gsl-tooltip.gsl-active');

	var activechevron = tt.querySelector('.popleft');
	var activechevronCR = activechevron.getBoundingClientRect();
	var polyline = tt.querySelector('.popleft polyline');
	var polylineCR = activechevron.getBoundingClientRect();

	activechevron.style.marginLeft = '-' + (activechevronCR.right - polylineCR.right + activechevron.offsetWidth)/2 + 'px';//linkLeftCFpopover + (popButtonCR.width - activechevron.offsetWidth) / 2 + 'px';

}

function ys_positionchevron(dropdown)
{
	var parentCR = dropdown.parentNode.getBoundingClientRect();
	var popCR = dropdown.getBoundingClientRect();

	var previousSibling = dropdown.previousSibling;
	while (previousSibling && previousSibling.nodeType !== 1) {
		previousSibling = previousSibling.previousSibling;
	}

	if (!previousSibling) {
		return;
	}

	var popButtonCR = previousSibling.getBoundingClientRect();

	// popover to the right of the element
	if (popCR.left > parentCR.right) {
		dropdown.querySelector('.popabove').style.display = 'none';
		dropdown.querySelector('.popbelow').style.display = 'none';

		var activechevron = dropdown.querySelector('.popleft');
		var activechevronCR = activechevron.getBoundingClientRect();
		var polyline = dropdown.querySelector('.popleft polyline');
		var polylineCR = polyline.getBoundingClientRect();

		var linkLeftCFpopover =  popButtonCR.left - popCR.left;
		// Defaults to 26 pixels based on width of
		activechevron.style.marginLeft = '-' + (activechevronCR.right - polylineCR.right + activechevron.offsetWidth)/2 + 'px';//linkLeftCFpopover + (popButtonCR.width - activechevron.offsetWidth) / 2 + 'px';
		return;
	}
	// popover is below element
	else if (popCR.y < parentCR.y) {
		var activechevron = dropdown.querySelector('.popabove');
		var inactivechevron = dropdown.querySelector('.popbelow');
	}
	else {
		var activechevron = dropdown.querySelector('.popbelow');
		var inactivechevron = dropdown.querySelector('.popabove');
	}
	activechevron.style.display = 'block';
	inactivechevron.style.display = 'none';
	dropdown.querySelector('.popleft').style.display = 'none';

	var linkLeftCFpopover =  popButtonCR.left - popCR.left;
	activechevron.style.marginLeft = linkLeftCFpopover + (popButtonCR.width - activechevron.offsetWidth) / 2 + 'px';
}

function ys_resizepopover(dropdown)
{
	var popCR = dropdown.getBoundingClientRect();

	var poptitle = dropdown.querySelector('.ys-popover-title');
	let height = 0;
	if (poptitle) {
		var poptitleCR = poptitle.getBoundingClientRect();
		height += poptitleCR.height;
	}
	var popbody = dropdown.querySelector('.ys-popover-body');
	if (popbody ) {
		var popbodyCR = popbody.getBoundingClientRect();
		height += popbodyCR.height;
	}

	dropdown.style.minHeight = height + 4 + 'px';
}

function ys_popover(selector) {
	document.addEventListener('DOMContentLoaded', function () {
		ys_setuppopover(selector || ".ys-popover");
	});
}
function ys_tooltip(selector) {
	document.addEventListener('DOMContentLoaded', function () {
		ys_setuptooltip(selector || ".ys-tooltip");
	});
}

// Call as ys_popover(".ys-popover");

var oldtop =  window.pageYOffset;
// Sticky scrolling
function stickyScroll() {
	let tophead = document.getElementById('top-head');

	// Get the table header
	let table = document.querySelector(".mainlist table.gsl-table ");
	let thead = document.querySelector(".mainlist table.gsl-table thead");

	if (!table || !thead || !tophead)
	{
		return;
	}

	// Get the offset position of the table heading
	let sticky = table.getBoundingClientRect().top;

	// ('oldtop : ' + oldtop + ' windowy: ' + window.pageYOffset + ' sticky: ' + sticky);
	// Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
	if ( window.pageYOffset >= sticky ) {
		// ('sticky 1');
		if (window.pageYOffset != oldtop) {
			oldtop =  window.pageYOffset;
			// console.log('sticky 2');
			if (window.getComputedStyle(tophead).getPropertyValue('position') == "fixed") {
				// console.log('fixed');
				thead.style.top = (tophead.offsetTop + tophead.offsetHeight) + 'px';
			} else {
				// console.log('Not fixed');
				thead.style.top = '0px';
			}
			thead.classList.add("sticky")
		}
		thead.style.width = table.offsetWidth + 'px';

	} else {
		// console.log('not sticky');
		thead.style.top = '0px';
		thead.classList.remove("sticky");
	}
	ystsPositionElements();
}
window.addEventListener('scroll', stickyScroll);
//window.addEventListener('resize', stickyScroll);


window.addEventListener('load', function () {
// Special tooltips for select based filters
	let filters = document.querySelectorAll('.js-stools-field-filter select');

	for (let f = 0; f < filters.length; f++) {
		let filter = filters[f];
		let options = {};

		options.container = "#gslc";
		// tags filter may be empty!
		options.title = filter.options.length > 0 ? filter.options[0].innerText : '';

		gslUIkit.tooltip(filter, options);

		// in case replaced by chosen
		let filterid = filter.id;
		filterid = filterid.replace('[', '').replace(']', '');
		if (document.querySelector('#' + filterid + '_chzn ul')) {
			gslUIkit.tooltip(document.querySelector('#' + filterid + '_chzn ul'), options);
		}
	}
});

function setupActionButtons(currenturl)
{
	let buttons = document.querySelectorAll('.ys-gsl-action-buttons button');
	for (let b=0; b < buttons.length; b++){

		if(buttons[b].onclick)
		{
			let clickaction = buttons[b].onclick
			buttons[b].onclick = null;
			let self = buttons[b];
			buttons[b].addEventListener('click', function(onclk, evt) {

				if (this.classList.contains('disabledToolbarItem'))
				{
					// action is disabled
					alert('not allowed');
					return;
				}
				if (typeof onclk == 'function')
				{
					//onclk(evt).bind(this);
					onclk.call(this, evt);
				}
			}.bind(self, clickaction));
		}

		buttons[b].addEventListener('click', function(evt) {
			if(document.adminForm.boxchecked.value==0){
				evt.stopPropagation();
				evt.preventDefault();
			}
		});
	}

	gslUIkit.util.on('#offcanvas-right-panel', 'hidden', function () {
		if (document.querySelector('#offcanvas-right-panel #progressModal')) {
			window.location.replace(currenturl);
		}
	});

	gslUIkit.util.on('#offcanvas-left-panel', 'show', function () {
		if (document.querySelector('#offcanvas-left-panel #uploadModal')) {

			let ids = document.querySelectorAll('#siteList .row_checkbox input:checked');
			let minsize = 999999999;
			for (let i=0; i < ids.length; i++)
			{
				let size = document.querySelector('div.item' + ids[i].value + ' .upload_max_filesize');
				let size2 = document.querySelector('div.item' + ids[i].value + ' .post_max_size');
				if (size && parseFloat(size.innerHTML) + 0 < minsize)
				{
					minsize = parseFloat(size.innerHTML) + 0 ;
				}
				if (size2 && parseFloat(size2.innerHTML) + 0 < minsize)
				{
					minsize = parseFloat(size2.innerHTML) + 0 ;
				}
			}
			if (minsize == 999999999)
			{
				document.querySelector('#uploadModal .MAXUPLOAD').innerHTML = "?? MB";
			}
			else
			{

				ystsMaxUploadSize = minsize;

				minsize = minsize / 1024 / 1024;

				document.querySelector('#uploadModal .MAXUPLOAD').innerHTML = minsize.toFixed(2) + " MB";
			}
		}
	});
}

window.addEventListener('load', function() {

	var ysDropZone = document.getElementById("ysDropZone");
	if (!ysDropZone) {
		return;
	}
	ysDropZone.ondragover = ysDropZone.ondragenter = function(event) {
		event.stopPropagation();
		event.preventDefault();
	}

	ysDropZone.ondrop = function(event) {
		event.stopPropagation();
		event.preventDefault();

		var filesArray = event.dataTransfer.files;

		handleExtensionFile(filesArray);
	}

	document.getElementById("ysFileSelect").addEventListener("click", function(e) {
		if (document.getElementById("extensionfile")) {
			document.getElementById("extensionfile").click();
		}
		e.preventDefault(); // prevent navigation to "#"
	}, false);

});

// If loading from com_categories need to move the system messages
document.addEventListener('DOMContentLoaded', function () {
	let msgel = document.getElementById("system-message-container");

	// Not in Joomla 4 +
	let newmsgel = document.getElementById("ysts_system_messages");
	if (newmsgel) {
		if (msgel && msgel.parentNode && msgel.parentNode.id != "ysts_system_messages") {
			newmsgel.innerHTML = "";
			newmsgel.appendChild(msgel);
		}
		if (!msgel) {
			newmsgel.innerHTML = "&nbsp;";
		}
	}

	let maincontainer = document.getElementById("j-main-container");
	if (maincontainer) {
		maincontainer.classList.remove('span10');
		maincontainer.classList.add('span12');
	}

	let sidebar = document.getElementById("j-sidebar-container");
	if (sidebar)
	{
		sidebar.parentNode.removeChild(sidebar);
	}

/*
I could merge in font-awesome icons this way
	gslUIkit.util.ready(function () {
		gslUIkit.icon.add('fa-users', '<span class="fas fa-users fa-fw" aria-hidden="true"></span>');
	});
*/
});
