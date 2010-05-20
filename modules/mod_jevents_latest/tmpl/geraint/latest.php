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
include_once(dirname(__FILE__)."/../default/latest.php");

class GeraintModLatestView extends DefaultModLatestView  
{
	
	function displayLatestEvents(){

		$cfg = & JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;
	
		global $mainframe;
		$dispatcher	=& JDispatcher::getInstance();
		$datenow	= JEVHelper::getNow();

		$this->getLatestEventsData();

		$content = "";
		$content .= '<table class="mod_events_latest_table" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';

		if(isset($this->eventsByRelDay) && count($this->eventsByRelDay)){

			// Now to display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up.

			$firstTime=true;

			// initialize name of com_jevents module and task defined to view
			// event detail.  Note that these could change in future com_event
			// component revisions!!  Note that the '$this->itemId' can be left out in
			// the link parameters for event details below since the event.php
			// component handler will fetch its own id from the db menu table
			// anyways as far as I understand it.

			$task_events = 'icalrepeat.detail';

			$this->processFormatString();

			foreach($this->eventsByRelDay as $relDay => $daysEvents){

				reset($daysEvents);

				// get all of the events for this day
				foreach($daysEvents as $dayEvent){
					// get the title and start time
					$startDate	= strtotime($dayEvent->publish_up());
					if ($relDay>0){
						$eventDate	= strtotime($datenow->toFormat('%Y-%m-%d ').strftime('%H:%M', $startDate)." +$relDay days");
					}
					else {
						$eventDate	= strtotime($datenow->toFormat('%Y-%m-%d ').strftime('%H:%M', $startDate)." $relDay days");
					}
					$endDate	= strtotime($dayEvent->publish_down());

					list($st_year, $st_month, $st_day) = explode('-', strftime('%Y-%m-%d', $startDate));
					list($ev_year, $ev_month, $ev_day) = explode('-', strftime('%Y-%m-%d', $startDate));

					$dst = "border-color:".$dayEvent->bgcolor();
					if($firstTime) $content .= '<tr><td class="mod_events_latest_first" style="'.$dst.'">';
					else $content .= '<tr><td class="mod_events_latest" style="'.$dst.'">';

					// generate output according custom string
					foreach($this->splitCustomFormat as $condtoken) {

						if (isset($condtoken['cond'])) {
							if ( $condtoken['cond'] == 'a'  && !$dayEvent->alldayevent()) continue;
							if ( $condtoken['cond'] == '!a' &&  $dayEvent->alldayevent()) continue;
						}
						foreach($condtoken['data'] as $token) {
							unset($match);
							unset($dateParm);
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

							switch ($match){

								case 'endDate':
								case 'startDate':
								case 'eventDate':
									// Note we need to examine the date specifiers used to determine if language translation will be
									// necessary.  Do this later when script is debugged.

									if(!$this->disableDateStyle) $content .= '<span class="mod_events_latest_date">';

									if (!$dayEvent->alldayevent() && $match=="endDate" && ($dayEvent->noendtime() || $dayEvent->getUnixStartTime()==$dayEvent->getUnixEndTime())){
										$time_fmt = "";
									}
									else if (!isset($dateParm) || $dateParm == ''){
											if ($this->com_calUseStdTime) {
												$time_fmt = $dayEvent->alldayevent() ? '' : ' @%l:%M%p';
											} else {
												$time_fmt = $dayEvent->alldayevent() ? '' : ' @%H:%M';
											}
											$dateFormat = $this->displayYear ? '%a %b %d, %Y'.$time_fmt : '%a %b %d'.$time_fmt;
											$jmatch = new JDate($$match);
											$content .= $jmatch->toFormat($dateFormat);
											//$content .= JEV_CommonFunctions::jev_strftime($dateFormat, $$match);
									} else {
										// if a '%' sign detected in date format string, we assume strftime() is to be used,
										if(preg_match("/\%/", $dateParm)) {
											$jmatch = new JDate($$match);
											$content .= $jmatch->toFormat($dateParm);
										}
										// otherwise the date() function is assumed.
										else $content .= date($dateParm, $$match);
									}
									if(!$this->disableDateStyle) $content .= "</span>";
									break;

								case 'title':

									if (!$this->disableTitleStyle) $content .= '<span class="mod_events_latest_content">';
									if ($this->displayLinks) {

										$link = $dayEvent->viewDetailLink($ev_year,$ev_month,$ev_day,false,$this->myItemid);
										$link = JRoute::_($link.$this->datamodel->getCatidsOutLink());

										$content .= $this->_htmlLinkCloaking($link,JEventsHTML::special($dayEvent->title()));
										/*
										"index.php?option=".$compname
										. "&task="  . $task_events
										. "&agid="  . $dayEvent->id()
										. "&year="  . date("Y", $eventDate)
										. "&month=" . date("m", $eventDate)
										. "&day=" 	. date("d", $eventDate)
										. "&Itemid=". $this->myItemid . $this->catout, $dayEvent->title());
										*/
									} else {
										$content .= JEventsHTML::special($dayEvent->title());
									}
									if (!$this->disableTitleStyle) $content .= '</span>';
									break;

								case 'category':
									$catobj   = $dayEvent->getCategoryName();
									$content .= JEventsHTML::special($catobj);
									break;

								case 'contact':
									// Also want to cloak contact details so
									$this->modparams->set("image",1);
									$dayEvent->text = $dayEvent->contact_info();
									$dispatcher->trigger( 'onPrepareContent', array( &$dayEvent, &$this->modparams, 0 ), true );
									$dayEvent->contact_info($dayEvent->text);
									$content .= $dayEvent->contact_info();
									break;

								case 'content':  // Added by Kaz McCoy 1-10-2004
								$this->modparams->set("image",1);
								$dayEvent->data->text = $dayEvent->content();
								$results = $dispatcher->trigger( 'onPrepareContent', array( &$dayEvent->data, &$this->modparams, 0 ), true );
								$dayEvent->content($dayEvent->data->text);
								//$content .= substr($dayEvent->content, 0, 150);
								$content .= $dayEvent->content();
								break;

								case 'addressInfo':
								case 'location':
									$this->modparams->set("image",0);
									$dayEvent->data->text = $dayEvent->location();
									$results = $dispatcher->trigger( 'onPrepareContent', array( &$dayEvent->data, &$this->modparams, 0 ), true );
									$dayEvent->location($dayEvent->data->text);
									$content .= $dayEvent->location();
									break;

								case 'extraInfo':
									$this->modparams->set("image",0);
									$dayEvent->data->text = $dayEvent->extra_info();
									$results = $dispatcher->trigger( 'onPrepareContent', array( &$dayEvent->data, &$this->modparams, 0 ), true );
									$dayEvent->extra_info($dayEvent->data->text);
									$content .= $dayEvent->extra_info();
									break;

								case 'countdown':
									$timedelta = $dayEvent->getUnixStartTime() - mktime() ;
									$fieldval = $dateParm;
									$shownsign = false;
									if (stripos($fieldval,"%d")!==false){
										$days = intval($timedelta /(60*60*24));
										$timedelta -= $days*60*60*24;
										$fieldval = str_ireplace("%d",$days,$fieldval);
										$shownsign = true;
									}
									if (stripos($fieldval,"%h")!==false){
										$hours = intval($timedelta /(60*60));
										$timedelta -= $hours*60*60;
										if ($shownsign) $hours = abs($hours);
										$hours = sprintf("%02d",$hours);
										$fieldval = str_ireplace("%h",$hours,$fieldval);
										$shownsign = true;
									}
									if (stripos($fieldval,"%m")!==false){
										$mins = intval($timedelta /60);
										$timedelta -= $hours*60;
										if ($mins) $mins = abs($mins);
										$mins = sprintf("%02d",$mins);
										$fieldval = str_ireplace("%m",$mins,$fieldval);
									}
									
									$content .= $fieldval;
									break;

								case 'createdByAlias':
									$content .= $dayEvent->created_by_alias();
									break;

								case 'createdByUserName':
									$catobj   = JEVHelper::getUser($dayEvent->created_by());
									$content .= isset($catobj->username)?$catobj->username:"";
									break;

								case 'createdByUserEmail':
									// Note that users email address will NOT be available if they don't want to receive email
									$catobj   = JEVHelper::getUser($dayEvent->created_by());
									$content .= $catobj->sendEmail ? $catobj->email : '';
									break;

								case 'createdByUserEmailLink':
									// Note that users email address will NOT be available if they don't want to receive email
									$content .= JRoute::_("index.php?option="
									. $compname
									. "&task=".$task_events
									. "&agid=".$dayEvent->id()
									. "&year=".$st_year
									. "&month=".$st_month
									. "&day=".$st_day
									. "&Itemid=".$this->myItemid . $this->catout);
									break;

								case 'color':
									$content .= $dayEvent->bgcolor();
									break;

								case 'eventDetailLink':
									$link = $dayEvent->viewDetailLink($st_year,$st_month,$st_day,false,$this->myItemid);
									$link = JRoute::_($link.$this->datamodel->getCatidsOutLink());
									$content .= $link;

									/*
									$content .= JRoute::_("index.php?option="
									. $compname
									. "&task=".$task_events
									. "&agid=".$dayEvent->id()
									. "&year=".$st_year
									. "&month=".$st_month
									. "&day=".$st_day
									. "&Itemid=".$this->myItemid . $this->catout);
									*/
									break;

								default:
									try {
										if (strpos($match,'${')!==false){
											$parts = explode('${',$match);
											$tempstr = "";
											foreach ($parts as $part){
												if (strpos($part,"}")!==false){
													
													$subparts = explode("}",$part);
													//$part = str_replace("}","",$part);
													$subpart = "_".$subparts[0];
													if (isset($dayEvent->$subpart)){
														$temp =  $dayEvent->$subpart;
														$tempstr .= $temp;
													}
													$tempstr .= $subparts[1];
												}
												else {
													$tempstr .= $part;
												}
											}
											$content .= $tempstr;
										}
										else if ($match) $content .= $match;

									}
									catch (Exception $e){
										if ($match) $content .= $match;
									}
									break;
							} // end of switch
						} // end of foreach
					} // end of foreach
					$content .= "</td></tr>\n";
					$firstTime=false;
				} // end of foreach
			} // end of foreach

		} else {
			$content .= '<tr><td class="mod_events_latest_noevents">'. JText::_('JEV_NO_EVENTS') . '</td></tr>' . "\n";
		}
		$content .="</table>\n";

		$callink_HTML = '<div class="mod_events_latest_callink">'
		.$this->getCalendarLink()
		. '</div>';

		if ($this->linkToCal == 1) $content = $callink_HTML . $content;
		if ($this->linkToCal == 2) $content .= $callink_HTML;

		if ($this->displayRSS){
			$rssimg = JURI::root() . "images/M_images/livemarks.png";

			$callink_HTML = '<div class="mod_events_latest_rsslink">'
			.'<a href="'.$this->rsslink.'" title="'.JText::_("RSS Feed").'" target="_blank">'
			.'<img src="'.$rssimg.'" alt="'.JText::_("RSS Feed").'" />'
			.JText::_("Subscribe to RSS Feed")
			. '</a>'
			. '</div>';
			$content .= $callink_HTML;
		}
		return $content;
	} // end of function
} // end of class
