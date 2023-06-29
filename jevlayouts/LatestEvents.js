var jevpreview = new Array();

jevpreview.push({
	'id': 1,
	'name': 'Date, Time, Title',
	'image': 'http://www.jevents.net/jevlayouts/latest1.png',
	'code': '<span class="icon-calendar"></span>${startDate(%d %b %Y)}[!a:<br/><span class="icon-clock"></span>${startDate(%I:%M%p)} - ${endDate(%I:%M%p)}]\n<span class="icon-hand-right  fa-hand-point-right"></span><strong>${title}</strong>',
	'info': 'Shows event date, time and title with scalable Glyphicons next to them.  This layout requires no club addons.',
	'css': '',
	'ignorebr': false,
	'inccss': true,
	'templatetop' : '',
	'templaterow' : '',
	'templatebottom' : '',
});
jevpreview.push({
	'id': 2,
	'name': 'Date, Time, Title, Location',
	'image': 'http://www.jevents.net/jevlayouts/latest2.png',
	'code': '<span class="icon-calendar"></span>${startDate(%d %b %Y)}[!a:<br/><span class="icon-clock"></span>${startDate(%I:%M%p)} - ${endDate(%I:%M%p)}]\n<span class="icon-hand-right  fa-hand-point-right"></span><strong>${title}</strong>${JEVLOCATION_TITLE#\n<span class="icon-globe"></span>%s}',
	'info': 'Shows event date, time, title and location with scalable Glyphicons next to them.  <strong>This layout requires the Silver Member Managed Locations Addon</strong>',
	'css': '',
	'ignorebr': false,
	'inccss': true,
	'templatetop' : '',
	'templaterow' : '',
	'templatebottom' : '',
});
jevpreview.push({
	'id': 3,
	'name': 'Image, Date, Time, Title, Location',
	'image': 'http://www.jevents.net/jevlayouts/latest3.png',
	'code': '<a href="${eventDetailLink}" target="_self" style="float:left;margin:0px 10px 10px 0px;${JEV_LIST_THUMBNAIL_1##display:none;}">${JEV_LIST_THUMBNAIL_1}</a><span class="icon-calendar"></span>${startDate(%d %b %Y)}[!a:<br/><span class="icon-clock"></span>${startDate(%I:%M%p)} - ${endDate(%I:%M%p)}]\n<span class="icon-hand-right  fa-hand-point-right  fa-hand-point-right"></span><strong>${title}</strong>${JEVLOCATION_TITLE#\n<span class="icon-globe"></span>%s}',
	'info': 'Shows event image. date, time, title and location with scalable Glyphicons next to them.  <strong>This layout requires the Silver Member Standare Images and Managed Locations Addons to work.</strong>',
	'css': '',
	'ignorebr': false,
	'inccss': true,
	'templatetop' : '',
	'templaterow' : '',
	'templatebottom' : '',
});
/*
jevpreview.push({'id':4,
	'name':'Event Attendance Buttons and Countdown',
	'image':'http://www.jevents.net/jevlayouts/latest4.png',
	'code': '<div style="float:right;"><a href="${eventDetailLink}#registrations" alt="Event Registration" class="${REGOPEN#regopen#regclosed}"><div class="regbutton">${REGOPEN}${REGCLOSED}</div><div class="attendeecount" style="${ATTENDCOUNT##display:none;}">Attendees : ${ATTENDCOUNT}</div></a></div> <a href="${eventDetailLink}" style="padding-left:3px;border-left: 8px solid ${bgcolor}">${category} </a><br/><a href="${eventDetailLink}">${title} </a>\n<span style="font-size:10px; font-family:Tahoma;">[!a: ${eventDate(%l:%M %p)}  ${endDate(- %l:%M %p)}][a: ${startDate(%a %e %b)} - All Day] - ${countdown( %d Days to go)} </span>',
	'info':'Shows event details with countdown and efent registration buttons.  <strong>This layout requires the Gold Member RSVP Pro addon and is ideally coupled with the Iconic layout available to Silver and Gold members.</strong>',
	'css': '.jevbootstrap .regopen .regbutton {background-color: #55aa55; border: 1px solid #55aa55;border-radius: 3px;color: #fff;display: block;padding: 3px}\n.jevbootstrap .regclosed {display:none;}',
	'ignorebr':false,
	'inccss': true,
	'templatetop' : '',
	'templaterow' : '',
	'templatebottom' : '',
} );
*/
jevpreview.push({
	'id': 5,
	'name': 'UIKit Float Popup',
	'image': 'http://www.jevents.net/jevlayouts/latest5.png',
	'code': '<div id=\'jeviso_item${RPID}\' class=\'jeviso_item w4 style3\'>\n' +
		'    <div class=\'jfloat-event ng-scope ng-isolate-scope\' itemscope=\'\' itemtype=\'http://schema.org/Event\'>\n' +
		'        <div class=\'jeviso_item_image uk-height-small\' uk-toggle=\'target: #modal-event${RPID}\'>\n' +
		'            ${JEV_SIZEDIMAGE_1;400x300}\n' +
		'        </div>\n' +
		'        <div uk-grid uk-height-match id=\'eventcontainer${RPID}\' class=\'jeviso_eventsummary\' uk-toggle=\'target: #modal-event${RPID}\'>\n' +
		'            <div>\n' +
		'                <div class=\'startdate uk-height-1-1 uk-padding-small\' itemprop=\'startDate\' datetime=\'${STARTDATE;%Y-%b-%d}\' style=\'background-color:${COLOUR};color:${FGCOLOUR}\'>\n' +
		'                    ${STARTDATE;<div uk-icon=\'icon: calendar\'></div><div class=\'startmonth\'>%b</div><div class=\'startday\'>%d</div>}\n' +
		'                </div>\n' +
		'            </div>\n' +
		'            <h3 class=\'uk-modal-title uk-width-expand eventtitle uk-padding-small uk-text-nowrap uk-overflow-hidden\' itemprop=\'name\' title=\'${TITLE}\'>\n' +
		'                ${TRUNCATED_TITLE:24chars}\n' +
		'                <span class=\'eventcategory\'>${CATEGORY}</span>\n' +
		'            </h3>\n' +
		'        </div>\n' +
		'        <!-- This is the modal -->\n' +
		'        <div id=\'modal-event${RPID}\' uk-modal=\'container:jev_isoitem${RPID}\' class=\'jeviso-modal\'>\n' +
		'            <div class=\'uk-modal-dialog\'>\n' +
		'                <div class=\'uk-modal-header uk-padding-small\' uk-grid>\n' +
		'                    <h2 class=\'startdate\'>${STARTDATE;<div class=\'startmonth\'>%b</div><div class=\'startday\'>%d</div>}</h2>\n' +
		'                    <h2 class=\'uk-modal-title uk-width-expand eventtitle\'>${TITLE_LINK}<span class=\'eventcategory\'>${CATEGORY}</span></h2>\n' +
		'                    <button class=\'uk-modal-close-default\' type=\'button\' uk-close></button>\n' +
		'                </div>\n' +
		'                <div class=\'uk-modal-body uk-padding-small\'>\n' +
		'                    ${JEV_SIZEDIMAGE_1;800x600#<div class=\'jeviso_modal_image uk-width-1-1\'>%s</div>#}\n' +
		'                    <div class=\'eventtime uk-margin-medium-top \' uk-grid>\n' +
		'                        <div uk-icon=\'icon: clock\'></div>\n' +
		'                        <div class=\'uk-width-expand\'>\n' +
		'                            <div class=\'eventdetaillink uk-float-right uk-button uk-button-primary\'>\n' +
		'                                ${LINKSTART} View in Calendar ${LINKEND}\n' +
		'                            </div>\n' +
		'                            <div class=\'timelabel\'>\n' +
		'                                Time\n' +
		'                            </div>\n' +
		'                            ${STARTTIME;%l:%M %p# %s#No Specific time}${ENDTIME;%l:%M %p# - %s}\n' +
		'                        </div>\n' +
		'                    </div>\n' +
		'                    <div class=\'eventdetails  uk-margin-small-top \' uk-grid>\n' +
		'                        <div uk-icon=\'icon: info\'></div>\n' +
		'                        <div class=\'uk-width-expand\'>\n' +
		'                            <div class=\'detailslabel\'>\n' +
		'                                Event Details\n' +
		'                            </div>\n' +
		'                            ${TRUNCATED_DESC:30words}\n' +
		'                        </div>\n' +
		'                    </div>\n' +
		'                    <div class=\'calendarlinks  uk-margin-small-top\' uk-grid>\n' +
		'                        <div uk-icon=\'icon: calendar\'></div>\n' +
		'                        <div class=\'uk-width-expand\'>\n' +
		'                            <div class=\'exportlabel\'>Export Event to Your Calendar</div>\n' +
		'                            <div>\n' +
		'                                <a href=\'${ICALGOOGLE}\' title=\'Save to Google Calendar\' rel=\'nofollow\'>\n' +
		'                                    <span uk-icon=\'icon: google\'></span>\n' +
		'                                </a>\n' +
		'                                <a href=\'${ICALSAVE}\' title=\'Save to Calendar\'  rel=\'nofollow\'>\n' +
		'                                    <span uk-icon=\'icon: calendar\'></span>\n' +
		'                                </a>\n' +
		'                            </div>\n' +
		'                        </div>\n' +
		'                    </div>\n' +
		'                    <div class=\'location  uk-margin-small-top\' uk-grid>\n' +
		'                        <div uk-icon=\'icon: location\'></div>\n' +
		'                        <div class=\'uk-width-expand\'>\n' +
		'                            <div class=\'locationlabel\'>Location</div>\n' +
		'                            <div class=\'jfloat-venue\' itemprop=\'location\' itemscope=\'\' itemtype=\'http://schema.org/EventVenue\'>\n' +
		'                                <link itemprop=\'url\' href=\'${JEVLOCATION_URL}\'>\n' +
		'                                <span itemprop=\'geo\' itemscope=\'\' itemtype=\'http://schema.org/GeoCoordinates\'>\n' +
		'                                    <meta itemprop=\'latitude\' content=\'${JEVLOCATION_LAT}\'>\n' +
		'                                    <meta itemprop=\'longitude\' content=\'${JEVLOCATION_LON}\'>\n' +
		'                                </span>\n' +
		'                                <div class=\'jfloat-venue-name\' itemprop=\'name\' title=\'${JEVLOCATION_TITLE}\'>\n' +
		'                                    ${JEVLOCATION_TITLE}\n' +
		'                                </div>\n' +
		'                                <div class=\'jfloat-city-state\' itemprop=\'address\' itemscope=\'\' itemtype=\'http://schema.org/PostalAddress\'>\n' +
		'                                    <span itemprop=\'addressLocality\'>${JEVLOCATION_CITY}</span>\n' +
		'                                    <span itemprop=\'addressRegion\'>${JEVLOCATION_STATE}</span>\n' +
		'                                </div>\n' +
		'                            </div>\n' +
		'                            ${JEVLOCATION_MAP}\n' +
		'                        </div>\n' +
		'                    </div>\n' +
		'                </div>\n' +
		'            </div>\n' +
		'        </div>\n' +
		'    </div>\n' +
		'</div>',
	'css': '.jeviso_container:after {\n' +
		'	content: "";\n' +
		'	display: block;\n' +
		'	clear: both;\n' +
		'}\n' +

		'#jeviso_module .jeviso_item {\n' +
		'	border: 1px solid;\n' +
		'	max-width: 100%;\n' +
		'	box-sizing: border-box;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_image img {\n' +
		'	max-width: 100%;\n' +
		'	border-radius: 3px;\n' +
		'}\n' +
		'	#jeviso_module .jeviso_item .jeviso_item_title {\n' +
		'	padding: 5px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_date {\n' +
		'	padding: 5px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_body {\n' +
		'	word-wrap: break-word;\n' +
		'	padding: 5px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_footer {\n' +
		'	width: 100%;\n' +
		'	max-width: 100%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_footer .jeviso_item_catcolor {\n' +
		'	border-left-width: 10px;\n' +
		'	border-left-style: solid;\n' +
		'	display: inline-block;\n' +
		'	padding-left: 2px;\n' +
		'	text-align: left;\n' +
		'	width: 35%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_footer .jeviso_item_rmlink {\n' +
		'   display: inline-block;\n' +
		'	text-align: right;\n' +
		'	width: 55%;\n' +
		'}\n' +
		'/* Float module specific */\n' +
		'#jeviso_module .jeviso_container .jeviso_item {\n' +
		'	border: 1px solid #dddddd;\n' +
		'	border-radius: 3px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item:hover {\n' +
		'	box-shadow: 0 0 8px 2px rgba(0, 0, 0, 0.4);\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item .jeviso_item_image img {\n' +
		'	width: 100%;\n' +
		'	max-width: 100%;\n' +
		'	border-top-right-radius: 3px;\n' +
		'	border-top-left-radius: 3px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.style3 {\n' +
		'	cursor: pointer;\n' +
		'	border: 1px solid #f0f0f1;\n' +
		'	border-radius: 3px;\n' +
		'	display: inline-block;\n' +
		'	margin-right: 1%;\n' +
		'	margin-bottom: 10px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.style3 .jeviso_item_image {\n' +
		'	overflow: hidden;\n' +
		'	position: relative;\n' +
		'	border-bottom-right-radius: 0;\n' +
		'	border-bottom-left-radius: 0;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.style3 .jeviso_item_image img {\n' +
		'	position: absolute;\n' +
		'	top: -50%;\n' +
		'	bottom: -50%;\n' +
		'	width: 100%;\n' +
		'	margin: auto;\n' +
		'	height: auto;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.style3.listv {\n' +
		'	width: 100%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.style3.listv .noleftpadding {\n' +
		'	padding-left: 0;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.style3 h3.eventtitle {\n' +
		'	font-size: 1.3rem !important;\n' +
		'	font-weight: bold;\n' +
		'	text-overflow: ellipsis;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.style3 h3.eventtitle .eventcategory {\n' +
		'	display: block;\n' +
		'	font-size: 1.0rem !important;\n' +
		'	margin-top: 10px;\n' +
		'	font-weight: normal;\n' +
		'	text-overflow: ellipsis;\n' +
		'    overflow-x: hidden;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.style3 div.startdate {\n' +
		'	text-align: center;\n' +
		'}\n' +
		'.jeviso-modal {\n' +
		'	cursor: initial;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog {\n' +
		'	width: 800px;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .uk-modal-header {\n' +
		'	margin: 0;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .uk-button-primary a {\n' +
		'	color: #fff;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .eventtime,\n' +
		'.jeviso-modal .uk-modal-dialog .eventdetails,\n' +
		'.jeviso-modal .uk-modal-dialog .calendarlinks {\n' +
		'	padding-bottom: 5px;\n' +
		'	margin-bottom: 5px;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .startdate {\n' +
		'	font-size: 1.5rem !important;\n' +
		'	padding-left: 10px;\n' +
		'	text-align: center;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .startdate .startmonth {\n' +
		'   height: 40px;\n' +
		'	line-height: 40px;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .startdate .startday {\n' +
		'	height: 25px;\n' +
		'	line-height: 25px;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .uk-modal-title {\n' +
		'	padding-left: 40px;\n' +
		'	text-overflow: ellipsis;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .uk-modal-title a {\n' +
		'	font-size: 1.5rem !important;\n' +
		'	height: 40px;\n' +
		'	line-height: 40px;\n' +
		'}\n' +
		'.jeviso-modal .uk-modal-dialog .uk-modal-title .eventcategory {\n' +
		'	display: block;\n' +
		'	height: 25px;\n' +
		'	line-height: 25px;\n' +
		'	font-size: 1.3rem !important;\n' +
		'	opacity: 0.6;\n' +
		'	text-overflow: ellipsis;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_gutter {\n' +
		'	width: 0% !important;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w1 {\n' +
		'	width: 10%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w2 {\n' +
		'	width: 49%;\n' +
		'	margin-right: 2%;\n' +
		'	margin-bottom: 2%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w2:nth-child(2n) {\n' +
		'	margin-right: 0%;\n' +
		'}\n' +
		'@media (max-width: 479px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w2 {\n' +
		'		width: 100%;\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w3 {\n' +
		'	width: 32%;\n' +
		'	margin-right: 2%;\n' +
		'	margin-bottom: 2%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w3:nth-child(3n) {\n' +
		'	margin-right: 0%;\n' +
		'}\n' +
		'@media (min-width: 480px) and (max-width: 768px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w3 {\n' +
		'		width: 49%;\n' +
		'		margin-right: 2%;\n' +
		'		margin-bottom: 2%;\n' +
		'	}\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w3:nth-child(2n) {\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
		'@media (max-width: 479px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w3 {\n' +
		'		width: 100%;\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w4 {\n' +
		'	width: 23.5%;\n' +
		'	margin-right: 2%;\n' +
		'	margin-bottom: 2%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w4:nth-child(4n) {\n' +
		'	margin-right: 0%;\n' +
		'}\n' +
		'@media (min-width: 480px) and (max-width: 768px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w4 {\n' +
		'		width: 49%;\n' +
		'		margin-right: 2%;\n' +
		'		margin-bottom: 2%;\n' +
		'	}\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w4:nth-child(2n) {\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
		'@media (max-width: 479px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w4 {\n' +
		'		width: 100%;\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w5 {\n' +
		'	width: 18.4%;\n' +
		'	margin-right: 2%;\n' +
		'	margin-bottom: 2%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_container .jeviso_item.w5:nth-child(5n) {\n' +
		'	margin-right: 0%;\n' +
		'}\n' +
		'@media (min-width: 920px) and (max-width: 1120px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w5 {\n' +
		'		width: 23.5%;\n' +
		'		margin-right: 2%;\n' +
		'		margin-bottom: 2%;\n' +
		'	}\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w5:nth-child(4n) {\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
		'@media (min-width: 691px) and (max-width: 919px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w5 {\n' +
		'		width: 32%;\n' +
		'		margin-right: 2%;\n' +
		'		margin-bottom: 2%;\n' +
		'}\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w5:nth-child(3n) {\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
		'@media (min-width: 480px) and (max-width: 690px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w5 {\n' +
		'		width: 49%;\n' +
		'		margin-right: 2%;\n' +
		'		margin-bottom: 2%;\n' +
		'}\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w5:nth-child(2n) {\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
		'@media (max-width: 479px) {\n' +
		'	#jeviso_module .jeviso_container .jeviso_item.w5 {\n' +
		'		width: 100%;\n' +
		'		margin-right: 0%;\n' +
		'	}\n' +
		'}\n' +
	'',
	'info': 'UIKit 3 based theme that combines with Float Theme to show event image. date, time, title and location with popup details in modal.  <strong>This layout requires the Silver Member Float Theme, Standard Images and Managed Locations Addons to work together with a UIKit 3 based template.</strong>',
	'ignorebr': true,
	'inccss': false,
	'templatetop' : '    <div id="jeviso_module" class="jeviso_day">\n' +
		'        <div class="jeviso_container" itemscope itemtype="http://schema.org/Event">\n',
	'templaterow' : '%s',
	'templatebottom' : '</div></div>',
});

function loadJevPreview(target, csstarget, ignorebrtarget, ttop, trow, tbot, inccss) {
	jQuery(document).ready(function ($) {
		// Setup click action on default option (which is translated)
		var defaultLI = jQuery("#dropdownUL_" + target + " li");
		var currentCode = jQuery("#" + target).val();
		var currentCSS = jQuery("#" + csstarget).length ? jQuery("#" + csstarget).val() : "";
		var currentIgnore = jQuery("#" + ignorebrtarget).val();
		var currentTTop = jQuery("#" + ttop).val();
		var currentTRow = jQuery("#" + trow).val();
		var currentTBot = jQuery("#" + tbot).val();
		var currentinccss = jQuery("#" + inccss).val();
		defaultLI.on('click', function (event) {
			event.preventDefault();
			jQuery("#" + target).val(currentCode);
		});
		jQuery(jevpreview).each(function (index, item) {
			var info = "";
			if (item.image) {
				info = "<img src='" + item.image + "' style='margin:0px 10px 10px 10px;'/><br/>";
			}
			info += "<div>" + item.info + "</div>";
			var elem = jQuery('<li role="presentation" class="mx-3 my-1"><a  role="menuitem" tabindex="-1" href="#" class="dropdownpopover" data-title="' + item.name + '" data-bs-title="' + item.name + '" data-content="' + info + '"  data-bs-content="' + info + '">' + item.name + '</a></li>');
			var dropdowntarget = jQuery("#dropdownUL_" + target);
			elem.appendTo(dropdowntarget);
			elem.on('click', function (event) {
				event.preventDefault();
				jQuery("#" + target).val(item.code);
				try {
					if (item.ignorebr) {
						document.getElementById(ignorebrtarget).value = 1;
					}
					else {
						document.getElementById(ignorebrtarget).value = 0;
					}
					jQuery('#' + ignorebrtarget).trigger("chosen:updated");
					jQuery('#' + ignorebrtarget).trigger("liszt:updated");

					if (item.ignorecss)
					{
						document.getElementById(inccss).value = 1;
					}
					else {
						document.getElementById(inccss).value = 0;
					}
					jQuery('#' + inccss).trigger("chosen:updated");
					jQuery('#' + inccss).trigger("liszt:updated");
				}
				catch (e) {

				}
				if (jQuery("#" + csstarget).length) {
					jQuery("#" + csstarget).val(item.css);
				}
				jQuery("#" + ttop).val(item.templatetop);
				jQuery("#" + trow).val(item.templaterow);
				jQuery("#" + tbot).val(item.templatebottom);
			});
		});
		$('a.dropdownpopover').popover({container: 'body', trigger: 'hover', placement: 'right', html: true});
		jQuery("#" + target).on('change', function (event) {
			currentCode = jQuery("#" + target).val();
			currentCSS = jQuery("#" + csstarget).length ? jQuery("#" + csstarget).val() : "";
			currentIgnore = jQuery("#" + ignorebrtarget).val();
			currentTTop = jQuery("#" + ttop).val();
			currentTRow = jQuery("#" + trow).val();
			currentTBot = jQuery("#" + tbot).val();
			currentinccss = jQuery("#" + inccss).val();
			// must reset default option selection event because of closures!
			defaultLI.off('click');
			defaultLI.on('click', function (event) {
				event.preventDefault();
				jQuery("#" + target).val(currentCode);
				if (jQuery("#" + csstarget).length) {
					jQuery("#" + csstarget).val(currentCSS);
				}
				jQuery("#" + ignorebrtarget).val(currentIgnore);
				jQuery('#' + ignorebrtarget).trigger("chosen:updated");
				jQuery('#' + ignorebrtarget).trigger("liszt:updated");

				jQuery("#" + inccss).val(currentinccss);
				jQuery('#' + inccss).trigger("chosen:updated");
				jQuery('#' + inccss).trigger("liszt:updated");

				jQuery("#" + ttop).val(currentTTop);
				jQuery("#" + trow).val(currentTRow);
				jQuery("#" + tbot).val(currentTBot);

			});
		});
		jQuery("#" + csstarget).on('change', function (event) {
			currentCode = jQuery("#" + target).val();
			currentCSS = jQuery("#" + csstarget).length ? jQuery("#" + csstarget).val() : "";
			currentIgnore = jQuery("#" + ignorebrtarget).val();
			currentTTop = jQuery("#" + ttop).val();
			currentTRow = jQuery("#" + trow).val();
			currentTBot = jQuery("#" + tbot).val();
			currentinccss = jQuery("#" + inccss).val();
			// must reset default option selection event because of closures!
			defaultLI.off('click');
			defaultLI.on('click', function (event) {
				event.preventDefault();
				jQuery("#" + target).val(currentCode);
				if (jQuery("#" + csstarget).length) {
					jQuery("#" + csstarget).val(currentCSS);
				}
				jQuery("#" + ignorebrtarget).val(currentIgnore);
				jQuery('#' + ignorebrtarget).trigger("chosen:updated");
				jQuery('#' + ignorebrtarget).trigger("liszt:updated");

				jQuery("#" + ttop).val(currentTTop);
				jQuery("#" + trow).val(currentTRow);
				jQuery("#" + tbot).val(currentTBot);

				jQuery("#" + inccss).val(currentinccss);
				jQuery('#' + inccss).trigger("chosen:updated");
				jQuery('#' + inccss).trigger("liszt:updated");

			});
		});
	});
}
