/**
 * @version    CVS: 1.7.4
 * @package    com_yoursites
 * @author     Geraint Edwards
 * @copyright  2017-2019 GWE Systems Ltd
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise
// https://stackoverflow.com/questions/36016327/how-to-make-promises-work-in-ie11#36018899
'use strict';

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
	// Setup position of offcanvas and left column to match our main wrapper - can't just rely on height of Isis menu bar
	let offsetTop = document.getElementById('gslc').offsetTop;
	document.getElementById('left-col').style.top              = offsetTop + 'px';
	document.getElementById('offcanvas-right-panel').style.top = offsetTop + 'px';
	document.getElementById('offcanvas-left-panel').style.top  = offsetTop + 'px';
	document.querySelector('#offcanvas-right-panel .gsl-offcanvas-bar button.gsl-close').style.top  = 15 + offsetTop + 'px';

	let headerOffsetHeight = document.getElementById('top-head').offsetHeight;

	if (window.getComputedStyle(document.getElementById('top-head')).getPropertyValue('position') == "fixed")
	{
		document.getElementById('ysts_system_messages').style.marginTop = (10 + headerOffsetHeight) + 'px';
	}
	else
	{
		document.getElementById('ysts_system_messages').style.marginTop = '10px';
	}

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
}

document.addEventListener('DOMContentLoaded', function () {

	gslUIkit.container = document.getElementById('gslc');

	// Clean up ISIS stuff etc.
	let joomlaelements = document.querySelectorAll('#isisJsData, header.header, .btn.btn-subhead');
	for (let j=0; j < joomlaelements.length; j++)
	{
		joomlaelements[j].remove();
	}

	ystsPositionElements()

	// Handle 'flip' events - delay by 100 to make sure its a late event response!
	window.addEventListener('resize' , function () { setTimeout( function ()
	{
		ystsPositionElements();
	})} , 100);

	// Toggle actions
	let ystoolbar_wrappers = document.querySelectorAll(".ystoolbar_wrapper");

	// Isis scroll fix for positioning of elements underneath the subhead
	let resizeEvent = new Event('resize');
	window.dispatchEvent(resizeEvent);

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
	ys_tooltip(".hasYsTooltip");

	// toggle radio buttons and check box highlighting
	let buttons = document.querySelectorAll('.gsl-button');
	if (buttons.length)
	{
		for (let b = 0; b < buttons.length; b++)
		{
			let inputNodes = buttons[b].parentNode.querySelectorAll('input');
			for (let i = 0; i < inputNodes.length; i++) {
				inputNodes[i].addEventListener('change', function() {
					if (this.parentNode.classList.contains('gsl-button-group'))
					{
						let inputNodes = this.parentNode.querySelectorAll('input');
						for (let i = 0; i < inputNodes.length; i++)
						{
							let label = this.parentNode.querySelector('[for="' + inputNodes[i].id + '"]');
							if (label)
							{
								if (inputNodes[i].checked)
								{
									if (this.value == 0)
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
									label.classList.remove('gsl-button-primary');
									label.classList.remove('gsl-button-danger');
								}
							}
						}
					}

				});
			}

		}
	}

});

// uikit popovers
// Polyfills for MSIE
if (window.NodeList && !NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

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

		gslUIkit.tooltip(hoveritem, options);
	});
}

function ys_setuppopover(selector)
{

	// Setup new ones
	var hoveritems = document.querySelectorAll(selector);
	hoveritems.forEach(function (hoveritem) {

		let title = hoveritem.getAttribute('data-yspoptitle') || hoveritem.getAttribute('title');
		let body = hoveritem.getAttribute('data-yspopcontent') || '';
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
		options.title = filter.options[0].innerText;

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

function clipboardAttach() {
	navigator.permissions.query({name: "clipboard-read"}).then(result => {
		if (result.state == "granted" || result.state == "prompt") {
			alert('permission '  + result.state);
			/* read the clipboard now */
			navigator.clipboard.readText().then(
				clipText => {
					document.getElementById("testclipboard").innerText = clipText;
					alert(clipText);
				}
			)
				.catch(err => {
					alert('Could not read the clipboard so we need to use fall back : ' + err)
				})           ;
		}
		else if (result.state == "denied") {
			alert('permission denied falling back ');
		}
	})
		.catch(err => {
			// This can happen if the user denies clipboard permissions:
			alert('Could not set permission to get clipboard automatically so we need to use fall back : ' + err)

			let button = document.getElementById('clipboardbutton');
			button.style.display = 'inline-block';
			button.addEventListener('click', e => {
				navigator.clipboard.readText().then(
					clipText => {
						document.getElementById("testclipboard").innerText = clipText;
						alert(clipText);
					}
				);
			});

		});

	document.addEventListener('paste', async e => {
		let text;
		alert(0);
		if (typeof navigator.clipboard !== 'undefined') {
			alert(1);
			text = await navigator.clipboard.readText()
			alert(2);
		}
		else {
			text = e.clipboardData.getData('text/plain');
		}
		document.getElementById("testclipboard").innerText = text;
		e.preventDefault();
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
	if (msgel)
	{
		let newmsgel = document.getElementById("ysts_system_messages");
		newmsgel.innerHTML = msgel.innerHTML;
		msgel.parentNode.removeChild(msgel);
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

});