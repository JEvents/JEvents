<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultLoadedFromTemplate($view,$template_name, $event, $mask){

	$db = JFactory::getDBO();
	// find published template
	static $template;
	if (!isset($template)){
		$db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= ".$db->Quote($template_name));
		$template = $db->loadObject();
	}

	if (is_null($template) || $template->value=="")  return false;
	// now replace the fields
	$search = array();
	$replace = array();

	$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);

	// Built in fields
	$search[]="{{TITLE}}";$replace[]=$event->title();

	// Title link
	global $Itemid;
	$rowlink = $event->viewDetailLink($event->yup(),$event->mup(),$event->dup(),false);
	$rowlink = JRoute::_($rowlink.$view->datamodel->getCatidsOutLink());
	ob_start();
	?>
	<a class="ev_link_row" href="<?php echo $rowlink; ?>" style="font-weight:bold;" title="<?php echo JEventsHTML::special($event->title()) ;?>"><?php echo $event->title() ;?></a>
	<?php

	$search[]="{{TITLE_LINK}}";$replace[]=ob_get_clean();

	$search[]="{{TRUNCATED_DESC:.*}}";$replace[]=$event->content();
	//	$search[]="|{{TRUNCATED_DESC:(.*)}}|";$replace[]=$event->content();
	$search[]="{{DESCRIPTION}}";$replace[]=$event->content();

	$search[]="{{CATEGORY}}";$replace[]=$event->catname();

	$document = JFactory::getDocument();
	$document->addStyleDeclaration("div.jevdialogs {position:relative;margin-top:35px;text-align:left;}\n div.jevdialogs img{float:none!important;margin:0px}");

	if ($jevparams->get("showicalicon",0) &&  !$jevparams->get("disableicalexport",0) ){
		JHTML::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
		$cssloaded = true;
		ob_start();
		?>
		<a href="javascript:void(0)" onclick='clickIcalButton()' title="<?php echo JText::_('JEV_SAVEICAL');?>">
			<img src="<?php echo JURI::root().'administrator/components/'.JEV_COM_COMPONENT.'/assets/images/jevents_event_sml.png'?>" align="middle" name="image"  alt="<?php echo JText::_('JEV_SAVEICAL');?>" style="height:24px;"/>
		</a>
        <div class="jevdialogs">
        <?php
        $view->eventIcalDialog($event, $mask);
        ?>
        </div>
		
		<?php
		$search[]="{{ICALBUTTON}}";$replace[]=ob_get_clean();
	}
	else {
		$search[]="{{ICALBUTTON}}";$replace[]="";
	}

	if( $event->canUserEdit() && !( $mask & MASK_POPUP )) {
		JHTML::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );

		ob_start();
    	?>
        <a href="javascript:void(0)" onclick='clickEditButton()' title="<?php echo JText::_('JEV_E_EDIT');?>">
        	<?php echo JHTML::_('image.site', 'edit.png', '/images/M_images/', NULL, NULL, JText::_('JEV_E_EDIT'));?>
        </a>
        <div class="jevdialogs">
        <?php
        $view->eventManagementDialog($event, $mask);
        ?>
        </div>
        
        <?php
        $search[]="{{EDITBUTTON}}";;$replace[]=ob_get_clean();
	}
	else {
		$search[]="{{EDITBUTTON}}";$replace[]="";
	}

	if ($template_name=="icalevent.detail_body"){
		$search[]="{{REPEATSUMMARY}}";$replace[]=$event->repeatSummary();
	}
	else {
		// these would slow things down if not needed in the list
		static $dorepeatsummary;
		if (!isset($dorepeatsummary)){
			$dorepeatsummary = (strpos($template->value,":REPEATSUMMARY}}")!==false);
		}
		if ($dorepeatsummary){

			// I would this in case we do a full repeat summary
			/*
			$row->start_date = JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 0 );
			$row->start_time = JEVHelper::getTime($row->getUnixStartTime() );
			$row->stop_date = JEventsHTML::getDateFormat( $row->ydn(), $row->mdn(), $row->ddn(), 0 );
			$row->stop_time = JEVHelper::getTime($row->getUnixEndTime() );
			*/

			$cfg = & JEVConfig::getInstance();
			$jevtask  = JRequest::getString("jevtask");
			$jevtask = str_replace(".listevents","",$jevtask);

			$showyeardate = $cfg->get("showyeardate",0);

			$row = $event;
			$times = "";
			if (($showyeardate && $jevtask=="year") || $jevtask=="search.results" || $jevtask=="cat"){

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
			$search[]="{{REPEATSUMMARY}}";$replace[]=$times;
		}
	}

	static $doprevnext;
	if (!isset($doprevnext)){
		$doprevnext = (strpos($template->value,":PREVIOUSNEXT}}")!==false);
	}
	if ($doprevnext){
		$search[]="{{PREVIOUSNEXT}}";$replace[]=$event->previousnextLinks();
	}
	$search[]="{{CREATOR_LABEL}}";$replace[]=JText::_('JEV_BY');
	$search[]="{{CREATOR}}";$replace[]=$event->contactlink();

	$search[]="{{HITS}}";$replace[]=JText::_('JEV_EVENT_HITS') . ' : ' . $event->hits();

	if ($event->hasLocation()){
		$search[]="{{LOCATION_LABEL}}";$replace[]=JText::_('JEV_EVENT_ADRESSE')."&nbsp;";
		$search[]="{{LOCATION}}";$replace[]=$event->location();
	}
	else {
		$search[]="{{LOCATION_LABEL}}";$replace[]="";
		$search[]="{{LOCATION}}";$replace[]="";
	}

	if ($event->hasContactInfo()){
		$search[]="{{CONTACT_LABEL}}";$replace[]=JText::_('JEV_EVENT_CONTACT')."&nbsp;";
		$search[]="{{CONTACT}}";$replace[]=$event->contact_info();
	}
	else {
		$search[]="{{CONTACT_LABEL}}";$replace[]="";
		$search[]="{{CONTACT}}";$replace[]="";
	}

	$search[]="{{EXTRAINFO}}";$replace[]=$event->extra_info();

	// Now do the plugins
	// get list of enabled plugins

	$layout = $template_name=="icalevent.list_row"?"list":"detail";

	$jevplugins = JPluginHelper::getPlugin("jevents");
	foreach ($jevplugins as $jevplugin){
		$classname = "plgJevents".ucfirst($jevplugin->name);
		if (is_callable(array($classname,"substitutefield"))){
			$fieldNameArray = call_user_func(array($classname,"fieldNameArray"),$layout);
			if (isset($fieldNameArray["values"])) {
				foreach ($fieldNameArray["values"] as $fieldname) {
					$search[]="{{".$fieldname."}}";
					$replace[]=call_user_func(array($classname,"substitutefield"),$event,$fieldname);
				}
			}
		}
	}

	// non greedy replacement - because of the ?
	$template_value = preg_replace_callback('|{{.*?}}|', 'cleanLabels', $template->value);

	// word counts etc.
	for ($s=0;$s<count($search);$s++){
		if (strpos($search[$s],"TRUNCATED_DESC:")>0){
			global $tempreplace, $tempevent, $tempsearch;
			$tempreplace = $replace[$s];
			$tempsearch = $search[$s];
			$tempevent = $event;
			$template_value = preg_replace_callback("|$tempsearch|", 'jevSpecialHandling', $template_value);
		}
	}

	$template_value =  str_replace($search,$replace,$template_value);
	echo $template_value;
	return true;
}

function cleanLabels($matches){
	if (count($matches)==1){
		$parts = explode(":",$matches[0]);
		if (count($parts)>0) {
			if (strpos($matches[0],"://")>0){
				return "{{".$parts[count($parts)-1];
			}
			array_shift($parts);
			return "{{".implode(":",$parts);
		}
		return "";
	}
	return "";
}

function jevSpecialHandling($matches){
	if (count($matches)==1 && strpos($matches[0],":")>0){
		global $tempreplace, $tempevent, $tempsearch;
		$parts = explode(":",$matches[0]);
		if (count($parts)==2){
			$wordcount = intval(str_replace("}}","",$parts[1]));
			$value = strip_tags($tempreplace);
			
			$value = str_replace("  "," ",$value);
			$words = explode(" ",$value);
			if (count($words)>$wordcount) {
				$words = array_slice($words,0,$wordcount);
				$words[]=" ...";
			}
			return implode(" ",$words);
		}
		else {
			return $matches[0];
		}
	}
	else if (count($matches)==1) return $matches[0];
}