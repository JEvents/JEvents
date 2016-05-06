/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view_detailJQ.js 1539 2010-12-07 10:30:01Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

function jevSetupAddLink() {
	var classes = ["td.cal_td_today"
				, "td.cal_td_today"
				, "td.cal_td_daysnoevents"
				, "td.cal_td_dayshasevents"
				, "td.jev_daynoevents"
				, "td.jev_today"
				, "div.jev_daynum"
				, "td.jev_daynum"
				, "td.todayclr"
				, "td.weekdayclr"
				, "td.sundayclr"
				, "td.sundayemptyclr"
				, ".cal_div_daysnoevents"
				, ".cal_div_today"
				, "td.cal_today"
				, "td.cal_daysnoevents"
				, "td.cal_dayshasevents"];
	
	for (var ci = 0; ci < classes.length; ci++)
	{
		tds = jevjq(classes[ci]);
		tds.each (function (index) {
			element = jevjq(this);
			element.on('mouseover', function() {
				 jevjq(this).addClass('showjevadd');
			});
			element.on('mouseout', function() {
				 jevjq(this).removeClass('showjevadd');
			});
		});
	}

}

jevjq(document).on('ready', function() {
	jevSetupAddLink();
	var bootstrap3_enabled = (typeof jQuery().emulateTransitionEnd == 'function');
	// move dialog to main body because some template wrap it in a relative positioned element - wrapped to ensure our namespaced bootstrap picks it up!
	var subwrap = jQuery("<div>", {class:"jevbootstrap"});
	subwrap.appendTo("body");
	if (jQuery(".action_dialogJQ").length) {
		jQuery(".action_dialogJQ").appendTo(subwrap);
		if (bootstrap3_enabled && jQuery(".action_dialogJQ").hasClass('hide')){
			jQuery(".action_dialogJQ").removeClass('hide');
		}
	}
	if (jQuery(".ical_dialogJQ").length) {
		jQuery(".ical_dialogJQ").appendTo(subwrap);
		if (bootstrap3_enabled && jQuery(".ical_dialogJQ").hasClass('hide')){
			jQuery(".ical_dialogJQ").removeClass('hide');
		}
	}
});


function clickEditButton(){
}

function clickIcalSaveButton(){
}

function closedialog() {
}

function clickIcalButton(){
	}

function closeical() {
}

