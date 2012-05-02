/**
* JEvents Component for Joomla 1.5.x
*
* @version     $Id: editical.js 3576 2012-05-01 14:11:04Z geraintedwards $
* @package     JEvents
* @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
* @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
* @link        http://www.jevents.net
*/

Date.prototype.getYMD =  function()
{
	month = "0"+(this.getMonth()+1);
	day = "0"+this.getDate();
	// MSIE 7 still doesn't support negative num1 in substr!!
	var result = this.getFullYear()+"-"+month.substr(month.length-2)+"-"+day.substr(day.length-2);
	//alert(result);
	return result;
};
Date.prototype.addDays = function(days)
{
	return new Date(this.getTime() + days*24*60*60*1000);
};
Date.prototype.dateFromYMD = function(ymd){
	parts = ymd.split("-");
	//alert(parts[0]+" "+parts[1]+" "+parts[2]);
	temp = new Date(parts[0],parts[1]-1,parts[2],0,0,0,0);
	return temp;
};

function highlightElem(elem){
	elem.style.color="red";
	elem.style.fontWeight="bold";
	document.getElementById("valid_dates").value=0;
}
function normaliseElem(elem) {
	elem.style.color="";
	elem.style.fontWeight="";
	document.getElementById("valid_dates").value=1;
}

function checkTimeFormat(time){
	if (time.value.indexOf(":")>0){
		normaliseElem(time);
		return true;
	}
	else if (time.value.indexOf("-")>0 || time.value.indexOf(".")>0 || time.value.indexOf(",")>0){
		time.value = time.value.replace(/-/g,":");
		time.value = time.value.replace(/\./g,":");
		time.value = time.value.replace(/,/g,":");
		normaliseElem(time);
		return true;
	}
	else if (time.value.length>2 && time.value.length<5){
		temp = time.value.substr(0,time.value.length-2);
		time.value = temp + ":"+ time.value.substr(time.value.length-2);
		normaliseElem(time);
		return true;
	}
	else {
		alert(handm);
		highlightElem(time);
		return false;
	}
}

function checkValidTime(time){
	parts = time.value.split(":");
	if (parts.length!=2) {
		return false;
	}
	parts[0] = parseInt(parts[0],10);
	parts[1] = parseInt(parts[1],10);
	if (parts[0]==24 && parts[1]==0){
		parts[0]=0;
	}
	if (parts[0]<0 || parts[0]>=24){
		return false
	}
	if (parts[1]<0 || parts[1]>=60 ){
		return false;
	}
	parts[0] = "00"+parts[0]+"";
	parts[1] = "00"+parts[1]+"";
	parts[0] = parts[0].substring(parts[0].length-2);
	parts[1] = parts[1].substring(parts[1].length-2);
	time.value = parts[0]+":"+parts[1];
	return true;
}

function checkTime(time){
	if (!checkTimeFormat(time)){
		return false;
	}
	set12hTime(time);

	if (!checkValidTime(time)){
		alert(invalidtime);
		highlightElem(time);
		return false;
	}
	else normaliseElem(time);


	checkEndTime();
}

/*
* Does nothing at this stage
*/
function checkInterval() {
	updateRepeatWarning();

}

function set12hTime(time24h){
	if (time24h.id=="end_time"){
		var time = document.getElementById("end_12h");
		pm   = document.getElementById("endPM");
		am   = document.getElementById("endAM");
	}
	else {
		var time = document.getElementById("start_12h");
		pm   = document.getElementById("startPM");
		am   = document.getElementById("startAM");
	}

	parts = time24h.value.split(":");
	hour  = parseInt(parts[0], 10);
	min   = parseInt(parts[1], 10);
	if ((hour >= 12) ){
		ampm = pm;
	} else {
		ampm = am;
	}
	if (hour > 12){
		hour = hour - 12;
	}
	if (hour == 0) hour = 12;

	if (hour < 10) hour = "0"+hour;
	if (min  < 10) min  = "0"+min;
	time.value = hour+":"+min;
	ampm.checked = true;
}


function set24hTime(time12h){
	if (time12h.id=="end_12h"){
		time = document.getElementById("end_time");
		pm = document.getElementById("endPM");
	}
	else {
		time = document.getElementById("start_time");
		pm = document.getElementById("startPM");
	}

	if (!checkValidTime(time12h)){
		alert(invalidtime);
		highlightElem(time12h);
		return false;
	}
	else {
		normaliseElem(time12h);
		parts = time12h.value.split(":");
		hour = parseInt(parts[0],10);
		if (pm.checked) {
			if (hour < 12) {
				time.value = (hour+12)+":"+parts[1];
			} else {
				time.value = time12h.value;
			}
		}
		else {
			/*
			if (hour == 0) {
			time.value = "12:"+parts[1];
			}
			*/
			if (hour == 12) {
				time.value = "00:"+parts[1];
			} else {
				time.value = time12h.value;
			}
		}
	}
	if (!checkValidTime(time)){
		alert(invalidtime);
		highlightElem(time12h);
		return false;
	}
	else {
		normaliseElem(time12h);
		return true;
	}
}

function checkEndTime() {
	updateRepeatWarning();

	var noendchecked = document.adminForm.noendtime.checked;

	start_time = document.getElementById("start_time");
	end_time = document.getElementById("end_time");

	endfield = (document.adminForm.view12Hour.checked) ? document.getElementById("end_12h") : end_time;
	end_date = document.getElementById("publish_down");

	if (noendchecked){
		end_time.value=start_time.value;
		normaliseElem(endfield);
		normaliseElem(end_date);
	}

	starttimeparts = start_time.value.split(":");
	start_date = document.getElementById("publish_up");
	startdateparts = start_date.value.split("-");
	startDate = new Date(startdateparts[0],parseInt(startdateparts[1],10)-1,startdateparts[2],starttimeparts[0],starttimeparts[1],0);

	endtimeparts = (end_time.value=="00:00") ? [23,59] : end_time.value.split(":");
	enddateparts = end_date.value.split("-");
	endDate = new Date(enddateparts[0],parseInt(enddateparts[1],10)-1,enddateparts[2],endtimeparts[0],endtimeparts[1],0);
	//alert(endDate +" vs "+startDate);

	var jevmultiday = document.getElementById('jevmultiday');
	if (end_date.value>start_date.value){
		jevmultiday.style.display='block';
	}
	else {
		jevmultiday.style.display='none';
	}

	if (endDate>=startDate){
		normaliseElem(endfield);
		normaliseElem(end_date);
		return true;
	}
	else {
		highlightElem(end_date);
		highlightElem(endfield);
		//alert("end date and time must be after start date and time");
		return false;
	}
}

function check12hTime(time12h){
	if (!checkTimeFormat(time12h)){
		return false;
	}
	set24hTime(time12h);
	checkEndTime();
}

function checkDates(elem){
	forceValidDate(elem);
	setEndDateWhenNotRepeating();
	checkEndTime();
	checkUntil();
	updateRepeatWarning();
}

function checkUntil(){

	start_date = document.getElementById("publish_up");
	startdateparts = start_date.value.split("-");
	startDate = new Date(startdateparts[0],parseInt(startdateparts[1],10)-1,startdateparts[2],0,0,0,0);

	until_date = document.getElementById("until");
	untildateparts = until_date.value.split("-");
	untilDate = new Date(untildateparts[0],parseInt(untildateparts[1],10)-1,untildateparts[2],0,0,0,0);

	if (untilDate<startDate){
		until_date.value = start_date.value;
	}

}

function setEndDateWhenNotRepeating(){
	var norepeat = document.getElementById("NONE");
	start_date = document.getElementById("publish_up");
	end_date = document.getElementById("publish_down");

	startdateparts = start_date.value.split("-");
	startDate = new Date(startdateparts[0],parseInt(startdateparts[1],10)-1,startdateparts[2],1,1,0);
	enddateparts = end_date.value.split("-");
	endDate = new Date(enddateparts[0],parseInt(enddateparts[1],10)-1,enddateparts[2],1,1,0);
	if (startDate>endDate){
		end_date.value = start_date.value;
		normaliseElem(end_date);
	}
}

function forceValidDate(elem){
	oldDate = new Date();
	oldDate = oldDate.dateFromYMD(elem.value);
	newDate = oldDate.getYMD();
	if (newDate!=elem.value) {
		elem.value = newDate;
		alert(invalidcorrected);
	}
}

function toggleView12Hour(){
	if (document.adminForm.view12Hour.checked) {
		document.getElementById('start_24h_area').style.display="none";
		document.getElementById('end_24h_area').style.display="none";
		document.getElementById('start_12h_area').style.display="inline";
		document.getElementById('end_12h_area').style.display="inline";
	} else {
		document.getElementById('start_24h_area').style.display="inline";
		document.getElementById('end_24h_area').style.display="inline";
		document.getElementById('start_12h_area').style.display="none";
		document.getElementById('end_12h_area').style.display="none";
	}
}

function toggleAMPM(elem)
{
	if (elem=="startAM" || elem=="startPM"){
		time12h = document.getElementById("start_12h");
	}
	else {
		time12h = document.getElementById("end_12h");
	}
	set24hTime(time12h);
	checkEndTime();
}

function toggleAllDayEvent()
{
	var checked = document.adminForm.allDayEvent.checked;
	if (checked) document.adminForm.noendtime.checked = false;
	var noendchecked = document.adminForm.noendtime.checked;

	var starttime = document.adminForm.start_time;
	var startdate = document.adminForm.publish_up;
	var endtime = document.adminForm.end_time;
	var enddate = document.adminForm.publish_down;
	var spm   = document.getElementById("startPM");
	var	sam   = document.getElementById("startAM");
	var epm   = document.getElementById("endPM");
	document.adminForm.noendtime.checked
	var	eam   = document.getElementById("endAM");

	if (document.adminForm.view12Hour.checked){
		hide_start = document.adminForm.start_12h;
		hide_end   = document.adminForm.end_12h;
	} else {
		hide_start = starttime;
		hide_end   = endtime;
	}

	hide_start12 = document.adminForm.start_12h;
	hide_end12   = document.adminForm.end_12h;
	hide_start = starttime;
	hide_end   = endtime;

	temp = new Date();
	temp = temp.dateFromYMD(startdate.value);

	if (checked){
		// set 24h fields
		//temp = temp.addDays(1);
		starttime.value="00:00";
		starttime.disabled=true;
		hide_start.disabled=true;
		hide_start12.disabled=true;
		sam.disabled=true;
		spm.disabled=true;

		var sd = temp.getYMD();
		temp = temp.dateFromYMD(enddate.value);
		var ed = temp.getYMD();
		if (ed<sd) {
			enddate.value = temp.getYMD();
		}
		endtime.value="23:59";

		if (!noendchecked){
			endtime.disabled=true;
			hide_end.disabled=true;
			hide_end12.disabled=true;

			eam.disabled=true;
			epm.disabled=true;
		}
	}
	else {
		// set 24h fields
		hide_start.disabled=false;
		hide_start12.disabled=false;
		starttime.value="08:00";
		starttime.disabled=false;

		sam.disabled=false;
		spm.disabled=false;

		if (!noendchecked){
			hide_end.disabled=false;
			hide_end12.disabled=false;
			endtime.value="17:00";
			endtime.disabled=false;
			var sd = temp.getYMD();
			temp = temp.dateFromYMD(enddate.value);
			var ed = temp.getYMD();
			if (ed<sd) {
				enddate.value = temp.getYMD();
			}

			eam.disabled=false;
			epm.disabled=false;
		}
		else {
			endtime.value=starttime.value;
		}

	}

	if (document.adminForm.start_12h){
		// move to 12h fields
		set12hTime(starttime);
		set12hTime(endtime);
	}
	updateRepeatWarning();

}

function toggleNoEndTime(){
	var checked = document.adminForm.noendtime.checked;
	if (checked && document.adminForm.allDayEvent.checked) {
		document.adminForm.allDayEvent.checked = false;
		toggleAllDayEvent();
	}

	var alldaychecked = document.adminForm.allDayEvent.checked;
	var endtime = document.adminForm.end_time;
	var enddate = document.adminForm.publish_down;
	var starttime = document.adminForm.start_time;
	var epm   = document.getElementById("endPM");
	var	eam   = document.getElementById("endAM");

	if (document.adminForm.view12Hour.checked){
		hide_end   = document.adminForm.end_12h;
	} else {
		hide_end   = endtime;
	}

	hide_end12   = document.adminForm.end_12h;
	hide_end   = endtime;

	if (checked || alldaychecked){
		// set 24h fields
		endtime.value=starttime.value;
		endtime.disabled=true;
		hide_end.disabled=true;
		hide_end12.disabled=true;

		eam.disabled=true;
		epm.disabled=true;

		checkTime(endtime);
	}
	else {
		// set 24h fields
		hide_end.disabled=false;
		hide_end12.disabled=false;
		//endtime.value="17:00";
		endtime.disabled=false;

		eam.disabled=false;
		epm.disabled=false;

	}

	if (document.adminForm.start_12h){
		// move to 12h fields
		set12hTime(endtime);
	}

	updateRepeatWarning();
}

function toggleCountUntil(cu){
	inputtypes = new Array("cu_count","cu_until");
	for (var i=0;i<inputtypes.length;i++) {
		inputtype = inputtypes[i];
		elem = document.getElementById(inputtype);
		inputs = elem.getElementsByTagName('input');
		for (var e=0;e<inputs.length;e++){
			inputelem = inputs[e];
			if (inputelem.name!="countuntil"){
				if (inputtype==cu){
					inputelem.disabled = false;
					inputelem.parentNode.style.backgroundColor="#ffffff";
				}
				else {
					inputelem.disabled = true;
					inputelem.parentNode.style.backgroundColor="#dddddd";
				}
			}
		}
	}
	updateRepeatWarning();

}

function toggleWhichBy(wb)
{
	inputtypes = new Array("byyearday","byweekno","bymonthday","bymonth","byday");
	for (var i=0;i<inputtypes.length;i++) {
		inputtype = inputtypes[i];
		elem = document.getElementById(inputtype);
		inputs = elem.getElementsByTagName('input');
		for (var e=0;e<inputs.length;e++){
			inputelem = inputs[e];
			if (inputelem.name!="whichby"){
				if (inputtype==wb){
					inputelem.disabled = false;
					inputelem.parentNode.style.backgroundColor="#ffffff";
				}
				else {
					inputelem.disabled = true;
					inputelem.parentNode.style.backgroundColor="#dddddd";
				}
			}

		}
	}
	updateRepeatWarning();

}

function toggleFreq(freq , setup)
{
	var myDiv = document.getElementById('interval_div');
	var byyearday = document.getElementById('byyearday');
	var byweekno = document.getElementById('byweekno');
	var bymonthday = document.getElementById('bymonthday');
	var bymonth = document.getElementById('bymonth');
	var byday = document.getElementById('byday');
	var weekofmonth = document.getElementById('weekofmonth');
	var intervalLabel = document.getElementById('interval_label');
	switch (freq) {
		case "NONE":
		{
			myDiv.style.display="none";
			byyearday.style.display="none";
			bymonth.style.display="none";
			byweekno.style.display="none";
			bymonthday.style.display="none";
			byday.style.display="none";

			// must also reset freq to 1 and count to 1
			document.getElementById('rinterval').value="1";
			document.getElementById('count').value="1";
			document.getElementById('cuc').checked='checked';
			toggleCountUntil('cu_count');
		}
		break;
		case "YEARLY":
		{
			intervalLabel.innerHTML=jevyears;
			myDiv.style.display="block";
			byyearday.style.display="block";
			document.getElementById('jevbyd').checked="checked";
			toggleWhichBy("byyearday");
			bymonth.style.display="none";
			byweekno.style.display="none";
			bymonthday.style.display="none";
			byday.style.display="none";
		}
		break;
		case "MONTHLY":
		{
			intervalLabel.innerHTML=jevmonths;
			myDiv.style.display="block";
			byyearday.style.display="none";
			bymonth.style.display="none";
			byweekno.style.display="none";
			bymonthday.style.display="block";
			document.getElementById('jevbmd').checked="checked";
			toggleWhichBy("bymonthday");
			byday.style.display="block";
			weekofmonth.style.display="block";
			if (!setup) toggleWeekNums(true);
		}
		break;
		case "WEEKLY":
		{
			intervalLabel.innerHTML=jevweeks;
			myDiv.style.display="block";
			byyearday.style.display="none";
			bymonth.style.display="none";
			byweekno.style.display="none";
			bymonthday.style.display="none";
			byday.style.display="block";
			document.getElementById('jevbd').checked="checked";
			toggleWhichBy("byday");
			weekofmonth.style.display="none";
			// always set week nums false for weekly events
			toggleWeekNums(false);
		}
		break;
		case "DAILY":
		{
			intervalLabel.innerHTML=jevdays;
			myDiv.style.display="block";
			byyearday.style.display="none";
			bymonth.style.display="none";
			byweekno.style.display="none";
			bymonthday.style.display="none";
			byday.style.display="none";
			document.getElementById('jevbd').checked="checked";
			//toggleWhichBy("byday");
			weekofmonth.style.display="none";
		}
		break;
		case "IRREGULAR":
		{
			intervalLabel.innerHTML=jevirregular;
			myDiv.style.display="block";
			byyearday.style.display="none";
			bymonth.style.display="none";
			byweekno.style.display="none";
			bymonthday.style.display="none";
			byday.style.display="none";
			document.getElementById('jevirregular').checked="checked";
			weekofmonth.style.display="none";
		}
		break;
	}

	updateRepeatWarning();
}

function fixRepeatDates(){
	start_time = document.getElementById("start_time");
	starttimeparts = start_time.value.split(":");
	start_date = document.getElementById("publish_up");
	startdateparts = start_date.value.split("-");
	startDate = new Date(startdateparts[0],parseInt(startdateparts[1],10)-1,startdateparts[2],0,0,0,0);
	bmd = document.adminForm.bymonthday;
	if (bmd.value.indexOf(",")<=0) {
		bmd.value = parseInt(startdateparts[2],10);
	}

	byd = document.adminForm.byyearday;
	byddir = document.adminForm.byd_direction;
	if (byd.value.indexOf(",")<=0) {
		yearStart = new Date(startdateparts[0],0,0,0,0,0,0);
		// count back from jan 1
		yearEnd = new Date(Math.round(startdateparts[0])+1,0,1,0,0,0,0);
		if (byddir.checked){
			days = ((yearEnd-startDate)/(24*60*60*1000));
			//byd.value = parseInt(days,10);
			byd.value = Math.round(days);
		}
		else {
			days = ((startDate-yearStart)/(24*60*60*1000));
			byd.value = Math.round(days);
		}
	}

	bmd = document.adminForm.bymonthday;
	bmddir = document.adminForm.bmd_direction;
	if (bmd.value.indexOf(",")<=0) {
		monthStart = new Date(startdateparts[0],parseInt(startdateparts[1],10)-1,0,0,0,0,0);
		monthEnd = new Date(startdateparts[0],parseInt(startdateparts[1],10),0,0,0,0,0);
		if (bmddir.checked){
			days = 1+monthEnd.getDate()-startDate.getDate();
			bmd.value = parseInt(days,10);
		}
		else {
			days = startDate.getDate();
			bmd.value = parseInt(days,10);
		}
	}

	// variable bd is reserved in MSIE 8 ?
	var bd = document.adminForm["weekdays[]"];
	for(var day=0;day<bd.length;day++){
		bd[day].checked=false;
	}
	bd[startDate.getDay()].checked=true;

	unt = document.getElementById('until');
	unt.value = start_date.value;

	updateRepeatWarning();

}

function toggleWeekNums(newstate){
	wn = document.adminForm["weeknums[]"];
	for(var w=0;w<wn.length;w++){
		wn[w].checked=newstate;
	}

	updateRepeatWarning();

}
/*
function setupIE6(){
if (window.ie6) {
var adminForm = document.getElementById('jevadminform');
adminForm.style.border="none";
adminForm.style.borderSpacing="0px";
var editor = document.getElementById('jeveditor');
editor.style.overflow = 'auto';
editor.style.width="550px";
}
}

window.addEvent('domready',function(){setupIE6();});
*/
/*
if (window.webkit) {
	window.addEvent('domready', function(){
		setTimeout("fixTabbedWebkit()",100);
	});
}
function fixTabbedWebkit(){
	if (typeof(tinyMCE)!="undefined"){
		tinyMCE.execCommand('mceRemoveControl', false, "jevcontent");
		tinyMCE.execCommand('mceAddControl', false, "jevcontent");
	}
}
*/
// sets the date for the page after save
function resetYMD(){
	start_date = document.getElementById("publish_up");

	startdateparts = start_date.value.split("-");
	//	startDate = new Date(startdateparts[0],parseInt(startdateparts[1],10)-1,startdateparts[2],1,1,0);
	document.adminForm.year.value = startdateparts[0];
	document.adminForm.month.value = startdateparts[1]
	document.adminForm.day.value = startdateparts[2]
}


function updateRepeatWarning(){
	if (document.adminForm.updaterepeats){
		document.adminForm.updaterepeats.value = 1;
	}
}

/* Check for booking conflicts */

Element.implement ({
	formToJson: function(){
		var json = {};
		this.getElements('input, textarea, select').each(function(el){
			var name = el.name;
			var value = el.get('value');
			if (value === false || !name || el.disabled) return;
			// multi selects
			if (name.contains('[]') && (el.tagName.toLowerCase() =='select' ) && el.get('multiple')==true){
				name = name.substr(0,name.length-2);
				if (!json[name]) json[name] = [];
				el.getElements('option').each(function(opt){
					if (opt.selected ==true) json[name].push(opt.value);
				});
			}
			else if (name.contains('[]') && (el.type=='radio' || el.type=='checkbox') ){
				if (!json[name]) json[name] = [];
				if (el.checked==true) json[name].push(value);
			}
			else if (el.type=='radio' || el.type=='checkbox'){
				//alert(el+" "+el.name+ " "+el.checked+ " "+value);
				if (el.checked==true) {
					json[name] = value;
				}
			}
			else json[name] = value;
		});
		return json;
	}

});

function checkConflict(url, pressbutton, jsontoken, client, repeatid,  redirect){
	var requestObject = new Object();
	requestObject.error = false;
	requestObject.client = client;
	requestObject.token = jsontoken;
	requestObject.pressbutton = pressbutton;
	requestObject.repeatid = repeatid;
	requestObject.formdata = $(document.adminForm).formToJson();

	var doRedirect = (typeof redirect =='undefined') ?  1 : redirect;
	
	requestObject.redirect = doRedirect;
	var hasConflicts = false;

	var jSonRequest = new Request.JSON({
		'url':url,
		onSuccess: function(json, responsetext){
			if (!json){
				alert('could not check conflicts');
				$('jevoverlapwarning').style.display='none';
				if (doRedirect) submit2(pressbutton);
				else hasConflicts = true;
			}
			if (json.error){
				try {
					eval(json.error);
				}
				catch (e){
					alert('could not process error handler');
				}
			}
			else {
				if (json.allclear){
					$('jevoverlapwarning').style.display='none';
					if (doRedirect) submit2(pressbutton);
					else hasConflicts = false;
				}
				else {
					$('jevoverlapwarning').style.display='block';
					var container = $('jevoverlaps');
					container.innerHTML="";
					$A(json.overlaps).each (function(overlap){
						var elem = new Element ("a", {
							'href':overlap.url, 
							'target':'_blank'
						});
						elem.inject(container,'bottom');
						//elem.appendText (overlap.summary+ " ( "+overlap.startrepeat+" - "+overlap.endrepeat+")");
						elem.appendText (overlap.conflictMessage);
						var br = new Element ("br");
						br.inject(container,'bottom');
					});
					hasConflicts = true;
				}
			}
		},
		onFailure: function(x){
			alert('Something went wrong...'+x);
			hasConflicts = true;
		}
	}).post({
		'json':JSON.encode(requestObject)
		});
	return hasConflicts;
}

// fix for auto-rotating radio boxes in firefox !!!
// see http://www.ryancramer.com/journal/entries/radio_buttons_firefox/
window.addEvent ('domready', function() {
	try {
		if(Browser.firefox) {
			$("adminForm").autocomplete='off';
		}
	}
	catch(e){	
	}
}); 
