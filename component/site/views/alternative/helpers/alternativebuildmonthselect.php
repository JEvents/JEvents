<?php 
defined('_JEXEC') or die('Restricted access');

class AlternativeBuildMonthSelect {

	var $view = null;
	var $result = "";
	
	public function __construct($view, $link, $month, $year){
		for( $a=-6; $a<6; $a++ ){
			$m = $month+$a;
			$y=$year;
			if ($m<=0){
				$m+=12;
				$y-=1;
			}
			if ($m>12){
				$m-=12;
				$y+=1;
			}
			$name_of_month = JEVHelper::getMonthName($m)." $y";
			$monthslist[] = JHTML::_('select.option', "$m|$y", $name_of_month );
		}
		$link = str_replace(array("day.listevents","week.listevents","year.listevents","cat.listevents","icalrepeat.detail","icalevent.detail"),"month.calendar",$link);
		
		$tosend = "<script type='text/javascript'>\n";
		$tosend .= "/* <![CDATA[ */\n";
		$tosend .= " function selectMD(elem) {
        var ym = elem.options[elem.selectedIndex].value.split('|');\n";

		$link.="day=1&month=MMMMmmmm&year=YYYYyyyy";
		$link2 = JRoute::_($link,false);
		$tosend .= "var link = '$link2';\n";
		// This is needed in case SEF is not activated
		$tosend .= "link = link.replace(/&/g,'&');\n";
		$tosend .= "link = link.replace(/MMMMmmmm/g,ym[0]);\n";
		$tosend .= "link = link.replace(/YYYYyyyy/g,ym[1]);\n";
		$tosend .= "location.replace(link);\n";
		$tosend .= "}\n";
		$tosend .= "/* ]]> */\n";
		$tosend .= "</script>\n";
		$tosend .= JHTML::_('select.genericlist', $monthslist, 'monthyear', "onchange=\"selectMD(this);\"", 'value', 'text', "$month|$year" );
		$this->result = $tosend;
	}

}