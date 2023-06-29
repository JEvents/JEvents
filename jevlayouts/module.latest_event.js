var jevpreview = new Array();

jevpreview.push({
	'id': 1,
	'name': 'Date, Time, Title',
	'image': 'http://www.jevents.net/jevlayouts/latest1.png',
	'code': '<span class="icon-calendar"></span>${startDate(%d %b %Y)}[!a:<br/><span class="icon-time"></span>${startDate(%I:%M%p)} - ${endDate(%I:%M%p)}]\n<span class="icon-hand-right"></span><strong>${title}</strong>',
	'info': 'Shows event date, time and title with scalable Glyphicons next to them.  This layout requires no club addons.',
	'css': '',
	'js': '',
	'templatetop' : '',
	'templatebottom' : '',
});
jevpreview.push({
	'id': 2,
	'name': 'Date, Time, Title, Location',
	'image': 'http://www.jevents.net/jevlayouts/latest2.png',
	'code': '<span class="icon-calendar"></span>${startDate(%d %b %Y)}[!a:<br/><span class="icon-time"></span>${startDate(%I:%M%p)} - ${endDate(%I:%M%p)}]\n<span class="icon-hand-right"></span><strong>${title}</strong>${JEVLOCATION_TITLE#\n<span class="icon-globe"></span>%s}',
	'info': 'Shows event date, time, title and location with scalable Glyphicons next to them.  <strong>This layout requires the Silver Member Managed Locations Addon</strong>',
	'css': '',
	'js': '',
	'templatetop' : '',
	'templatebottom' : '',
});
jevpreview.push({
	'id': 3,
	'name': 'Image, Date, Time, Title, Location',
	'image': 'http://www.jevents.net/jevlayouts/latest3.png',
	'code': '<a href="${eventDetailLink}" target="_self" style="float:left;margin:0px 10px 10px 0px;${JEV_LIST_THUMBNAIL_1##display:none;}">${JEV_LIST_THUMBNAIL_1}</a><span class="icon-calendar"></span>${startDate(%d %b %Y)}[!a:<br/><span class="icon-time"></span>${startDate(%I:%M%p)} - ${endDate(%I:%M%p)}]\n<span class="icon-hand-right"></span><strong>${title}</strong>${JEVLOCATION_TITLE#\n<span class="icon-globe"></span>%s}',
	'info': 'Shows event image. date, time, title and location with scalable Glyphicons next to them.  <strong>This layout requires the Silver Member Standare Images and Managed Locations Addons to work.</strong>',
	'css': '',
	'js': '',
	'templatetop' : '',
	'templatebottom' : '',
});
/*
jevpreview.push({'id':4,
	'name':'Event Attendance Buttons and Countdown',
	'image':'http://www.jevents.net/jevlayouts/latest4.png',
	'code': '<div style="float:right;"><a href="${eventDetailLink}#registrations" alt="Event Registration" class="${REGOPEN#regopen#regclosed}"><div class="regbutton">${REGOPEN}${REGCLOSED}</div><div class="attendeecount" style="${ATTENDCOUNT##display:none;}">Attendees : ${ATTENDCOUNT}</div></a></div> <a href="${eventDetailLink}" style="padding-left:3px;border-left: 8px solid ${bgcolor}">${category} </a><br/><a href="${eventDetailLink}">${title} </a>\n<span style="font-size:10px; font-family:Tahoma;">[!a: ${eventDate(%l:%M %p)}  ${endDate(- %l:%M %p)}][a: ${startDate(%a %e %b)} - All Day] - ${countdown( %d Days to go)} </span>',
	'info':'Shows event details with countdown and efent registration buttons.  <strong>This layout requires the Gold Member RSVP Pro addon and is ideally coupled with the Iconic layout available to Silver and Gold members.</strong>',
	'css': '.jevbootstrap .regopen .regbutton {background-color: #55aa55; border: 1px solid #55aa55;border-radius: 3px;color: #fff;display: block;padding: 3px}\n.jevbootstrap .regclosed {display:none;}',
	'js': '',
	'templatetop' : '',
	'templatebottom' : '',
} );
*/
jevpreview.push({
	'id': 5,
	'name': 'UIKit Float Popup',
	'image': 'http://www.jevents.net/jevlayouts/latest5.png',
	'code': '<div id=\'jeviso_item{{Repeat id:RPID}}\' class=\'jeviso_item \'>\n' +
		'    <div class=\'jfloat-event ng-scope ng-isolate-scope\' itemscope=\'\' itemtype=\'http://schema.org/Event\'>\n' +
		'        <div class=\'jeviso_item_image uk-height-small\' uk-toggle=\'target: #modal-event{{Repeat id:RPID}}\'>\n' +
		'            {{Scaled Image:JEV_SIZEDIMAGE_1;400x300}}\n' +
		'        </div>\n' +
		'        <div uk-grid uk-height-match id=\'eventcontainer{{Repeat id:RPID}}\' class=\'jeviso_eventsummary\' uk-toggle=\'target: #modal-event{{Repeat id:RPID}}\'>\n' +
		'            <div>\n' +
		'                <div class=\'startdate uk-height-1-1 uk-padding-small\' itemprop=\'startDate\' datetime=\'{{Start Date:STARTDATE;%Y-%b-%d}}\' style=\'background-color:{{Event Colour:COLOUR}};color:{{Foreground Colour:FGCOLOUR}}\'>\n' +
		'                    {{Start Date:STARTDATE;<div uk-icon=\'icon: calendar\'></div><div class=\'startmonth\'>%b</div><div class=\'startday\'>%d</div>}}\n' +
		'                </div>\n' +
		'            </div>\n' +
		'            <h3 class=\'uk-modal-title uk-width-expand eventtitle uk-padding-small uk-text-nowrap uk-overflow-hidden\' itemprop=\'name\' title=\'{{Title:TITLE}}\'>\n' +
		'                {{Truncated Title:TRUNCATED_TITLE:24chars}}\n' +
		'            </h3>\n' +
		'        </div>\n' +
		'        <!-- This is the modal -->\n' +
		'        <div id=\'modal-event{{Repeat id:RPID}}\' uk-modal=\'container:#jev_isoitem{{Repeat id:RPID}}\' class=\'jeviso-modal\'>\n' +
		'            <div class=\'uk-modal-dialog\'>\n' +
		'                <div class=\'uk-modal-header uk-padding-small uk-margin-remove\' uk-grid>\n' +
		'                    <h2 class=\'startdate\'>{{Start Date:STARTDATE;<div class=\'startmonth\'>%b</div><div class=\'startday\'>%d</div>}}</h2>\n' +
		'                    <h2 class=\'uk-modal-title uk-width-expand eventtitle\'>{{Title Link:TITLE_LINK}}<span class=\'eventcategory uk-display-block\'>{{Category:CATEGORY}}</span></h2>\n' +
		'                    <button class=\'uk-modal-close-default\' type=\'button\' uk-close></button>\n' +
		'                </div>\n' +
		'                <div class=\'uk-modal-body uk-padding-small\' >\n' +
		'                    {{Scaled Image:JEV_SIZEDIMAGE_1;400x300#<div class=\'jeviso_modal_image uk-width-1-1\'>%s</div>#}}\n' +
		'                    <div class=\'eventtime uk-margin-small-top \' uk-grid>\n' +
		'                        <div uk-icon=\'icon: clock\'></div>\n' +
		'                        <div class=\'uk-width-expand\'>\n' +
		'                            <div class=\'eventdetaillink uk-float-right uk-button uk-button-primary\'>\n' +
		'                              {{Link Start:LINKSTART}} View in Calendar {{Link End:LINKEND}}\n' +
		'                            </div>\n' +
		'                            <div class=\'timelabel\'>\n' +
		'                                Time\n' +
		'                            </div>\n' +
		'                            {{Start Time:STARTTIME;%l:%M %p# %s#No Specific time}}{{End Time:ENDTIME;%l:%M %p# - %s}}\n' +
		'                        </div>\n' +
		'                    </div>\n' +
		'                    <div class=\'eventdetails  uk-margin-small-top \' uk-grid>\n' +
		'                        <div uk-icon=\'icon: info\'></div>\n' +
		'                        <div class=\'uk-width-expand\'>\n' +
		'                            <div class=\'detailslabel\'>\n' +
		'                                Event Details\n' +
		'                            </div>\n' +
		'                            {{Truncated Description:TRUNCATED_DESC:30words}}\n' +
		'                        </div>\n' +
		'                    </div>\n' +
		'                    <div class=\'calendarlinks  uk-margin-small-top\' uk-grid>\n' +
		'                        <div uk-icon=\'icon: calendar\'></div>\n' +
		'                        <div class=\'uk-width-expand\'>\n' +
		'                            <div class=\'exportlabel\'>Export Event to Your Calendar</div>\n' +
		'                            <div>\n' +
		'                                <a href=\'{{Save to Google:ICALGOOGLE}}\' title=\'Save to Google Calendar\' rel=\'nofollow\'>\n' +
		'                                    <span uk-icon=\'icon: google\'></span>\n' +
		'                                </a>\n' +
		'                                <a href=\'{{Save Ical Link:ICALSAVE}}\' title=\'Save to Calendar\' rel=\'nofollow\'>\n' +
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
		'                                <link itemprop=\'url\' href=\'{{Location Url:JEVLOCATION_URL}}\'>\n' +
		'                                <span itemprop=\'geo\' itemscope=\'\' itemtype=\'http://schema.org/GeoCoordinates\'>\n' +
		'                                    <meta itemprop=\'latitude\' content=\'{{Latitude:JEVLOCATION_LAT}}\'>\n' +
		'                                    <meta itemprop=\'longitude\' content=\'{{Longitude:JEVLOCATION_LON}}\'>\n' +
		'                                </span>\n' +
		'                                <div class=\'jfloat-venue-name\' itemprop=\'name\' title=\'{{Location Title:JEVLOCATION_TITLE}}\'>\n' +
		'                                    {{Location Title:JEVLOCATION_TITLE}}\n' +
		'                                </div>\n' +
		'                                <div class=\'jfloat-city-state\' itemprop=\'address\' itemscope=\'\' itemtype=\'http://schema.org/PostalAddress\'>\n' +
		'                                    <span itemprop=\'addressLocality\'>{{Location City:JEVLOCATION_CITY}}</span>\n' +
		'                                    <span itemprop=\'addressRegion\'>{{Location State:JEVLOCATION_STATE}}</span>\n' +
		'                                </div>\n' +
		'                            </div>\n' +
		'                        </div>\n' +
		'                    </div>\n' +
		'                </div>\n' +
		'            </div>\n' +
		'        </div>\n' +
		'    </div>\n' +
		'</div>\n' +
		'',
	'css': '.jeviso_itemcontainer:after {\n' +
		'    content: \'\';\n' +
		'    display: block;\n' +
		'    clear: both;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item  .jfloat-event {\n' +
		'    max-width: 100%;\n' +
		'    width: 100%;\n' +
		'    box-sizing: border-box;\n' +
		'    border-radius: 3px;\n' +
		'    cursor: pointer;\n' +
		'    border: 1px solid #f0f0f1;\n' +
		'    display: inline-block;\n' +
		'    margin-right: 1%;\n' +
		'    margin-bottom: 10px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item .jfloat-event:hover {\n' +
		'    box-shadow: 0 0 8px 2px rgba(0, 0, 0, 0.4);\n' +
		'}\n' +
		'\n' +
		'#jeviso_module .jeviso_item .jeviso_item_image img {\n' +
		'    max-width: 100%;\n' +
		'    border-radius: 3px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_title {\n' +
		'    padding: 5px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_date {\n' +
		'    padding: 5px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_body {\n' +
		'    word-wrap: break-word;\n' +
		'    padding: 5px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_footer {\n' +
		'    width: 100%;\n' +
		'    max-width: 100%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_footer .jeviso_item_catcolor {\n' +
		'    border-left-width: 10px;\n' +
		'    border-left-style: solid;\n' +
		'    display: inline-block;\n' +
		'    padding-left: 2px;\n' +
		'    text-align: left;\n' +
		'    width: 35%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_item .jeviso_item_footer .jeviso_item_rmlink {\n' +
		'    display: inline-block;\n' +
		'    text-align: right;\n' +
		'    width: 55%;\n' +
		'}\n' +
		'.uk-button-primary > .ev_link_row,\n' +
		'.uk-button-primary > .ev_link_row:hover {\n' +
		'   color:inherit;\n' +
		'}\n' +
		'#jeviso-modal {\n' +
		'cursor:auto;\n' +
		'}\n' +
		'.jeviso_modal_image > img {\n' +
		'  margin: 0 auto;\n' +
		'  display:block;\n' +
		'}\n' +
		'/* Float module specific */\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image img {\n' +
		'    width: 100%;\n' +
		'    max-width: 100%;\n' +
		'    border-top-right-radius: 3px;\n' +
		'    border-top-left-radius: 3px;\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item {\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image {\n' +
		'    overflow: hidden;\n' +
		'    position: relative;\n' +
		'    border-bottom-right-radius: 0;\n' +
		'    border-bottom-left-radius: 0;\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image img {\n' +
		'    position: absolute;\n' +
		'    top: -50%;\n' +
		'    bottom: -50%;\n' +
		'    width: 100%;\n' +
		'    margin: auto;\n' +
		'    height: auto;\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item.listv {\n' +
		'    width: 100%;\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item.listv .noleftpadding {\n' +
		'    padding-left: 0;\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item h3.eventtitle {\n' +
		'    font-size: 1.3rem !important;\n' +
		'    font-weight: bold;\n' +
		'    text-overflow: ellipsis;\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item h3.eventtitle .eventcategory {\n' +
		'    display: block;\n' +
		'    font-size: 1.0rem !important;\n' +
		'    margin-top: 10px;\n' +
		'    font-weight: normal;\n' +
		'    text-overflow: ellipsis;\n' +
		'    overflow-x: hidden;\n' +
		'}\n' +
		'#jeviso_module .jeviso_itemcontainer .jeviso_item div.startdate {\n' +
		'    text-align: center;\n' +
		'}\n' +
		'.jeviso_modal {\n' +
		'    cursor: initial;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog {\n' +
		'    width: 800px;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .uk-modal-header {\n' +
		'    margin: 0;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .uk-button-primary a {\n' +
		'    color: #fff;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .eventtime,\n' +
		'.jeviso_modal .uk-modal-dialog .eventdetails,\n' +
		'.jeviso_modal .uk-modal-dialog .calendarlinks {\n' +
		'    padding-bottom: 5px;\n' +
		'    border-bottom: 1px solid #e5e5e5;\n' +
		'    margin-bottom: 5px;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .startdate {\n' +
		'    font-size: 1.5rem !important;\n' +
		'    padding-left: 10px;\n' +
		'    text-align: center;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .startdate .startmonth {\n' +
		'    height: 40px;\n' +
		'    line-height: 40px;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .startdate .startday {\n' +
		'    height: 25px;\n' +
		'    line-height: 25px;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .uk-modal-title {\n' +
		'    padding-left: 40px;\n' +
		'    text-overflow: ellipsis;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .uk-modal-title a {\n' +
		'    font-size: 1.5rem !important;\n' +
		'    height: 40px;\n' +
		'    line-height: 40px;\n' +
		'}\n' +
		'.jeviso_modal .uk-modal-dialog .uk-modal-title .eventcategory {\n' +
		'    display: block;\n' +
		'    height: 25px;\n' +
		'    line-height: 25px;\n' +
		'    font-size: 1.3rem !important;\n' +
		'    opacity: 0.6;\n' +
		'    text-overflow: ellipsis;\n' +
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
		'}\n' +
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
	'js': 'document.addEventListener(\'DOMContentLoaded\', function () {\n' +
		'\n' +
		'    jQuery(\'div.jeviso_container\').on(\'jevisolist\', function () {\n' +
		'        var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);\n' +
		'\n' +
		'        if (w > 960) {\n' +
		'            jQuery(\'.jfloat-event\').addClass(\'uk-height-small\');\n' +
		'            jQuery(\'.jfloat-event .jeviso_item_image\').addClass(\'uk-float-right\');\n' +
		'            jQuery(\'.jfloat-event .jeviso_eventsummary, .jfloat-event .jeviso_item_image\').addClass(\'uk-width-1-2@m\');\n' +
		'            jQuery(\'.jfloat-event .jeviso_eventsummary\').addClass(\'uk-height-1-1\');\n' +
		'            jQuery(\'.jfloat-event .uk-first-column\').addClass(\'noleftpadding\');\n' +
		'        }\n' +
		'    });\n' +
		'    jQuery(\'div.jeviso_container\').on(\'jevisogrid\', function () {\n' +
		'        jQuery(\'.jfloat-event\').removeClass(\'uk-height-small\');\n' +
		'        jQuery(\'.jfloat-event .jeviso_item_image\').removeClass(\'uk-float-right\');\n' +
		'        jQuery(\'.jfloat-event .jeviso_eventsummary, .jfloat-event .jeviso_item_image\').removeClass(\'uk-width-1-2@m\');\n' +
		'        jQuery(\'.jfloat-event .jeviso_eventsummary\').removeClass(\'uk-height-1-1\');\n' +
		'        jQuery(\'.jfloat-event .uk-first-column\').removeClass(\'noleftpadding\');\n' +
		'    });\n' +
		'    jQuery(window).on(\'resize\' , function() {\n' +
		'        if(jQuery(\'div.jeviso_container .listv\').length) {\n' +
		'            var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);\n' +
		'            if (w > 960) {\n' +
		'                jQuery(\'.jfloat-event\').addClass(\'uk-height-small\');\n' +
		'                jQuery(\'.jfloat-event .jeviso_item_image\').addClass(\'uk-float-right\');\n' +
		'                jQuery(\'.jfloat-event .jeviso_eventsummary, .jfloat-event .jeviso_item_image\').addClass(\'uk-width-1-2@m\');\n' +
		'                jQuery(\'.jfloat-event .jeviso_eventsummary\').addClass(\'uk-height-1-1\');\n' +
		'                jQuery(\'.jfloat-event .uk-first-column\').addClass(\'noleftpadding\');\n' +
		'            }\n' +
		'            else {\n' +
		'                jQuery(\'.jfloat-event\').removeClass(\'uk-height-small\');\n' +
		'                jQuery(\'.jfloat-event .jeviso_item_image\').removeClass(\'uk-float-right\');\n' +
		'                jQuery(\'.jfloat-event .jeviso_eventsummary, .jfloat-event .jeviso_item_image\').removeClass(\'uk-width-1-2@m\');\n' +
		'                jQuery(\'.jfloat-event .jeviso_eventsummary\').removeClass(\'uk-height-1-1\');\n' +
		'                jQuery(\'.jfloat-event .uk-first-column\').removeClass(\'noleftpadding\');\n' +
		'            }\n' +
		'        }\n' +
		'    });\n' +
		'});\n' +
		'',
	'info': 'UIKit 3 based theme that combines with Float Theme to show event image. date, time, title and location with popup details in modal.  <strong>This layout requires the Silver Member Float Theme, Standard Images and Managed Locations Addons to work together with a UIKit 3 based template.</strong>',
	'templatetop' : '    <div id="jeviso_module" class="jeviso_day">\n' +
		'        <div class="jeviso_itemcontainer {COLUMNS+1}@xl {COLUMNS}@l {COLUMNS-1}@m {COLUMNS-2}@s uk-grid" itemscope itemtype="http://schema.org/Event">\n',
	'templatebottom' : '</div></div>'
});

jevpreview.push({
	'id': 6,
	'name': 'Bootstrap 4 Float Popup',
	'image': 'http://www.jevents.net/jevlayouts/latest5.png',
	'code': "<div id='jeviso_item{{Repeat id:RPID}}' class='jeviso_item {COLUMNS}'>\n" +
		"\t<div class='jfloat-event ng-scope ng-isolate-scope container' itemscope='' itemtype='http://schema.org/Event'>\n" +
		"\t\t<div class='jeviso_item_image row' data-toggle='modal' data-target='#modal-event{{Repeat id:RPID}}'  data-bs-toggle='modal' data-bs-target='#modal-event{{Repeat id:RPID}}'>\n" +
		"\t\t\t{{Scaled Image:JEV_SIZEDIMAGE_1;400x300}}\n" +
		"\t\t</div>\n" +
		"\t\t<div id='eventcontainer{{Repeat id:RPID}}' class='jeviso_eventsummary row' data-toggle='modal' data-target='#modal-event{{Repeat id:RPID}}' data-bs-toggle='modal' data-bs-target='#modal-event{{Repeat id:RPID}}'>\n" +
		"\t\t\t<div class='startdate col-3 p-1' itemprop='startDate' datetime='{{Start Date:STARTDATE;%Y-%b-%d}}' style='background-color:{{Event Colour:COLOUR}};color:{{Foreground Colour:FGCOLOUR}}'>\n" +
		"\t\t\t\t{{Start Date:STARTDATE;<div class='icon-calendar'></div><div class='startmonth'>%b</div><div class='startday'>%d</div>}}\n" +
		"\t\t\t</div>\n" +
		"\t\t\t<div class=\" col-9\">\n" +
		"\t\t\t\t<h3 class=' eventtitle p-1 ' itemprop='name' title='{{Title:TITLE}}'>\n" +
		"\t\t\t\t\t{{Truncated Title:TRUNCATED_TITLE:24chars}}\n" +
		"\t\t\t\t</h3>\n" +
		"\t\t\t</div>\n" +
		"\t\t</div>\n" +
		"\t\t<!-- This is the modal -->\n" +
		"\t\t<div id='modal-event{{Repeat id:RPID}}' class='jeviso-modal modal fade'>\n" +
		"\t\t\t<div class='modal-dialog modal-lg'>\n" +
		"\t\t\t\t<div class=\"modal-content container-fluid\">\n" +
		"\t\t\t\t\t<div class='modal-header p-1 m-0 ' >\n" +
		"\t\t\t\t\t\t<div class=\"modal-title row col-12\">\n" +
		"\t\t\t\t\t\t\t<div class=\"col-3\">\n" +
		"\t\t\t\t\t\t\t\t<h2 class='startdate'>{{Start Date:STARTDATE;<div class='startmonth'>%b</div><div class='startday'>%d</div>}}</h2>\n" +
		"\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t\t<div class=\"col-9\">\n" +
		"\t\t\t\t\t\t\t\t<h2 class='eventtitle'>{{Title Link:TITLE_LINK}}<span class='eventcategory d-block'>{{Category:CATEGORY}}</span></h2>\n" +
		"\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" data-bs-dismiss=\"modal\" aria-label=\"Close\">\n" +
		"\t\t\t\t\t\t\t<span aria-hidden=\"true\">&times;</span>\n" +
		"\t\t\t\t\t\t</button>\n" +
		"\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t<div class='modal-body p-3 ' >\n" +
		"\t\t\t\t\t\t{{Scaled Image:JEV_SIZEDIMAGE_1;400x300#<div class='jeviso_modal_image w-100'>%s</div>#}}\n" +
		"\t\t\t\t\t\t<div class='eventtime m-1 row '>\n" +
		"\t\t\t\t\t\t\t<div class='icon-clock col-1'></div>\n" +
		"\t\t\t\t\t\t\t<div class='col-11'>\n" +
		"\t\t\t\t\t\t\t\t<a href='{{Raw Link:LINK}}' class='eventdetaillink float-right btn btn-primary'>\n" +
		"\t\t\t\t\t\t\t\t\tView in Calendar\n" +
		"\t\t\t\t\t\t\t\t</a>\n" +
		"\t\t\t\t\t\t\t\t<div class='timelabel'>\n" +
		"\t\t\t\t\t\t\t\t\tTime\n" +
		"\t\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t\t\t{{Start Time:STARTTIME;%l:%M %p# %s#No Specific time}}{{End Time:ENDTIME;%l:%M %p# - %s}}\n" +
		"\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t<div class='eventdetails  m-1 row' >\n" +
		"\t\t\t\t\t\t\t<div class='icon-info col-1'></div>\n" +
		"\t\t\t\t\t\t\t<div class=' col-11'>\n" +
		"\t\t\t\t\t\t\t\t<div class='detailslabel'>\n" +
		"\t\t\t\t\t\t\t\t\tEvent Details\n" +
		"\t\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t\t\t{{Truncated Description:TRUNCATED_DESC:30words}}\n" +
		"\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t<div class='calendarlinks  m-1 row' >\n" +
		"\t\t\t\t\t\t\t<div class='icon-calendar col-1'></div>\n" +
		"\t\t\t\t\t\t\t<div class='col-11'>\n" +
		"\t\t\t\t\t\t\t\t<div class='exportlabel'>Export Event to Your Calendar</div>\n" +
		"\t\t\t\t\t\t\t\t<div>\n" +
		"\t\t\t\t\t\t\t\t\t<a href='{{Save to Google:ICALGOOGLE}}' title='Save to Google Calendar'>\n" +
		"\t\t\t\t\t\t\t\t\t\t<span class='icon-social-google'></span>\n" +
		"\t\t\t\t\t\t\t\t\t</a>\n" +
		"\t\t\t\t\t\t\t\t\t<a href='{{Save Ical Link:ICALSAVE}}' title='Save to Calendar'>\n" +
		"\t\t\t\t\t\t\t\t\t\t<span class='icon-calendar'></span>\n" +
		"\t\t\t\t\t\t\t\t\t</a>\n" +
		"\t\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t<div class='location  m-1 row' >\n" +
		"\t\t\t\t\t\t\t<div class='icon-location-pin col-1'></div>\n" +
		"\t\t\t\t\t\t\t<div class='col-11'>\n" +
		"\t\t\t\t\t\t\t\t<div class='locationlabel'>Location</div>\n" +
		"\t\t\t\t\t\t\t\t<div class='jfloat-venue' itemprop='location' itemscope='' itemtype='http://schema.org/EventVenue'>\n" +
		"\t\t\t\t\t\t\t\t\t<link itemprop='url' href='{{Location Url:JEVLOCATION_URL}}'>\n" +
		"\t\t\t\t\t\t\t\t\t<span itemprop='geo' itemscope='' itemtype='http://schema.org/GeoCoordinates'>\n" +
		"\t                                    <meta itemprop='latitude' content='{{Latitude:JEVLOCATION_LAT}}'>\n" +
		"\t                                    <meta itemprop='longitude' content='{{Longitude:JEVLOCATION_LON}}'>\n" +
		"\t                                </span>\n" +
		"\t\t\t\t\t\t\t\t\t<div class='jfloat-venue-name' itemprop='name' title='{{Location Title:JEVLOCATION_TITLE}}'>\n" +
		"\t\t\t\t\t\t\t\t\t\t{{Location Title:JEVLOCATION_TITLE}}\n" +
		"\t\t\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t\t\t\t<div class='jfloat-city-state' itemprop='address' itemscope='' itemtype='http://schema.org/PostalAddress'>\n" +
		"\t\t\t\t\t\t\t\t\t\t<span itemprop='addressLocality'>{{Location City:JEVLOCATION_CITY}}</span>\n" +
		"\t\t\t\t\t\t\t\t\t\t<span itemprop='addressRegion'>{{Location State:JEVLOCATION_STATE}}</span>\n" +
		"\t\t\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t\t</div>\n" +
		"\t\t\t\t\t</div>\n" +
		"\t\t\t\t</div>\n" +
		"\t\t\t</div>\n" +
		"\t\t</div>\n" +
		"\t</div>\n" +
		"</div>\n",
	'css': ".jeviso_itemcontainer:after {\n" +
		"    content: '';\n" +
		"    display: block;\n" +
		"    clear: both;\n" +
		"}\n" +
		"#jeviso_module .jeviso_item  .jfloat-event {\n" +
		"    max-width: 100%;\n" +
		"    width: 100%;\n" +
		"    box-sizing: border-box;\n" +
		"    border-radius: 3px;\n" +
		"    cursor: pointer;\n" +
		"    border: 1px solid #f0f0f1;\n" +
		"    display: inline-block;\n" +
		"    margin-right: 1%;\n" +
		"    margin-bottom: 10px;\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item .jfloat-event:hover {\n" +
		"    box-shadow: 0 0 8px 2px rgba(0, 0, 0, 0.4);\n" +
		"}\n" +
		"\n" +
		"#jeviso_module .jeviso_item .jeviso_item_image img {\n" +
		"    max-width: 100%;\n" +
		"    border-radius: 3px;\n" +
		"}\n" +
		"#jeviso_module .jeviso_item .jeviso_item_title {\n" +
		"    padding: 5px;\n" +
		"}\n" +
		"#jeviso_module .jeviso_item .jeviso_item_date {\n" +
		"    padding: 5px;\n" +
		"}\n" +
		"#jeviso_module .jeviso_item .jeviso_item_body {\n" +
		"    word-wrap: break-word;\n" +
		"    padding: 5px;\n" +
		"}\n" +
		"#jeviso_module .jeviso_item .jeviso_item_footer {\n" +
		"    width: 100%;\n" +
		"    max-width: 100%;\n" +
		"}\n" +
		"#jeviso_module .jeviso_item .jeviso_item_footer .jeviso_item_catcolor {\n" +
		"    border-left-width: 10px;\n" +
		"    border-left-style: solid;\n" +
		"    display: inline-block;\n" +
		"    padding-left: 2px;\n" +
		"    text-align: left;\n" +
		"    width: 35%;\n" +
		"}\n" +
		"#jeviso_module .jeviso_item .jeviso_item_footer .jeviso_item_rmlink {\n" +
		"    display: inline-block;\n" +
		"    text-align: right;\n" +
		"    width: 55%;\n" +
		"}\n" +
		".uk-button-primary > .ev_link_row,\n" +
		".uk-button-primary > .ev_link_row:hover {\n" +
		"   color:inherit;\n" +
		"}\n" +
		"#jeviso-modal {\n" +
		"cursor:auto;\n" +
		"}\n" +
		".jeviso_modal_image > img {\n" +
		"  margin: 0 auto;\n" +
		"  display:block;\n" +
		"}\n" +
		"/* Float module specific */\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image img {\n" +
		"    width: 100%;\n" +
		"    max-width: 100%;\n" +
		"    border-top-right-radius: 3px;\n" +
		"    border-top-left-radius: 3px;\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item {\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image {\n" +
		"   height:300px;\n" +
		"    overflow: hidden;\n" +
		"    position: relative;\n" +
		"    border-bottom-right-radius: 0;\n" +
		"    border-bottom-left-radius: 0;\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image img {\n" +
		"    position: absolute;\n" +
		"    top: -50%;\n" +
		"    bottom: -50%;\n" +
		"    width: 100%;\n" +
		"    margin: auto;\n" +
		"    height: auto;\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item.listv {\n" +
		"    width: 100%;\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item.listv .noleftpadding {\n" +
		"    padding-left: 0;\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item h3.eventtitle {\n" +
		"    font-size: 1.3rem !important;\n" +
		"    font-weight: bold;\n" +
		"    text-overflow: ellipsis;\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item h3.eventtitle .eventcategory {\n" +
		"    display: block;\n" +
		"    font-size: 1.0rem !important;\n" +
		"    margin-top: 10px;\n" +
		"    font-weight: normal;\n" +
		"    text-overflow: ellipsis;\n" +
		"}\n" +
		"#jeviso_module .jeviso_itemcontainer .jeviso_item div.startdate {\n" +
		"    text-align: center;\n" +
		"}\n" +
		".jeviso_modal {\n" +
		"    cursor: initial;\n" +
		"}\n" +
		".jeviso_modal .modal-dialog {\n" +
		"    width: 800px;\n" +
		"}\n" +
		".jeviso_modal .modal-dialog .modal-header {\n" +
		"    margin: 0;\n" +
		"}\n" +
		"\n" +
		".jeviso_modal .modal-dialog .eventtime,\n" +
		".jeviso_modal .modal-dialog .eventdetails,\n" +
		".jeviso_modal .modal-dialog .calendarlinks {\n" +
		"    padding-bottom: 5px;\n" +
		"    border-bottom: 1px solid #e5e5e5;\n" +
		"    margin-bottom: 5px;\n" +
		"}\n" +
		".jeviso_modal .modal-dialog .startdate {\n" +
		"    font-size: 1.5rem !important;\n" +
		"    padding-left: 10px;\n" +
		"    text-align: center;\n" +
		"}\n" +
		".jeviso_modal .modal-dialog .startdate .startmonth {\n" +
		"    height: 40px;\n" +
		"    line-height: 40px;\n" +
		"}\n" +
		".jeviso_modal .modal-dialog .startdate .startday {\n" +
		"    height: 25px;\n" +
		"    line-height: 25px;\n" +
		"}\n" +
		".jeviso_modal .modal-dialog .modal-title {\n" +
		"    padding-left: 40px;\n" +
		"    text-overflow: ellipsis;\n" +
		"}\n" +
		".jeviso_modal .modal-dialog .modal-title a {\n" +
		"    font-size: 1.5rem !important;\n" +
		"    height: 40px;\n" +
		"    line-height: 40px;\n" +
		"}\n" +
		".jeviso_modal .modal-dialog .modal-title .eventcategory {\n" +
		"    display: block;\n" +
		"    height: 25px;\n" +
		"    line-height: 25px;\n" +
		"    font-size: 1.3rem !important;\n" +
		"    opacity: 0.6;\n" +
		"    text-overflow: ellipsis;\n" +
		"}",
	'js': "function jevModalsToBody(selector)\n" +
		"{\n" +
		"         let modals = jQuery(selector);\n" +
		"          modals.appendTo('body');\n" +
		"}\n" +
		"\n" +
		"document.addEventListener('DOMContentLoaded', function () {\n" +
		"         jevModalsToBody('.mod_events_latest_data .jeviso-modal.modal');\n" +
		"    jQuery('.mod_events_latest_data').on('jevisomoreevents', function () {\n" +
		"         jevModalsToBody('.mod_events_latest_data .jeviso-modal.modal');\n" +
		"    });\n" +
		"});\n",
	'info': 'Boostrap 4 based theme that combines with Float Theme to show event image. date, time, title and location with popup details in modal.  <strong>This layout requires the Silver Member Float Theme, Standard Images and Managed Locations Addons to work together with a Bootstrap 4 based template.</strong>',
	'templatetop' : "    <div id=\"jeviso_module\" class=\"jeviso_day \">\n" +
		"        <div class=\"jeviso_itemcontainer row\" itemscope itemtype=\"http://schema.org/Event\">\n",
	'templatebottom' : '</div></div>'
});

function loadJevPreview(target, csstarget, jstarget, ttop, tbot) {
	jQuery(document).ready(function ($) {
		// Setup click action on default option (which is translated)
		var defaultLI = jQuery("#dropdownUL_" + target + " li:first-child");

		var currentCode = defaultsEditorPlugin.extract(target);

		var currentCSS = jQuery("#" + csstarget).val();
		var currentJS = jQuery("#" + jstarget).val();

		var currentTTop = defaultsEditorPlugin.extract(ttop);
		var currentTBot = defaultsEditorPlugin.extract(tbot);

		defaultLI.on('click', function (event) {
			event.preventDefault();
			jQuery("#" + csstarget).val(currentCSS);
			jQuery("#" + jstarget).val(currentJS);
			defaultsEditorPlugin.inject(ttop, currentTTop);
			defaultsEditorPlugin.inject(tbot, currentTBot);
			defaultsEditorPlugin.inject(target, currentCode );
			window.scrollTo(0,0);
		});
		jQuery(jevpreview).each(function (index, item) {
			var info = "";
			if (item.image) {
				info = "<img src='" + item.image + "' style='margin:0px 10px 10px 10px;'/><br/>";
			}
			info += "<div>" + item.info + "</div>";
			var elem = jQuery('<li role="presentation"><a  role="menuitem" tabindex="-1" href="#" class="dropdownpopover" data-title="\n' + item.name + '" data-content="\n' + info + '" data-bs-content="\n' + info + '">\n' + item.name + '</a></li>');
			var dropdowntarget = jQuery("#dropdownUL_" + target);
			elem.appendTo(dropdowntarget);
			elem.on('click', function (event) {
				event.preventDefault();
				jQuery("#" + jstarget).val(item.js);
				jQuery("#" + csstarget).val(item.css);

				defaultsEditorPlugin.inject(ttop, item.templatetop);
				defaultsEditorPlugin.inject(tbot, item.templatebottom);
				defaultsEditorPlugin.inject(target, item.code );
				window.scrollTo(0,0);
			});
		});
		$('a.dropdownpopover').popover({container: 'body', trigger: 'hover', placement: 'right', html: true});
		jQuery("#" + target).on('change', function (event) {
			currentCode = defaultsEditorPlugin.extract(target);
			currentCSS = jQuery("#" + csstarget).val() ;
			currentJS = jQuery("#" + jstarget).val();
			currentTTop = defaultsEditorPlugin.extract(ttop);
			currentTBot = defaultsEditorPlugin.extract(tbot);
			// must reset default option selection event because of closures!
			defaultLI.off('click');
			defaultLI.on('click', function (event) {
				event.preventDefault();
				jQuery("#" + csstarget).val(currentCSS);
				jQuery("#" + jstarget).val(currentJS);
				defaultsEditorPlugin.inject(ttop, currentTTop);
				defaultsEditorPlugin.inject(tbot, currentTBot);
				defaultsEditorPlugin.inject(target, currentCode );
				window.scrollTo(0,0);
			});
		});
		jQuery("#" + csstarget + ',#\n' + jstarget).on('change', function (event) {
			currentCode = jQuery("#" + target).val();
			currentCSS = jQuery("#" + csstarget).val();
			currentJS = jQuery("#" + jstarget).val();
			currentTTop = jQuery("#" + ttop).val();
			currentTBot = jQuery("#" + tbot).val();
			// must reset default option selection event because of closures!
			defaultLI.off('click');
			defaultLI.on('click', function (event) {
				event.preventDefault();
				jQuery("#" + csstarget).val(currentCSS);
				jQuery("#" + jstarget).val(currentJS);
				defaultsEditorPlugin.inject(ttop, currentTTop);
				defaultsEditorPlugin.inject(tbot, currentTBot);
				defaultsEditorPlugin.inject(target, currentCode );
				window.scrollTo(0,0);
			});
		});
	});
}
