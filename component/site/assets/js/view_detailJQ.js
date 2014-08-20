/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view_detail.js 1539 2010-12-07 10:30:01Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2014 GWE Systems Ltd, 2006-2008 JEvents Project Group
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
});

// no-op backward compatible functions
function clickIcalButton(){
}
function clickEditButton(){
}