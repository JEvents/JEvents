<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultViewHelperShowNavTableBar($view){
	// this, previous and next date handling
	
	$cfg = JEVConfig::getInstance();
	$jinput = JFactory::getApplication()->input;
	// Optionally display no nav bar
	if($cfg->get('com_calUseIconic', 1) == -1) return "";
	
	$t_datenow = JEVHelper::getNow();
	$datetime = JevDate::strftime( '%Y-%m-%d %H:%M:%S', $t_datenow->toUnix(true));
	preg_match( "#([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})#", $datetime, $regs );

	$this_date = new JEventDate();
	$this_date->setDate( $view->year, $view->month, $view->day );

	$today_date = clone($this_date);
	$today_date->setDate( $regs[1], $regs[2], $regs[3] );

	$task = $jinput->getString("jevtask");
	if ($task == ""){
		// I think xdebug messes up this variable in the memory so when debugging this is helpful
		$task = $jinput->getString("jevtask", $jinput->getString("task", $jinput->getString("view") . "." . $jinput->getString("layout")));
	}
	echo "<div class='jev_pretoolbar'>";	
	$view->loadModules("jevpretoolbar");
	$view->loadModules("jevpretoolbar_".$task);
	echo "</div>";
	$prev_year = clone($this_date);
	$prev_year->addMonths( -12 );
	$next_year = clone($this_date);
	$next_year->addMonths( +12 );

	$prev_month = clone($this_date);
	$prev_month->addMonths( -1 );
	$next_month = clone($this_date);
	$next_month->addMonths( +1 );

	$prev_week = clone($this_date);
	$prev_week->addDays( -7 );
	$next_week = clone($this_date);
	$next_week->addDays( +7 );

	$prev_day = clone($this_date);
	$prev_day->addDays( -1 );
	$next_day = clone($this_date);
	$next_day->addDays( +1 );

	switch( $task ){
		case 'year.listevents':
			$dates['prev2'] = $prev_year;
			$dates['prev1'] = $prev_year;
			$dates['next1'] = $next_year;
			$dates['next2'] = $next_year;

			$alts['prev2'] = JText::_('JEV_PREVIOUSYEAR');
			$alts['prev1'] = JText::_('JEV_PREVIOUSYEAR');
			$alts['next1'] = JText::_('JEV_NEXTYEAR');
			$alts['next2'] = JText::_('JEV_NEXTYEAR');

			// Show
			if($cfg->get('com_calUseIconic', 1) == 1 || $cfg->get('com_calUseIconic', 1) == 2) $view->viewNavTableBarIconic( $today_date, $this_date, $dates, $alts, JEV_COM_COMPONENT, $task, $view->Itemid );
			else  $view->viewNavTableBar( $today_date, $this_date, $dates, $alts, JEV_COM_COMPONENT, $task, $view->Itemid );
			break;

		case 'month.calendar':
			$dates['prev2'] = $prev_year;
			$dates['prev1'] = $prev_month;
			$dates['next1'] = $next_month;
			$dates['next2'] = $next_year;

			$alts['prev2'] = JText::_('JEV_PREVIOUSYEAR');
			$alts['prev1'] = JText::_('JEV_PREVIOUSMONTH');
			$alts['next1'] = JText::_('JEV_NEXTMONTH');
			$alts['next2'] = JText::_('JEV_NEXTYEAR');

			// Show
			if($cfg->get('com_calUseIconic', 1) == 1 || $cfg->get('com_calUseIconic', 1) == 2) $view->viewNavTableBarIconic( $today_date, $this_date, $dates, $alts, JEV_COM_COMPONENT, $task, $view->Itemid );
			else  $view->viewNavTableBar( $today_date, $this_date, $dates, $alts, JEV_COM_COMPONENT, $task, $view->Itemid );
			break;

		case 'week.listevents':
			$dates['prev2'] = $prev_month;
			$dates['prev1'] = $prev_week;
			$dates['next1'] = $next_week;
			$dates['next2'] = $next_month;

			$alts['prev2'] = JText::_('JEV_PREVIOUSMONTH');
			$alts['prev1'] = JText::_('JEV_PREVIOUSWEEK');
			$alts['next1'] = JText::_('JEV_NEXTWEEK');
			$alts['next2'] = JText::_('JEV_NEXTMONTH');

			// Show
			if($cfg->get('com_calUseIconic', 1) == 1 || $cfg->get('com_calUseIconic', 1) == 2) $view->viewNavTableBarIconic( $today_date, $this_date, $dates, $alts, JEV_COM_COMPONENT, $task, $view->Itemid );			
			else $view->viewNavTableBar( $today_date, $this_date, $dates, $alts, JEV_COM_COMPONENT, $task, $view->Itemid );
			break;

		case 'day.listevents':
		default:
			$dates['prev2'] = $prev_month;
			$dates['prev1'] = $prev_day;
			$dates['next1'] = $next_day;
			$dates['next2'] = $next_month;

			$alts['prev2'] = JText::_('JEV_PREVIOUSMONTH');
			$alts['prev1'] = JText::_('JEV_PREVIOUSDAY');
			$alts['next1'] = JText::_('JEV_NEXTDAY');
			$alts['next2'] = JText::_('JEV_NEXTMONTH');

			// Show
			if($cfg->get('com_calUseIconic', 1) == 1 || $cfg->get('com_calUseIconic', 1) == 2) $view->viewNavTableBarIconic( $today_date, $this_date, $dates, $alts, JEV_COM_COMPONENT, $task, $view->Itemid );
			else $view->viewNavTableBar( $today_date, $this_date, $dates, $alts, JEV_COM_COMPONENT, "day.listevents", $view->Itemid );
			break;
	}
	
		$view->loadModules("jevposttoolbar");
		$view->loadModules("jevposttoolbar_".$task);
	
}

