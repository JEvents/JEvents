/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view_detail.js 1539 2010-12-07 10:30:01Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

var myFaderTimeout=null;
var interval=10000;
if (myFaderTimeout) clearTimeout(myFaderTimeout);

var opacities = new Array();
var increments = 10;
var pause = 50;
var currentOpacity = 0;

for (var i=0;i<=increments ;i++){
	opacities[i] = (i*1.0)/(increments*1.0);
}

function closeAllDialogs(){
	currentOpacity=0;
	if (myFaderTimeout) clearTimeout(myFaderTimeout);
	var myDiv = document.getElementById("action_dialog");
	if (myDiv) myDiv.style.visibility="hidden";
	var myDiv = document.getElementById("ical_dialog");
	if (myDiv) myDiv.style.visibility="hidden";	
}

function clickEditButton(){
	if (window.ie6) {
		var action = document.getElementById('action_dialog');
		action.style.display="block";
		return;
	}

	closeAllDialogs();
	if (currentOpacity<0) currentOpacity = 0;
	jevFadeIn("action_dialog");
}

function clickIcalSaveButton(){
	closeAllDialogs();
	if (currentOpacity<0) currentOpacity = 0;
	jevFadeIn("action_dialog");
	return false;
}

function closedialog() {
	if (window.ie6) {
		var action = document.getElementById('action_dialog');
		action.style.display="none";
		return;
	}

	if (currentOpacity>opacities.length) currentOpacity =opacities.length;
	jevFadeOut("action_dialog");
}

function clickIcalButton(){
	closeAllDialogs();
	if (currentOpacity<0) currentOpacity = 0;
	jevFadeIn("ical_dialog");
}

function closeical() {
	if (currentOpacity>opacities.length) currentOpacity =opacities.length;
	jevFadeOut("ical_dialog");
}

function jevFadeIn(dlg) {
	var myDiv = document.getElementById(dlg);
	currentOpacity++;
	if (currentOpacity>=opacities.length){
		if (myFaderTimeout) clearTimeout(myFaderTimeout);
	}
	else {
		//window.status=opacities[currentOpacity];
		myDiv.style.opacity=opacities[currentOpacity];
		myDiv.style.filter="alpha(opacity="+(100*opacities[currentOpacity])+")";
		myDiv.style.visibility="visible";	
		if (myFaderTimeout) clearTimeout(myFaderTimeout);
		myFaderTimeout = setTimeout("jevFadeIn('"+dlg+"')",pause);
	}
}

function jevFadeOut(dlg) {
	var myDiv = document.getElementById(dlg);
	if (!myDiv) return;
	currentOpacity--;
	if (currentOpacity<=0){
		if (myFaderTimeout) clearTimeout(myFaderTimeout);
		myDiv.style.visibility="hidden";
	}
	else {
		myDiv.style.opacity=opacities[currentOpacity];
		//window.status = opacities[currentOpacity];
		myDiv.style.filter="alpha(opacity="+(100*opacities[currentOpacity])+")";
		if (myFaderTimeout) clearTimeout(myFaderTimeout);
		myFaderTimeout = setTimeout("jevFadeOut('"+dlg+"')",pause);
	}
}

function setupIE6(){
	if (window.ie6) {
		var action = document.getElementById('action_dialog');	
		if(action){
			action.style.visibility="visible";
			action.style.display="none";
		}
	}
}
window.addEvent('domready',function(){setupIE6();});


function jevSetupAddLink(){
	var tds = $$("td.cal_td_today");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.cal_td_daysnoevents");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.cal_td_dayshasevents");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.jev_daynoevents");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.jev_today");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}

	var tds = $$("div.jev_daynum");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}

	var tds = $$("td.jev_daynum");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}

// for ext layout
	var tds = $$("td.todayclr");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.weekdayclr");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.sundayclr");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.sundayemptyclr");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}

	// for alternative layout
	var tds = $$(".cal_div_daysnoevents");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$(".cal_div_today");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
        
        // flat layout
	var tds = $$("td.cal_today");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.cal_daysnoevents");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
	var tds = $$("td.cal_dayshasevents");
	for (var i=0; i<tds.length; i++)
	{
		element = tds[i];
		element.addEvent('mouseover', function(){ this.addClass('showjevadd'); });
		element.addEvent('mouseout', function(){ this.removeClass('showjevadd'); });
	}
}

window.addEvent('domready',function(){jevSetupAddLink();});
