/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: editicalJQ.js 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008--JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Methods missing in jQuery
// See http://stackoverflow.com/questions/23908283/jquery-associate-two-arrays-key-value-into-one-array
Array.prototype.associate = function (keys) {
  var result = {};

  this.forEach(function (el, i) {
    result[keys[i]] = el;
  });

  return result;
};
// from Mootools

// Polyfills for MSIE
if (window.NodeList && !NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

//
// my version
Date.prototype.clearTime =  function(){
	this.setHours(0);
	this.setMinutes(0);
	this.setSeconds(0);
	return this;
};

/** Returns the number of the week in year, as defined in ISO 8601. */
Date.prototype.getWeekNumber = function() {
	var d = new Date(this.getFullYear(), this.getMonth(), this.getDate(), 0, 0, 0);
	var DoW = d.getDay();
	d.setDate(d.getDate() - (DoW + 6) % 7 + 3); // Nearest Thu
	var ms = d.valueOf(); // GMT
	d.setMonth(0);
	d.setDate(4); // Thu in Week 1
	return Math.round((ms - d.valueOf()) / (7 * 864e5)) + 1;
};

var eventEditDateFormat = "Y-m-d";
//Date.defineParser(eventEditDateFormat.replace("d","%d").replace("m","%m").replace("Y","%Y"));

Date.prototype.jeventsParseDate = function (from ){

		var keys = {
			d: /[0-2]?[0-9]|3[01]/,
			H: /[01]?[0-9]|2[0-3]/,
			I: /0?[1-9]|1[0-2]/,
			M: /[0-5]?\d/,
			s: /\d+/,
			o: /[a-z]*/,
			p: /[ap]\.?m\.?/,
			y: /\d{2}|\d{4}/,
			Y: /\d{4}/,
			z: /Z|[+-]\d{2}(?::?\d{2})?/
		};

		keys.m = keys.I;
		keys.S = keys.M;

		var parsed = [];
		var re = eventEditDateFormat;
		re = re.replace(/\((?!\?)/g, '(?:') // make all groups non-capturing
		.replace(/ (?!\?|\*)/g, ',? ') // be forgiving with spaces and commas
		.replace(/([a-z])/gi,
			function(match, p1){
				var p = keys[p1];
				if (!p) return p1;
				parsed.push(p1);
				return '(' + p.source + ')';
			}
		);

		re = new RegExp('^' + re + '$', 'i');
		var handler = function(bits){
			bits = bits.slice(1).associate(parsed);
			var date = new Date().clearTime();
			// Brazil timezone problems when clocks change - a date of 20 Oct 2013 is parsed as 11pm on 19th October !!!
			date.setHours(6);
			year = bits.y || bits.Y;
			// set month to January  to ensure we can set days to 31 first!!!
			date.setMonth( 0);
			if (year != null) date.setYear( year);
			if ('d' in bits) date.setDate( bits.d);
			if ('m' in bits || bits.b || bits.B) date.setMonth( bits.m-1);

			return date;
		}

		var bits = re.exec(from);
		return (bits) ? (parsed = handler(bits)) : false;

	}


Date.prototype.getYMD =  function()
{
	month = "0"+(this.getMonth()+1);
	day = "0"+this.getDate();
	// MSIE 7 still doesn't support negative num1 in substr!!
	var result = eventEditDateFormat.replace("Y",this.getFullYear()).replace("m",month.substr(month.length-2)).replace("d",day.substr(day.length-2));
	//alert(result);
	return result;
};
Date.prototype.addDays = function(days)
{
	return new Date(this.getTime() + days*24*60*60*1000);
};
Date.prototype.dateFromYMD = function(ymd){
	var mydate = new Date();
	mydate  = mydate.jeventsParseDate(ymd);
	return mydate;
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
		/*
		parts = time.value.split(":");
		parts[0] = parseInt(parts[0],10);
		parts[1] = parseInt(parts[1],10);
		if (parts[0]>12){
			parts[0]-=12;
		}
		time.value = parts[0]+":"+parts[1];
		*/
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
	if (document.adminForm.view12Hour.checked){
		time.value = parts[0]+":"+parts[1];
	}
	else {
		time.value = parts[0]+":"+parts[1];
	}

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
		inactive_ampm = am;
	} else {
		ampm = am;
		inactive_ampm = pm;
	}
	if (hour > 12){
		hour = hour - 12;
	}
	if (hour == 0) hour = 12;

	//if (hour < 10) hour = "0"+hour;
	if (min  < 10) min  = "0"+min;
	time.value = hour+":"+min;
	ampm.checked = true;
	if (ampm.parentNode.classList.contains('gsl-button'))
	{
		ampm.parentNode.classList.add('gsl-button-primary');
		inactive_ampm.parentNode.classList.remove('gsl-button-primary');
	}
	if (ampm.parentNode.classList.contains('uk-button'))
	{
		ampm.parentNode.classList.add('uk-button-primary');
		inactive_ampm.parentNode.classList.remove('uk-button-primary');
	}
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
	startDate = new Date();
	startDate = startDate.dateFromYMD(start_date.value);
	startDate.setHours(starttimeparts[0]);
	startDate.setMinutes(starttimeparts[1]);

	endtimeparts = (end_time.value=="00:00") ? [23,59] : end_time.value.split(":");
	/*
	if (end_time.value=="00:00" && document.adminForm.view12Hour.checked)
	{
		end_time.value="11:59";
	}
	 */
	endDate = new Date();
	endDate = endDate.dateFromYMD(end_date.value);
	endDate.setHours(endtimeparts[0]);
	endDate.setMinutes(endtimeparts[1]);

	var jevmultiday = document.getElementById('jevmultiday');
	if (endDate.dateFromYMD(end_date.value)>startDate.dateFromYMD(start_date.value)){
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
	// only respond to calendar date selections
	if (!calendarDateClicked)
	{
		return;
	}

	forceValidDate(elem);
	setEndDateWhenNotRepeating(elem);
	checkEndTime();
	checkUntil();
	updateRepeatWarning();
        // update the by day type checkboxes
        fixRepeatDates();
        try {
		initialiseBootstrapButtons()
	}
	catch(e) {};

}

function reformatStartEndDates() {
	start_date = document.getElementById("publish_up");
	start_date2 = document.getElementById("publish_up2");
	startDate = new Date();
	startDate = startDate.dateFromYMD(start_date.value);
	start_date2.value = startDate.getFullYear()+"-"+(startDate.getMonth()+1)+"-"+startDate.getDate();

	end_date = document.getElementById("publish_down");
	end_date2 = document.getElementById("publish_down2");
	endDate = new Date();
	endDate = endDate.dateFromYMD(end_date.value);
	end_date2.value = endDate.getFullYear()+"-"+(endDate.getMonth()+1)+"-"+endDate.getDate();

	until_date = document.getElementById("until");
	until_date2 = document.getElementById("until2");
	untilDate = new Date();
	untilDate = untilDate.dateFromYMD(until_date.value);
	until_date2.value = untilDate.getFullYear()+"-"+(untilDate.getMonth()+1)+"-"+untilDate.getDate();

}
function checkUntil(){

	start_date = document.getElementById("publish_up");
	startDate = new Date();
	startDate = startDate.dateFromYMD(start_date.value);

	until_date = document.getElementById("until");
	untilDate = new Date();
	untilDate = untilDate.dateFromYMD(until_date.value);

	if (untilDate<startDate){
		until_date.value = start_date.value;
	}

}

function setEndDateWhenNotRepeating(elem){
	var id = elem[0].id;
	var norepeat = document.getElementById("NONE");
	start_date = document.getElementById("publish_up");
	end_date = document.getElementById("publish_down");

	startDate = new Date();
	startDate = startDate.dateFromYMD(start_date.value);

	defaultStartDate = new Date();
    defaultStartDate = startDate.dateFromYMD(start_date.defaultValue);

	endDate = new Date();
	endDate = endDate.dateFromYMD(end_date.value);

    defaultEndDate = new Date();
    defaultEndDate = defaultEndDate.dateFromYMD(end_date.defaultValue);

	/** If the end date is not visible then always set the end date to match the start date **/
	enddate_container = document.querySelector('.jevenddate');
	if (enddate_container.style.display == "none"){
		end_date.value = start_date.value;
	}

	/** New way of handling publish_up and publish_down calendar inputs **/

	if (id === 'publish_up' && startDate != defaultStartDate) {
        end_date.value = start_date.value;
        normaliseElem(end_date);
	} else if (startDate > endDate) {
		end_date.value = start_date.value;
		normaliseElem(end_date);
	}
}

function forceValidDate(elem){
	oldDate = new Date();

	// Joomla 4 bug - always adding 00:00:00 time to date field!
	if (elem.val().indexOf(" 00:00:00") > 0)
	{
		elem.val(elem.val().replace(" 00:00:00", ""));
	}

	oldDate = oldDate.dateFromYMD(elem.val());
	// if field is cleared then oldDate is empty
	if (oldDate == "")
	{
		oldDate = new Date();
		elem.val(oldDate.getYMD());
		alert(invalidcorrected);
		return;
	}
	newDate = oldDate.getYMD();
	if (newDate!=elem.val()) {
		elem.val(newDate);
		alert(invalidcorrected);
	}
}

function toggleView12Hour(){
	if (document.adminForm.view12Hour.checked) {
		document.getElementById('start_24h_area').style.display="none";
		document.getElementById('end_24h_area').style.display="none";
		document.getElementById('start_12h_area').style.display="inline-block";
		document.getElementById('end_12h_area').style.display="inline-block";
	} else {
		document.getElementById('start_24h_area').style.display="inline-block";
		document.getElementById('end_24h_area').style.display="inline-block";
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
	if (typeof document.adminForm.allDayEvent == 'undefined')
	{
		return;
	}
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

	var temp = new Date();
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

		document.querySelector('.jevstarttime').style.display = 'none';

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

			document.querySelector('.jevendtime').style.display = 'none';
			document.querySelector('.jevnoeendtime').style.display = 'none';

		}
	}
	else {
        var was24h = starttime.value=="00:00" && endtime.value=="23:59";
		// set 24h fields
		hide_start.disabled=false;
		hide_start12.disabled=false;
		if (was24h) {
            starttime.value="08:00";
        }
		starttime.disabled=false;

		sam.disabled=false;
		spm.disabled=false;

		document.querySelector('.jevstarttime').style.display = 'inline-block';

		if (!noendchecked){
			hide_end.disabled=false;
			hide_end12.disabled=false;
			if (was24h) {
                endtime.value="17:00";
            }
			endtime.disabled=false;
			var sd = temp.getYMD();
			temp = temp.dateFromYMD(enddate.value);
			var ed = temp.getYMD();
			if (ed<sd) {
				enddate.value = temp.getYMD();
			}

			eam.disabled=false;
			epm.disabled=false;

			document.querySelector('.jevendtime').style.display = 'inline-block';
			document.querySelector('.jevnoeendtime').style.display = 'inline-block';

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

	try {
		initialiseBootstrapButtons()
	}
	catch(e) {};

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

		document.querySelector('.jevendtime').style.display = 'none';

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

		document.querySelector('.jevendtime').style.display = 'inline-block';

	}

	if (document.adminForm.start_12h){
		// move to 12h fields
		set12hTime(endtime);
	}

	updateRepeatWarning();

	try {
		initialiseBootstrapButtons()
	}
	catch(e) {};

}

function toggleGreyBackground(inputtype,inputelem, tomatch) {
	if (inputtype==tomatch){
		inputelem.disabled = false;
		//inputelem.closest('fieldset').css("background-color","#ffffff");
                inputelem.closest('fieldset').removeClass("roundedgrey");
		inputelem.closest('fieldset').css("opacity","1");
		if (inputelem.closest('fieldset').find('legend')){
			//inputelem.closest('fieldset').find('legend').css("background-color","#ffffff");
			//jevjq("#"+inputtype).css("background-color","#ffffff");
			inputelem.closest('fieldset').find('legend').removeClass("roundedgrey");
			jevjq("#"+inputtype).removeClass("roundedgrey");
		}
	}
	else {
		inputelem.disabled = true;
		//inputelem.closest('fieldset').css("background-color","#dddddd");
                inputelem.closest('fieldset').addClass("roundedgrey");
		inputelem.closest('fieldset').css("opacity","0.7");
		if (inputelem.closest('fieldset').find('legend')){
			//inputelem.closest('fieldset').find('legend').css("background-color","#dddddd");
			//jevjq("#"+inputtype).css("background-color","#dddddd");
			inputelem.closest('fieldset').find('legend').addClass("roundedgrey");
			jevjq("#"+inputtype).addClass("roundedgrey");
		}
	}
}

function toggleCountUntil(cu){
	inputtypes = ["cu_count","cu_until"];
	for (var i=0;i<inputtypes.length;i++) {
		inputtype = inputtypes[i];
		elem = document.getElementById(inputtype);
		inputs = elem.getElementsByTagName('input');
		for (var e=0;e<inputs.length;e++){
			inputelem = jevjq(inputs[e]);
			if (inputelem.name!="countuntil"){
				toggleGreyBackground(inputtype,inputelem,cu);
			}
		}
	}
	updateRepeatWarning();

}

function toggleWhichBy(wb)
{
	inputtypes = ["byyearday","byweekno","bymonthday","bymonth","byday"];
	for (var i=0;i<inputtypes.length;i++) {
		inputtype = inputtypes[i];
		elem = document.getElementById(inputtype);
		inputs = elem.getElementsByTagName('input');
		for (var e=0;e<inputs.length;e++){
			inputelem = jevjq(inputs[e]);
			if (inputelem.name!="whichby"){
				toggleGreyBackground(inputtype, inputelem,wb);
			}
		}
	}
	updateRepeatWarning();
	try {
		initialiseBootstrapButtons()
	}
	catch(e) {};
}

function toggleFreq(freq , setup)
{
	var currentFreq = jevjq("input[name=freq]:checked").val().toUpperCase();

	var myDiv = document.getElementById('interval_div');
	var byyearday = document.getElementById('byyearday');
	var byweekno = document.getElementById('byweekno');
	var bymonthday = document.getElementById('bymonthday');
	var bymonth = document.getElementById('bymonth');
	var byday = document.getElementById('byday');
	var byirregular = document.getElementById('byirregular');
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
				byirregular.style.display="none";

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
				byirregular.style.display="none";

				if (!setup) fixRepeatDates(true);
			}
			break;
		case "MONTHLY":
			{
				intervalLabel.innerHTML=jevmonths;
				myDiv.style.display="block";
				byyearday.style.display="none";
				bymonth.style.display="none";
				byirregular.style.display="none";
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
				byirregular.style.display="none";
				byday.style.display="block";
				document.getElementById('jevbd').checked="checked";
				// needed for after switching to month repeat and then toi wekely
				document.getElementById("jevbd").closest('fieldset').style.backgroundColor = "#ffffff";
				document.getElementById("jevbd").parentNode.style.backgroundColor = "#ffffff";
				document.getElementById("byday").style.backgroundColor = "#ffffff";
				document.getElementById("jevbd").closest('fieldset').style.opacity = 1;

				//toggleWhichBy("byday");
				weekofmonth.style.display="none";
				// always set week nums false for weekly events
				toggleWeekNums(false);
                                fixRepeatDates(false);
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
				byirregular.style.display="none";
				document.getElementById('jevbd').checked="checked";
				//toggleWhichBy("byday");
				weekofmonth.style.display="none";
			}
			break;
		case "IRREGULAR":
			{
				myDiv.style.display="block";
				byyearday.style.display="none";
				bymonth.style.display="none";
				byweekno.style.display="none";
				bymonthday.style.display="none";
				byday.style.display="none";
				byirregular.style.display="block";
				document.getElementById('interval_div').style.display = "none";

				weekofmonth.style.display="none";
			}
			break;
	}
	if (freq!="NONE" || currentFreq!="NONE"){
		// can't use the function since it skips freq=NONE
		// ipdateRepeatWarning();
		if (document.adminForm.updaterepeats){
			document.adminForm.updaterepeats.value = 1;
		}

	}
}

function fixRepeatDates(checkYearDay){
	start_time = document.getElementById("start_time");
	starttimeparts = start_time.value.split(":");
	start_date = document.getElementById("publish_up");
	startDate = new Date();
	startDate = startDate.dateFromYMD(start_date.value);

	// special case where we first press yearly repeat - should check for 28 Feb
	if (checkYearDay && (document.adminForm.evid.value==0 || document.adminForm.updaterepeats.value==1)) {
		yearStart = new Date(startDate.getFullYear(),0,0,0,0,0,0);
		days = ((startDate-yearStart)/(24*60*60*1000));
		if (days>60){
			byddir = document.adminForm.byd_direction;
			byddir.checked = true;
		}
	}
	bmd = document.adminForm.bymonthday;
	if (bmd.value.indexOf(",")<=0) {
		//bmd.value = parseInt(startdateparts[2],10);
		bmd.value = startDate.getDate();
	}

	byd = document.adminForm.byyearday;
	byddir = document.adminForm.byd_direction;
	if (byd.value.indexOf(",")<=0) {
		yearStart = new Date(startDate.getFullYear(),0,0,0,0,0,0);
		// count back from jan 1
		yearEnd = new Date(Math.round(startDate.getFullYear())+1,0,1,0,0,0,0);
		//alert("year start = "+yearStart+" year end= "+yearEnd);
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
		monthStart = new Date(startDate.getFullYear(),startDate.getMonth()-1,0,0,0,0,0);
		monthEnd = new Date(startDate.getFullYear(),startDate.getMonth(),0,0,0,0,0);
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
		if (parseInt(document.getElementById('evid').value)==0) {
			bd[day].checked=false;
			// Make sure label is highlighted
			try {
				changeHiddenInput(bd[day]);
			}
			catch (e)
			{

			}
		}
	}
	document.getElementById('cb_wd' + startDate.getDay()).checked=true;
	// Make sure label is highlighted
	try {
		changeHiddenInput(document.getElementById('cb_wd' + startDate.getDay()));
	}
	catch (e)
	{

	}

	var wn = document.adminForm["weeknums[]"];

	for(var week = 0; week < wn.length; week++){
		if (parseInt(document.getElementById('evid').value) == 0) {

			var firstOfMonth   = new Date(startDate.getFullYear(), startDate.getMonth(), 1);
			var weeknumber = startDate.getWeekNumber() - firstOfMonth.getWeekNumber();

			if (week == weeknumber)
			{
				wn[week].checked = true;
			}
			else {
				wn[week].checked = false;
			}

			// Make sure label is highlighted
			try {
				changeHiddenInput(wn[week]);
			}
			catch (e)
			{

			}
		}
	}

	end_date = document.getElementById("publish_down");
	endDate = new Date();
	endDate = endDate.dateFromYMD(end_date.value);

	until_date = document.getElementById("until");
	untilDate = new Date();
	untilDate = untilDate.dateFromYMD(until_date.value);

	if (untilDate<startDate){
		until_date.value = start_date.value;
	}

	updateRepeatWarning();
}

function toggleWeekNums(newstate) {
	var wn = document.adminForm["weeknums[]"];
	if (parseInt(document.getElementById('evid').value) == 0) {
		start_date = document.getElementById('publish_up');
		startDate = new Date;
		startDate = startDate.dateFromYMD(start_date.value);

		for (var week = 0; week < wn.length; week++) {
			var firstOfMonth = new Date(startDate.getFullYear(), startDate.getMonth(), 1);
			var weeknumber = startDate.getWeekNumber() - firstOfMonth.getWeekNumber();

			if (week == weeknumber) {
				wn[week].checked = true;
			} else {
				wn[week].checked = false;
			}

			// Make sure label is highlighted
			try {
				changeHiddenInput(wn[week]);
			} catch (e) {

			}
		}
	} else {
		for (var w = 0; w < wn.length; w++) {
			wn[w].checked = newstate;
		}
	}
	updateRepeatWarning();

}

// sets the date for the page after save
function resetYMD(){
	start_date = document.getElementById("publish_up");
	startDate = new Date();
	startDate = startDate.dateFromYMD(start_date.value);

	document.adminForm.year.value = startDate.getFullYear();
	document.adminForm.month.value = startDate.getMonth()+1;
	document.adminForm.day.value = startDate.getDate();
}

// This variable blocks this check until an edited repeat/event has been fully loaded
var setupRepeatsRun = false;
var AllDayNoEndTimeSetup = false;
function updateRepeatWarning(){
	if (!setupRepeatsRun || !AllDayNoEndTimeSetup) {
		return;
	}
	if (jevjq("input[name=freq]:checked").length){
		var currentFreq = jevjq("input[name=freq]:checked").val().toUpperCase();
		if (document.adminForm.updaterepeats && currentFreq!="NONE")
		{
			document.adminForm.updaterepeats.value = 1;
		}
	}
}

function toggleWeeknumDirection () {
    if (jevjq('#weekofmonth input[name="bd_direction"]').attr('checked')){
        jevjq('.weeknameforward').style.display = 'none';
        jevjq('.weeknameback').style.display = 'inline';
    }
    else {
        jevjq('.weeknameforward').style.display = 'inline';
        jevjq('.weeknameback').style.display = 'inline';
    }
}

/* Check for booking conflicts */

jQuery.fn.formToJson =  function(){
		var json = {};
		jevjq(this).find('input, textarea, select').each(function(index,el){
			var name = el.name;
			var value = el.value;
			if (value === false || !name || el.disabled) return;
			// multi selects
			if (name.indexOf('[]')>=0 && (el.tagName.toLowerCase() =='select' ) && el.multiple==true){
				name = name.substr(0,name.length-2);
				if (!json[name]) json[name] = [];
				jevjq(el).find('option').each(function(eldx, opt){
					if (opt.selected ==true) json[name].push(opt.value);
				});
			}
			else if (name.indexOf('[]')>=0 && (el.type=='radio' || el.type=='checkbox') ){
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

function checkConflict(checkurl, pressbutton, jsontoken, client, repeatid,  redirect, language){
	var requestObject = {};
	requestObject.error = false;
	requestObject.client = client;
	requestObject.token = jsontoken;
	requestObject.pressbutton = pressbutton;
	requestObject.repeatid = repeatid;
	requestObject.lang = language;
	requestObject.formdata = jevjq(document.adminForm).formToJson();

	var doRedirect = (typeof redirect =='undefined') ?  1 : redirect;

	requestObject.redirect = doRedirect;
	var hasConflicts = false;

	// see http://stackoverflow.com/questions/26620/how-to-set-encoding-in-getjson-jquery
	//jevjq.ajaxSetup({ scriptCharset: "utf-8" , contentType: "application/json; charset=utf-8"});

	//var jSonRequest = jevjq.getJSON(checkurl, {'json':JSON.stringify(requestObject)})

	var jSonRequest = jevjq.ajax({
			type : 'POST',
			dataType : 'json',
			url : checkurl,
			data : {'json':JSON.stringify(requestObject)},
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			scriptCharset: "utf-8"
			})
		.done(function(json){
		if (!json){
			alert('could not check conflicts');
			jevjq('#jevoverlapwarning').css("display",'none');
			if (doRedirect) submit2(pressbutton);
			else hasConflicts = true;
		}
		else if (json.error){
			try {
				eval(json.error);
			}
			catch (e){
				alert('could not process error handler');
			}
		}
		else {
			//console.log(json);
			if (json.allclear){
				jevjq('#jevoverlapwarning').css("display",'none');
				jevjq('#jevoverlaprepeatwarning').css("display",'none');
				if (doRedirect) submit2(pressbutton);
				else hasConflicts = false;
			}
			else if (json.overlappingRepeats) {
				jevjq('#jevoverlapwarning').css("display",'none');
				jevjq('#jevoverlaprepeatwarning').css("display",'block');
				hasConflicts = true;
				// Make sure the message is visible
				//jQuery("#jevoverlapwarning").get(0).scrollIntoView();
				//jQuery('html, body').animate({	scrollTop: jQuery("#jevoverlapwarning").offset().top	}, 200);
				jQuery('html, body').animate({	scrollTop: jQuery("#jevents").offset().top-80	}, 200);
			}
			else {
				jevjq('#jevoverlapwarning').css("display",'block');
				jevjq('#jevoverlaprepeatwarning').css("display",'none');
				var container = jevjq('#jevoverlaps');
				container.html("");
				jevjq(json.overlaps).each (function(index, overlap){
					//var elem = jevjq("<a href='"+overlap.url+"' target='_blank>"+overlap.conflictMessage+"</a><br/>");
					//elem.appendText (overlap.summary+ " ( "+overlap.startrepeat+" - "+overlap.endrepeat+")");
					container.append("<a href='"+overlap.url+"' target='_blank'>"+overlap.conflictMessage+"</a><br/>")
				});
				hasConflicts = true;
				// Make sure the message is visible
				//jQuery("#jevoverlapwarning").get(0).scrollIntoView();
				//jQuery('html, body').animate({	scrollTop: jQuery("#jevoverlapwarning").offset().top	}, 200);
				jQuery('html, body').animate({	scrollTop: jQuery("#jevents").offset().top-80	}, 200);
			}
		}
	})
	.fail( function( jqxhr, textStatus, error){
		alert(textStatus + ", " + error);
		hasConflicts = true;
	});
}

var calendarDateClicked = true;

// fix for auto-rotating radio boxes in firefox !!!
// see http://www.ryancramer.com/journal/entries/radio_buttons_firefox/
document.addEventListener('DOMContentLoaded', function() {

	try {
		if(Browser.firefox) {
			jevjq("#adminForm").attr("autocomplete",'off');
		}
	}
	catch(e){
	}

	if (typeof JoomlaCalendar == 'undefined') {
		return;
	}

	// Fix JoomlaCalendar too
	if (typeof j3 != 'undefined' && j3) {
		JoomlaCalendar.prototype._handleDayMouseDownOLD = JoomlaCalendar.prototype._handleDayMouseDown;
		JoomlaCalendar.prototype._handleDayMouseDown = function (ev) {
			var el = ev.currentTarget;
			if (typeof el.navtype !== "undefined" && (el.navtype === -2 || el.navtype === -1 || el.navtype === 1 || el.navtype === 2)) {
				calendarDateClicked = false;
			}
			this._handleDayMouseDownOLD(ev);
			calendarDateClicked = true;
		};
		// Method to close/hide the calendar
		JoomlaCalendar.prototype.closeOLD = JoomlaCalendar.prototype.close;
		JoomlaCalendar.prototype.close = function () {
			calendarDateClicked = true;
			this.closeOLD();
		};
		JoomlaCalendar.prototype.showOLD = JoomlaCalendar.prototype.show;
		JoomlaCalendar.prototype.show = function () {
			calendarDateClicked = true;
			this.showOLD();
		};
	}
	if (jevjq('#view12Hour')){
		jevjq('#view12Hour').on('click', function(){toggleView12Hour();});
	}

	hideEmptyJevTabs();

	toggleAllDayEvent();
	toggleNoEndTime();
	AllDayNoEndTimeSetup = true;

	// get the count until box to trigger the switch if the date field is touched!
	jevjq('#cu_until').on('click', function(){enableRepeatUntil();});
	jevjq('#cu_until').on('mousedown', function(){enableRepeatUntil();});
	jevjq('#cu_count').on('click', function(){enableRepeatCount();});
	jevjq('#cu_count').on('mousedown', function(){enableRepeatCount();});

        // setup rounded grey response
        jevjq('#byyearday, #bymonth, #byweekno, #bymonthday, #byday, #byirregular, #bysetpos').on('click', function() {
            jevjq('#'+this.id).find('legend input[name="whichby"]').attr('checked', true);
            toggleWhichBy(this.id);
        })

	document.addEventListener('gslshowon', function(e) {
		hideEmptyJevTabs();
	})

});

function enableRepeatUntil() {
	jevjq("#cuu").prop("checked", 1);
	toggleCountUntil('cu_until');
}

function enableRepeatCount() {
	jevjq("#cuc").prop("checked", 1);
	toggleCountUntil('cu_count');
}

// Hide empty tabs and their links
function hideEmptyJevTabs() {
		// Old version
		// empty tabs - hide the tab link
		var tabs = jQuery("#myEditTabsContent .tab-pane");
		if (tabs.length){
			tabs.each(function(index) {
				tab = jQuery(this);
				if (tab.children().length==0){
					tab.css("display","none");
					var tablink = jQuery("#myEditTabs a[href='#"+tab.prop('id')+"']");
					if (tablink){
						tablink.parent().css("display","none");
					}
				}
			})
		}
		// tab link with no matching tab - hide the link
		var tablinks = jevjq("#myEditTabs.nav-tabs li a");

		if (tablinks.length){
			tablinks.each(function(index, tablink) {
				// use attr instead of prop here because prop messes up special characters!
				var href = jQuery(tablink).attr('href');
				href = href.substr(href.indexOf('#'));
				var tab = document.querySelector("#myEditTabsContent "+href);
				if (!tab) {
					tablink.innerHTML="xx";
					jQuery(tablink).css("display","none");
				}
			})
		}
		// new version
		var uitabs = document.querySelectorAll("#adminForm .gsl-switcher > li, #adminForm .uk-switcher > li");
		var uitablabels = document.querySelectorAll("#adminForm #myEditTabs > li");

		if (uitabs.length)
		{
			uitabs.forEach(function(tab, index)
			{
				if(tab.innerHTML.trim().length == 0)
				{
					uitablabels[index].style.display = 'none';
				}
				else
				{
					tab.classList.add('cleverGetHeightCSS');
					//console.log(index + ' ' + tab.scrollHeight);
					if (tab.scrollHeight == 0)
					{
						//console.log('hide tab ' + index);
						uitablabels[index].classList.add('hiddenTab');
					}
					else
					{
						uitablabels[index].classList.remove('hiddenTab');
					}
					tab.classList.remove('cleverGetHeightCSS');
				}
			});
		}

	}

function selectIrregularDate() {
	// only respond to calendar date selections
	if (!calendarDateClicked)
	{
		return;
	}

	var calpopup = document.querySelector(".irregularDateSelector .js-calendar");

	// Trap month to month movement!
	if (calpopup.style.display !== "none" && !calpopup.hidden)
	{
		return;
	}

	var repeatDate = new Date();
	repeatDate  = repeatDate.dateFromYMD(jQuery("#irregular").val());
	var m = repeatDate.getMonth()+1;
	var d = repeatDate.getDate();
	repeatDate = repeatDate.getFullYear()+"-" + (m < 10 ? '0' : '') + m + "-" + (d < 10 ? '0' : '') + d;

	var selectElem = jQuery("#irregularDates");

	var option = jQuery("#irregularDates option[value='" + repeatDate + "']");
	if (option.length)
	{
		option[0].selected = !option[0].selected;
		try {
			// form replacement
			gslselect("#irregularDates");
		}
		catch (e) { }
		/*
		option[0].selected = !option[0].selected;
		var event = new Event('gslchange');
		selectElem[0].dispatchEvent(event);
		*/
		return;
	}
	option = jQuery("<option>", {
		"value" : repeatDate,
		"text" : jQuery("#irregular").val(),
		"selected" : true
	});
	selectElem.append(option);
	try {
		// form replacement
		gslselect("#irregularDates");
	}
	catch (e) { }
	//selectElem.chosen();
	selectElem.trigger("chosen:updated");
	selectElem.trigger("liszt:updated");
}

// Set up multi-catid sorting
window.addEventListener('load', function() {
	var catids = document.querySelector('.jevcategory select[name="catid[]"]') || document.querySelector('.jevcategory select[name="catid"]');
	if(catids){
		var sortable = document.querySelector('.jevcategory #catid_chzn .chzn-choices');
		if (sortable)
		{
			sortable.setAttribute('data-sortable',
				Sortable.create(sortable, {
					onEnd: reorderCategorySelections,
				})
			);
		}
		catids.addEventListener('change', reorderCategorySelections);
	}
});

function reorderCategorySelections()
{
    // Make sure we fetch these fresh each time!
	var catids = document.querySelector('.jevcategory select[name="catid[]"]') || document.querySelector('.jevcategory select[name="catid"]');
    var chosenCatids = document.querySelector('.jevcategory #catid_chzn .chzn-choices');

    if (!chosenCatids)
    	return;

    // find all the selected categories
    var ccats = chosenCatids.querySelectorAll('a');

    var selectedCats = [];
    for (var c = 0; c < ccats.length; c++)
    {
        var cat = ccats[c];
        var catindex = cat.dataset.optionArrayIndex;
        var options = catids.querySelectorAll('option');
        selectedCats.push(options[catindex]);
    }

    for (var sc = 0; sc < selectedCats.length; sc ++)
    {
    	var target = catids.querySelector('option:nth-child(' + (sc + 1) + ')');
		var newNode = selectedCats[sc];
		catids.insertBefore(newNode, target);
    }

    jQuery(catids).trigger("chosen:updated");
    // old style version - still needed!
	jQuery(catids).trigger("liszt:updated");
}