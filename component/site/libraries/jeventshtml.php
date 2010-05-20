<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jeventshtml.php 1763 2010-05-18 10:04:45Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

// TODO replace with JDate 

class JEventsHTML{

	function buildRadioOption( $arr, $tag_name, $tag_attribs, $key, $text, $selected ) {
		$html = ''; //"\n<div name=\"$tag_name\" $tag_attribs>";

		for( $i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;

			$sel = '';

			if( is_array( $selected )) {
				foreach( $selected as $obj ) {
					$k2 = $obj->$key;
					if( $k == $k2 ) {
						$sel = ' checked="checked"';
						break;
					}
				}
			}else{
				$sel = ( $k == $selected ? ' checked="checked"' : '' );
			}

			$html .= "\n\t"
			. '<input name="' . $tag_name . '" type="radio" value="' . $k . '" id="' .  $tag_name . $k . '"'
			. $sel . ' '
			. $tag_attribs
			. ' />' . "\n"
			. '<label for="' . $tag_name . $k . '">'
			. $t
			. '</label>'
			. "\n"
			;
		}
		//$html .= "\n</select>\n";
		return $html;
	}

	function buildReccurDaySelect( $reccurday, $tag_name, $args ) {
		
		// get array
		$day_name = JEVHelper::getWeekdayLetter(null, 1);
		$day_name[0] = '<span class="sunday">' .   $day_name[0] . '</span>';
		$day_name[6] = '<span class="saturday">' . $day_name[6] . '</span>';
	
		$daynamelist[] = JHTML::_('select.option', '-1', '&nbsp;' . JText::_('JEV_BYDAYNUMBER') . '<br />' );

		for( $a=0; $a<7; $a++ ){
			$name_of_day	= '&nbsp;' . $day_name[$a]; //getDayName($a);
			$daynamelist[]	= JHTML::_('select.option', $a, $name_of_day );
		}

		$tosend = JEventsHTML::buildRadioOption( $daynamelist, $tag_name, $args, 'value', 'text', $reccurday );
		echo $tosend;
	}

	function buildMonthSelect( $month, $args ){
		for( $a=1; $a<13; $a++ ){
			$mnh = $a;
			if( $mnh <= '9' ) {
				$mnh = '0' . $mnh;
			}
			$name_of_month = JEVHelper::getMonthName($mnh);
			$monthslist[] = JHTML::_('select.option', $mnh, $name_of_month );
		}

		$tosend = JHTML::_('select.genericlist', $monthslist, 'month', $args, 'value', 'text', $month );
		echo $tosend;
	}

	function buildDaySelect( $year, $month, $day, $args ){
		$nbdays = date( 'd', mktime( 0, 0, 0, ( $month + 1 ), 0, $year ));

		for( $a=1; $a<=$nbdays; $a++ ) { //32
			$dys = $a;
			if( $dys <= '9' ) {
				$dys = '0' . $dys;
			}
			$dayslist[] = JHTML::_('select.option', $dys, $dys );
		}

		$tosend = JHTML::_('select.genericlist', $dayslist, 'day', $args, 'value', 'text', $day );
		echo $tosend;
	}

	function buildYearSelect( $year, $args ){
		$y = date( 'Y' );

		if( $year < $y-2 ){
			$yearslist[] = JHTML::_('select.option', $year, $year );
		}

		for( $i = $y-2; $i <= $y+5; $i++ ){
			$yearslist[] = JHTML::_('select.option', $i, $i );
		}

		if( $year > $y+5 ){
			$yearslist[] = JHTML::_('select.option', $year, $year );
		}

		$tosend = JHTML::_('select.genericlist', $yearslist, 'year', $args, 'value', 'text', $year );
		echo $tosend;
	}

	function buildViewSelect( $viewtype, $args ) {

		$cfg = & JEVConfig::getInstance();

		$viewlist[] = JHTML::_('select.option', 'day.listevents', 		JText::_('JEV_VIEWBYDAY') );
		$viewlist[] = JHTML::_('select.option', 'week.listevents', 	JText::_('JEV_VIEWBYWEEK') );
		$viewlist[] = JHTML::_('select.option', 'month.calendar', 	JText::_('JEV_VIEWBYMONTH') );
		$viewlist[] = JHTML::_('select.option', 'year.listevents', 	JText::_('JEV_VIEWBYYEAR') );

		if ($cfg->get('com_hideshowbycats', 0) == '0') {
			$viewlist[] = JHTML::_('select.option', 'cat.listevents', JText::_('JEV_VIEWBYCAT') );
		}

		$viewlist[] = JHTML::_('select.option', 'search.form', 	JText::_('JEV_SEARCH_TITLE') );

		$tosend = JHTML::_('select.genericlist', $viewlist, 'task', $args, 'value', 'text', $viewtype );
		echo $tosend;
	}

	function buildHourSelect( $start, $end, $inc, $tag_name, $tag_attribs, $selected, $format='' ) {

		$cfg = & JEVConfig::getInstance();

		$start	= intval( $start );
		$end 	= intval( $end );
		$inc 	= intval( $inc );
		$arr 	= array();
		$tmpi 	= '';

		for( $i = $start; $i <= $end; $i += $inc ) {
			if( $cfg->get('com_dateformat') == '1' ) { // US time
				if ($i > 11) {
					$tmpi = ($i-12) . ' pm';
				} else {
					$tmpi = $i . ' am';
				}
			}else{
				$tmpi = $format ? sprintf( $format, $i ) : $i;
			}

			$fi 	= $format ? sprintf( $format, $i ) : $i;
			$arr[] 	= JHTML::_('select.option', $fi, $tmpi );
		}

		return JHTML::_('select.genericlist', $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
	}

	/**
	 * Build HTML selection list of categories
	 *
	 * @param int $catid				Selected catid
	 * @param string $args				Additional HTML attributes for the <select> tag
	 * @param string $catidList			Restriction list of categories
	 * @param boolean $with_unpublished	Set true to build list with unpublished categories
	 * @param boolean $require_sel		First entry: true = Choose one category, false = All categories
	 * @param int $catidtop				Top level category ancestor
	 */
	function buildCategorySelect( $catid, $args, $catidList=null, $with_unpublished=false, $require_sel=false, $catidtop=0, $fieldname="catid", $sectionname=JEV_COM_COMPONENT, $excludeid=false){
		
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		
		$catsql = 'SELECT c.id, c.published, c.title as ctitle,p.title as ptitle, gp.title as gptitle, ggp.title as ggptitle, c.ordering ' .
				// for Joomfish onlu
				' , p.id as pid, gp.id as gpid, ggp.id as ggpid '.
				' FROM #__categories AS c' .
				' LEFT JOIN #__categories AS p ON p.id=c.parent_id' .
				' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id ' .
				' LEFT JOIN #__categories AS ggp ON ggp.id=gp.parent_id ' .
				//' LEFT JOIN #__categories AS gggp ON gggp.id=ggp.parent_id ' .
				' WHERE c.access<='.$db->Quote($user->aid) .
				' AND c.section = '.$db->Quote($sectionname);
		if ($with_unpublished) {
			$catsql .= ' AND c.published >= 0';
		} else {
			$catsql .= ' AND c.published = 1';
		}
		if ($excludeid) $catsql .= ' AND c.id NOT IN ('.$excludeid.')';
		if (is_string($catidList) && strlen(trim($catidList)) ) {
			$catsql .=' AND c.id IN (' . trim($catidList) . ')';
		}
		$catsql .=" ORDER BY c.ordering";
		
		$db->setQuery($catsql);
		//echo $db->_sql;
		$rows = $db->loadObjectList('id');
		
		foreach ($rows as $key=>$option) {
			$title = $option->ctitle;
			if (!is_null($option->ptitle)){
				// this doesn't; work in Joomfish
				//$title = $option->ptitle."=>".$title;
				if (array_key_exists($option->pid,$rows)){
					$title = $rows[$option->pid]->ctitle."=>".$title;
				}
				else {
					$title = $option->ptitle."=>".$title;
				}
			}
			if (!is_null($option->gptitle)){
				// this doesn't; work in Joomfish
				//$title = $option->gptitle."=>".$title;
				if (array_key_exists($option->gpid,$rows)){
					$title = $rows[$option->gpid]->ctitle."=>".$title;
				}
				else {
					$title = $option->gptitle."=>".$title;
				}
			}
			if (!is_null($option->ggptitle)){
				// this doesn't; work in Joomfish
				//$title = $option->ggptitle."=>".$title;
				if (array_key_exists($option->ggpid,$rows)){
					$title = $rows[$option->ggpid]->ctitle."=>".$title;
				}
				else {
					$title = $option->ggptitle."=>".$title;
				}
			}
			/*
			if (!is_null($option->gggptitle)){
				$title = $option->gggptitle."=>".$title;
			}
			*/
			$rows[$key]->name = $title;
		}
		JArrayHelper::sortObjects($rows,"ordering");
		
		$t_first_entry = ($require_sel) ? JText::_('JEV_EVENT_CHOOSE_CATEG') : JText::_('JEV_EVENT_ALLCAT');
		//$categories[] = JHTML::_('select.option', '0', JText::_('JEV_EVENT_CHOOSE_CATEG'), 'id', 'name' );
		$categories[] = JHTML::_('select.option', '0', $t_first_entry, 'id', 'name' );
		
		
		if ($with_unpublished) {
			for ($i=0;$i<count($rows);$i++) {
				if ($rows[$i]->published == 0) $rows[$i]->name = $rows[$i]->name . '('. JText::_('JEV_NOT_PUBLISHED') . ')';
			}
		}

		$categories = array_merge( $categories, $rows );
		$clist = JHTML::_('select.genericlist', $categories, $fieldname, $args, 'id', 'name', $catid );

		return $clist;
	}

	function buildWeekDaysCheck( $reccurweekdays, $args, $name="reccurweekdays" ){

		// get array
		$day_name = JEVHelper::getWeekdayLetter(null, 1);
		$day_name[0] = '<span class="sunday">' .   $day_name[0] . '</span>';
		$day_name[6] = '<span class="saturday">' . $day_name[6] . '</span>';
		
		$tosend = '';

		if( $reccurweekdays === '' ){
			$split 		= array();
			$countsplit = 0;
		}else{
			$split 		= explode( '|', $reccurweekdays );
			$countsplit = count( $split );
		}

		for( $a=0; $a<7; $a++ ){
			$checked = '';
			for( $x = 0; $x < $countsplit; $x++ ){
				if( $split[$x] == $a ){
					$checked = ' checked="checked"';
				}
			}
			$tosend .= '<span  class="r'.($a%2+1).'" ><input type="checkbox" id="cb_wd' . $a . '" name="'.$name.'[]" value="'
			. $a . '" ' . $args . $checked . ' onclick="updateRepeatWarning();" />&nbsp;' . "\n"
			. '<label for="cb_wd' . $a . '">'
			. $day_name[$a] . '</label></span>' . "\n"
			;
		}
		echo $tosend;
	}

	function buildWeeksCheck( $reccurweeks, $args , $name="reccurweeks"){
		$week_name = array( '',
		JText::_('JEV_REP_WEEK') . ' 1 ',
		JText::_('JEV_REP_WEEK') . ' 2 ',
		JText::_('JEV_REP_WEEK') . ' 3 ',
		JText::_('JEV_REP_WEEK') . ' 4 ',
		JText::_('JEV_REP_WEEK') . ' 5 '
		);
		$tosend		= '';
		$checked	= '';

		if( $reccurweeks == '' ){
			$split		= array();
			$countsplit = 0;
		}else{
			$split		= explode( '|', $reccurweeks );
			$countsplit = count( $split );
		}

		for( $a=1; $a<6; $a++ ){
			$checked = '';
			if( $reccurweeks == '' ){
				$checked = ' checked="checked"';
			}

			for ($x = 0; $x < $countsplit; $x++) {
				if ($split[$x] == $a) {
					$checked = ' checked="checked"';
				}
			}

			$tosend .= '<span  class="r'.($a%2+1).'" ><input type="checkbox" id="cb_wn' . $a . '" name="'.$name.'[]" value="'
			. $a . '" ' . $args . $checked . '  onclick="updateRepeatWarning();" />&nbsp;' . "\n"
			. '<label for="cb_wn' . $a . '">'
			. $week_name[$a] . '</label></span>' . "\n"
			;
		}
		echo $tosend;
	}

	function getUserMailtoLink( $evid, $userid , $admin=false){

		$db	=& JFactory::getDBO();

		static $arr_userids;
		static $arr_evids;

		$cfg = & JEVConfig::getInstance();

		if (!$arr_userids) {
			$arr_userids = array();
		}
		if (!$arr_evids) {
			$arr_evids = array();
		}


		$agenda_viewmail = $cfg->get('com_mailview');
		$agenda_viewmail |= $admin;

		if( $userid ){
			if (!isset($arr_userids[$userid])) {
				$userdet = JEVHelper::getContact($userid);

				$contactlink = "";
				if( $userdet ){
					if( isset($userdet->slug) && $userdet->slug  &&  $agenda_viewmail == '1' ){
						$contactlink = JRoute::_('index.php?option=com_contact&view=contact&id='.$userdet->slug.'&catid='.$userdet->catslug);
						$contactlink = '<a href="' . $contactlink. '" title="' . JText::_('JEV_EMAIL_TO_AUTHOR') . '">'. $userdet->contactname . '</a>';
					}
					else if(  $userdet->email && $agenda_viewmail == '1' ){
						//$contactlink = '<a href="mailto:' . $userdet->email
						//. '" title="' . JText::_('JEV_EMAIL_TO_AUTHOR') . '">'
						//. $userdet->username . '</a>';
						$contactlink = JHTML::_('email.cloak',$userdet->email, 1, $userdet->username, 0);
					}else{
						$contactlink = $userdet->username;
					}
				}
				$arr_userids[$userid] = $contactlink;
			}
			return $arr_userids[$userid];
		}else{
			if (!isset($arr_evids[$evid])) {
				$querym = "SELECT created_by_alias"
				. "\n FROM #__events"
				. "\n WHERE id='$evid'"
				;
				$db->setQuery($querym);
				$userdet = $db->loadResult();

				if( $userdet ){
					$contactlink = $userdet;
				}else{
					$contactlink = JText::_('JEV_ANONYME');
				}
				$arr_evids[$evid] = $contactlink;
			}
			return $arr_evids[$evid];
		}

		return '?';
	}

	/**
	 * returns name of the day longversion
	 * @param	daynb		int		# of day
	 * @param	colored		bool	color sunday	[ new mic, because inside tooltips a color forces an error! ]
	 **/
	function getDayName( $daynb, $colored = false ){

		$i = $daynb % 7; // modulo 7
		if( $i == '0' && $colored === true){
			$dayname = '<span class="sunday">' . JEVHelper::getDayName($i) . '</span>';
		}
		else if( $i == '6' && $colored === true){
			$dayname = '<span class="saturday">' . JEVHelper::getDayName($i) . '</span>';
		}
		else {
			$dayname = JEVHelper::getDayName($i);
		}
		return $dayname;
	}

	function getColorBar( $event_id=null, $newcolor ){
		$db	=& JFactory::getDBO();

		$cfg = & JEVConfig::getInstance();

		if( $event_id != null ){
			$query = "SELECT color_bar"
			. "\n FROM #__events"
			. "\n WHERE id = '$event_id'"
			;
			$db->setQuery( $query );
			$rows = $db->loadResultList();

			$row = $rows[0];

			if( $newcolor ){
				if( $newcolor <> $row->color_bar ){
					$query = "UPDATE #__events"
					. "\n SET color_bar = '$newcolor'"
					. "\n WHERE id = '$event_id'"
					;
					$db->setQuery( $query );

					return $newcolor;
				}
			}else{
				return $row->color_bar;
			}
		}else{
			// dmcd May 20/04  check the new config parameter to see what the default
			// color should be
			switch( $cfg->get('com_defColor')){
				case 'none':
					return '';

				case 'category':
					// fetch the category color for this event?
					// Note this won't work for a new event since
					// the user can change the category on-the-fly
					// in the event entry form.  We need to dump a
					// javascript array of all the category colors
					// into the event form so the color can track the
					// chosen category.
					return '';

				case 'random':
				default:
					$event_id = rand( 1, 50 );
					// BAR COLOR GENERATION
					//$start_publish = mktime (0, 0, 0, date("m"),date("d"),date("Y"));

					//$colorgenerate = intval(($start_publish/$event_id));
					//$bg1color = substr($colorgenerate, 5, 1);
					//$bg2color = substr($colorgenerate, 3, 1);
					//$bg3color = substr($colorgenerate, 7, 1);
					$bg1color = rand( 0, 9 );
					$bg2color = rand( 0, 9 );
					$bg3color = rand( 0, 9 );
					$newcolorgen = '#' . $bg1color . 'F' . $bg2color . 'F' . $bg3color . 'F';

					return $newcolorgen;
			}
		}
	}

	/************** Date format ******************
	*       case "0":
	*            // Fr style : Monday 23 Juillet 2003
	*            // Us style : Monday, Juillet 23 2003
	*       case "1":
	*            // Fr style : 23 Juillet 2003
	*            // Us style : Juillet 23, 2003
	*       case "2":
	*    	 // Fr style : 23 Juillet
	*            // Us style : Juillet, 23
	*       case "3":
	*    	 // Fr style : Juillet 2003
	*            // Us style : Juillet 2003
	*       case "4":
	*            // Fr style : 23/07/2003
	*            // Us style : 07/23/2003
	*       case "5":
	*            // Fr style : 23/07
	*            // Us style : 07/23
	*       case "6":
	*            // Fr style : 07/2003
	*            // Us style : 07/2003
	********************************************/
	function getDateFormat( $year, $month, $day, $type ){
		// Transform to translation strings
		if( empty( $year )){
			$year = 0;
		}

		if( empty( $month )){
			$month = 0;
		}

		if( empty( $day )){
			$day = 1;
		}

		static $format_type;
		if (!isset($format_type)) {
			$cfg = & JEVConfig::getInstance();
			$format_type	= $cfg->get('com_dateformat');
		}
		$datestp		= ( mktime( 0, 0, 0, $month, $day, $year ));

		// if date format is from langauge file then do this first
		if( $format_type == 3 ){
			return JEV_CommonFunctions::jev_strftime(JText::_("DATE_FORMAT_".$type),$datestp);
		}
		
		switch( $type ){
			case '0':
				if( $format_type == 0 ){
					return JEV_CommonFunctions::jev_strftime("%A, %d %B %Y",$datestp);
					// Fr style : Monday 03 Juillet 2003
				}elseif( $format_type == 1 ){
					return JEV_CommonFunctions::jev_strftime("%A, %B %d, %Y",$datestp);
					// Us style : Monday, July 03, 2003
				}else{
					//return strftime("%A, %e. %B %Y",$datestp);
					// %e not supported by windows
					return sprintf(JEV_CommonFunctions::jev_strftime("%A, %%s. %B %Y",$datestp), intval(JEV_CommonFunctions::jev_strftime('%d', $datestp)));
					// De style : Montag, 3. Juli 2003
				}
				break;

			case '1':
				if( $format_type == 0 ){
					return JEV_CommonFunctions::jev_strftime("%d %B %Y",$datestp);
					// Fr style : 23 Juillet 2003
				}elseif( $format_type == 1 ){
					return JEV_CommonFunctions::jev_strftime("%B %d, %Y",$datestp);
					// Us style : July 23, 2003
				}else{
					return JEV_CommonFunctions::jev_strftime("%d. %B %Y",$datestp);
					// De style : 23. Juli 2003
				}
				break;

			case '2':
				if( $format_type == 0 ){
					return JEV_CommonFunctions::jev_strftime("%d %B",$datestp);
					// Fr style : 23 Juillet
				}elseif( $format_type == 1 ){
					return JEV_CommonFunctions::jev_strftime("%B %d",$datestp);
					// Us style : Juillet 23
				}else{
					return JEV_CommonFunctions::jev_strftime("%d. %B",$datestp);
					// De style : 23. Juli
				}
				break;

			case '3':
				if( $format_type == 0 ){
					return JEV_CommonFunctions::jev_strftime("%B %Y",$datestp);
					// Fr style : Juillet 2003
				}elseif( $format_type == 1 ){
					return JEV_CommonFunctions::jev_strftime("%B, %Y",$datestp);
					// Us style : Juillet, 2003
				}else{
					return JEV_CommonFunctions::jev_strftime("%B %Y",$datestp);
					// De style : Juli 2003
				}
				break;

			case '4':
				if( $format_type == 0 ){
					return JEV_CommonFunctions::jev_strftime("%d/%m/%Y",$datestp);
					// Fr style : 23/07/2003
				}elseif( $format_type == 1){
					return JEV_CommonFunctions::jev_strftime("%m/%d/%Y",$datestp);
					// Us style : 07/23/2003
				}else{
					return JEV_CommonFunctions::jev_strftime("%d.%m.%Y",$datestp);
					// De style : 23.07.2003
				}
				break;

			case '5':
				if( $format_type == 0 ){
					return JEV_CommonFunctions::jev_strftime("%d/%m",$datestp);
					// Fr style : 23/07
				}elseif( $format_type == 1 ){
					return JEV_CommonFunctions::jev_strftime("%m/%d",$datestp);
					// Us style : 07/23
				}else{
					return JEV_CommonFunctions::jev_strftime("%d.%m.",$datestp);
					// De style : 23.07.
				}
				break;

			case '6':
				if( $format_type == 0 ){
					return JEV_CommonFunctions::jev_strftime("%m/%Y",$datestp);
					// Fr style : 07/2003
				}elseif( $format_type == 1 ){
					return JEV_CommonFunctions::jev_strftime("%m/%Y",$datestp);
					// Us style : 07/2003
				}else{
					return JEV_CommonFunctions::jev_strftime("%m/%Y",$datestp);
					// De style : 07/2003
				}
				break;

			case '7':
				if( $format_type == 0 ){
					return JEV_CommonFunctions::jev_strftime("%A, %d",$datestp);
					// Fr style : Monday 23
				}elseif( $format_type == 1 ){
					return JEV_CommonFunctions::jev_strftime("%A, %d",$datestp);
					// Us style : Monday, 23
				}else{
					return JEV_CommonFunctions::jev_strftime("%A, %d.",$datestp);
					// De style : Montag, 23.
				}
				break;

			default:
				break;
		}
		return $newdate;
	}

	/**
	* Convert special characters to html entities
	* Required for edit fields containing html code
	*
	* @static
	* @param $html	string	html text
	* @return		string	html string
	*/
	function special ( $html='' ) {

		return htmlspecialchars( $html, ENT_QUOTES, 'UTF-8');
	}

	/**
	* Generate javascript start and end tags
	*
	* @access public
	* @param string $type 'start' or 'end' tag
	* @return string html sequence
	*/
	function buildScriptTag ( $type='start' ) {

		$html = "";
		switch ($type) {

			case 'start':
				$html = "\n" . '<script type="text/javascript">' . "\n"
						. "/* <![CDATA[ */\n"
						. "// inserted by JEvents\n";
				break;
			case 'end':
				$html = "\n".'/* ]]> */'."\n"
						. '</script>'."\n";
				break;
			default;
				$html = '<!-- wrong javascript tag parameter-->'."\n";
		}
		return $html;
	}
	
}