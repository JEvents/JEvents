<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the module  frontend
 *
 * @static
 */
include_once(JPATH_SITE."/modules/mod_jevents_latest/tmpl/default/latest.php");

class ExtModLatestView extends DefaultModLatestView  
{
	function displayLatestEvents(){

		// this will get the viewname based on which classes have been implemented
		$viewname = $this->getTheme();

		$cfg = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$viewpath = "components/".JEV_COM_COMPONENT."/views/".$viewname."/assets/css/";
		
		$dispatcher	= JDispatcher::getInstance();
		$datenow	= JEVHelper::getNow();

		$this->getLatestEventsData();

		$content = "";

		if(isset($this->eventsByRelDay) && count($this->eventsByRelDay)){
			$content .= '<table class="mod_events_latest_table" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';

			// Now to display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up.

			$firstTime=true;

			// initialize name of com_jevents module and task defined to view
			// event detail.  Note that these could change in future com_event
			// component revisions!!  Note that the '$this->itemId' can be left out in
			// the link parameters for event details below since the event.php
			// component handler will fetch its own id from the db menu table
			// anyways as far as I understand it.

			$this->processFormatString();

			foreach($this->eventsByRelDay as $relDay => $daysEvents){

				reset($daysEvents);

				// get all of the events for this day
				foreach($daysEvents as $dayEvent){

					if($firstTime) $content .= '<tr><td class="mod_events_latest_first">';
					else $content .= '<tr><td class="mod_events_latest">';

					// generate output according custom string
					foreach($this->splitCustomFormat as $condtoken) {

						if (isset($condtoken['cond'])) {
							if ( $condtoken['cond'] == 'a'  && !$dayEvent->alldayevent()) continue;
							else if ( $condtoken['cond'] == '!a' &&  $dayEvent->alldayevent()) continue;
							else if ( $condtoken['cond'] == 'e'  && !($dayEvent->noendtime() || $dayEvent->alldayevent())) continue;
							else if ( $condtoken['cond'] == '!e' &&  ($dayEvent->noendtime() || $dayEvent->alldayevent())) continue;							
							else if ( $condtoken['cond'] == '!m' &&  $dayEvent->getUnixStartDate()!=$dayEvent->getUnixEndDate() ) continue;
							else if ( $condtoken['cond'] == 'm' &&  $dayEvent->getUnixStartDate()==$dayEvent->getUnixEndDate() ) continue;
						}
						foreach($condtoken['data'] as $token) {
							unset($match);
							unset($dateParm);
							$dateParm="";
							$match='';
							if (is_array($token)) {
								$match = $token['keyword'];
								$dateParm = isset($token['dateParm']) ? trim($token['dateParm']) : "";
							}
							else if (strpos($token,'${')!==false){
								$match = $token;
							}
							else {
								$content .= $token;
								continue;
							}

							$this->processMatch($content, $match, $dayEvent, $dateParm,$relDay);
						} // end of foreach
					} // end of foreach
					$content .= "</td></tr>\n";
					$firstTime=false;
				} // end of foreach
			} // end of foreach
			$content .="</table>\n";

		}
		else if ($this->modparams->get("modlatest_NoEvents", 1)){
			$content .= '<table class="mod_events_latest_table" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';
			$content .= '<tr><td class="mod_events_latest_noevents">'. JText::_('JEV_NO_EVENTS') . '</td></tr>' . "\n";
			$content .="</table>\n";
		}

		$callink_HTML = '<div class="mod_events_latest_callink">'
		.$this->getCalendarLink()
		. '</div>';

		if ($this->linkToCal == 1) $content = $callink_HTML . $content;
		if ($this->linkToCal == 2) $content .= $callink_HTML;

		if ($this->displayRSS){
			$rssimg = JURI::root() . "media/system/images/livemarks.png";
			
			$callink_HTML = '<div class="mod_events_latest_rsslink">'
			.'<a href="'.$this->rsslink.'" title="'.JText::_("RSS_FEED").'"  target="_blank">'
			.'<img src="'.$rssimg.'" alt="'.JText::_("RSS_FEED").'" />'
			.JText::_("SUBSCRIBE_TO_RSS_FEED")
			. '</a>'
			. '</div>';
			$content .= $callink_HTML;
		}

		if ($this->modparams->get("contentplugins", 0)){
			$dispatcher = JDispatcher::getInstance();
			$eventdata = new stdClass();
			//$eventdata->text = str_replace("{/toggle","{/toggle}",$content);
			$eventdata->text = $content;
			$dispatcher->trigger('onContentPrepare', array('com_jevents', &$eventdata, &$this->modparams, 0));
			 $content = $eventdata->text;
		}

		return $content;
	} // end of function
} // end of class
