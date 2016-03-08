<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultViewEventRowNew($view,$row,$args="") {

	$cfg = JEVConfig::getInstance();
	$jinput = JFactory::getApplication()->input;
	$rowlink = $row->viewDetailLink($row->yup(),$row->mup(),$row->dup(),false);
	$rowlink = JRoute::_($rowlink.$view->datamodel->getCatidsOutLink());

	// I choost not to use $row->fgcolor
	$fgcolor="inherit";
	$tmpTitle = $row->title();

	/*
	// [mic] if title is too long, cut 'em for display
	if( JString::strlen( $row->title() ) >= 50 ){
		$tmpTitle = JString::substr( $row->title(), 0, 50 ) . ' ...';
	}
	*/

	$jevtask  = $jinput->getString("jevtask");
	$jevtask = str_replace(".listevents","",$jevtask);

	$showyeardate = $cfg->get("showyeardate",0);


	$times = "";
	if (($showyeardate && $jevtask=="year") || $jevtask=="search.results" || $jevtask=="cat"  || $jevtask=="range"){

		$start_publish  = $row->getUnixStartDate();
		$stop_publish  = $row->getUnixEndDate();

		$start_date	= JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 0 );
		$start_time = JEVHelper::getTime($row->getUnixStartTime(),$row->hup(),$row->minup());

		$stop_date	= JEventsHTML::getDateFormat(  $row->ydn(), $row->mdn(), $row->ddn(), 0 );
		$stop_time	= JEVHelper::getTime($row->getUnixEndTime(),$row->hdn(),$row->mindn());

		if( $stop_publish == $start_publish ){
			if ($row->noendtime()){
				$times = $start_time;
			}
			else if ($row->alldayevent()){
				$times = "";
			}
			else if($start_time != $stop_time ){
				$times = $start_time . ' - ' . $stop_time;
			}
			else {
				$times = $start_time;
			}

			$times = $start_date." ". $times."<br/>";
		} else {
			if ($row->noendtime()){
				$times = $start_time;
			}
			else if($start_time != $stop_time && !$row->alldayevent()){
				$times = $start_time . '&nbsp;-&nbsp;' . $stop_time;
			}
			$times =$start_date . ' - '	. $stop_date." ". $times."<br/>";
		}
	}
	else if (($jevtask=="day" || $jevtask=="week" )  && ($row->starttime() != $row->endtime()) && !($row->alldayevent())){
		$starttime = JEVHelper::getTime($row->getUnixStartTime(),$row->hup(),$row->minup());
		$endtime	= JEVHelper::getTime($row->getUnixEndTime(),$row->hdn(),$row->mindn());
		
		if ($row->noendtime()){
			if ($showyeardate && $jevtask=="year"){
				$times = $starttime. '&nbsp;-&nbsp;' . $endtime . '&nbsp;';
			}
			else {
				$times = $starttime. '&nbsp;';
			}
		}
		else {
			$times = $starttime. '&nbsp;-&nbsp;' . $endtime . '&nbsp;';
		}
	}

	echo $times;
		?>
			<a class="ev_link_row" href="<?php echo $rowlink; ?>" <?php echo $args;?> style="color:<?php echo $fgcolor;?>;" title="<?php echo JEventsHTML::special($row->title()) ;?>"><?php echo $tmpTitle ;?></a>
			<?php
			if( $cfg->get('com_byview') == '1' ) {
				echo JText::_('JEV_BY') . '&nbsp;<i>'. $row->contactlink() .'</i>';
			}
			?>
		<?php
}