<?php
defined('_JEXEC') or die('Restricted access');

function DefaultLoadedFromTemplate($view, $template_name, $event, $mask)
{

	$db = JFactory::getDBO();
// find published template
	static $templates;
	if (!isset($templates))
	{
		$templates = array();
	}
	if (!array_key_exists($template_name, $templates))
	{
		$db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= " . $db->Quote($template_name));
		$templates[$template_name] = $db->loadObject();
	}

	if (is_null($templates[$template_name]) || $templates[$template_name]->value == "")
		return false;

	$template = $templates[$template_name];

	$template_value = $template->value;

// strip carriage returns other wise the preg replace doesn;y work - needed because wysiwyg editor may add the carriage return in the template field
	$template_value = str_replace("\r", '', $template_value);
	$template_value = str_replace("\n", '', $template_value);
// non greedy replacement - because of the ?
	$template_value = preg_replace_callback('|{{.*?}}|', 'cleanLabels', $template_value);

	$matchesarray = array();
	preg_match_all('|{{.*?}}|', $template_value, $matchesarray);

	if (count($matchesarray) == 0)
		return;

// now replace the fields
	$search = array();
	$replace = array();
	$blank = array();

	$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);

	//var_dump($matchesarray);

	for ($i = 0; $i < count($matchesarray[0]); $i++)
	{
		$strippedmatch = preg_replace('/(#|:)+[^}]*/', '', $matchesarray[0][$i]);

		if (in_array($strippedmatch, $search))
		{
			continue;
		}
// Built in fields	
		switch ($strippedmatch) {
			case "{{TITLE}}":
				$search[] = "{{TITLE}}";
				$replace[] = $event->title();
				$blank[] = "";
				break;

			case "{{LINK}}":
			case "{{LINKSTART}}":
			case "{{LINKEND}}":
			case "{{TITLE_LINK}}":
// Title link
				$rowlink = $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), false);
				$rowlink = JRoute::_($rowlink . $view->datamodel->getCatidsOutLink());
				ob_start();
				?>
				<a class="ev_link_row" href="<?php echo $rowlink; ?>" style="font-weight:bold;" title="<?php echo JEventsHTML::special($event->title()); ?>">
					<?php
					$linkstart = ob_get_clean();

					$search[] = "{{LINK}}";
					$replace[] = $rowlink;
					$blank[] = "";
					$search[] = "{{LINKSTART}}";
					$replace[] = $linkstart;
					$blank[] = "";
					$search[] = "{{LINKEND}}";
					$replace[] = "</a>";
					$blank[] = "";

					$fulllink = $linkstart . $event->title() . '</a>';
					$search[] = "{{TITLE_LINK}}";
					$replace[] = $fulllink;
					$blank[] = "";

					break;

				case "{{TRUNCTITLE}}":

					// for month calendar cell only
					if (isset($event->truncatedtitle))
					{
						$search[] = "{{TRUNCTITLE}}";
						$replace[] = $event->truncatedtitle;
						$blank[] = "";
					}
					else
					{
						$search[] = "{{TRUNCTITLE}}";
						$replace[] = $event->title();
						$blank[] = "";
					}

					break;

				case "{{URL}}":
					$search[] = "{{URL}}";
					$replace[] = $event->url();
					$blank[] = "";
					break;

				case "{{TRUNCATED_DESC}}":
					$search[] = "{{TRUNCATED_DESC:.*?}}";
					$replace[] = $event->content();
					$blank[] = "";
					//	$search[]="|{{TRUNCATED_DESC:(.*)}}|";$replace[]=$event->content();
					break;

				case "{{DESCRIPTION}}":
					$search[] = "{{DESCRIPTION}}";
					$replace[] = $event->content();
					$blank[] = "";
					break;

				case "{{MANAGEMENT}}":
					$search[] = "{{MANAGEMENT}}";
					ob_start();
					$view->_viewNavAdminPanel();
					$replace[] = ob_get_clean();
					$blank[] = "";
					break;

				case "{{CATEGORY}}":
					$search[] = "{{CATEGORY}}";
					$replace[] = $event->catname();
					$blank[] = "";
					break;

				case "{{CALENDAR}}":
					$search[] = "{{CALENDAR}}";
					$replace[] = $event->getCalendarName();
					$blank[] = "";
					break;
				
				case "{{COLOUR}}":
					$bgcolor = $event->bgcolor();
					$search[] = "{{COLOUR}}";
					$replace[] = $bgcolor == "" ? "#ffffff" : $bgcolor;
					$blank[] = "";
					break;

				case "{{FGCOLOUR}}":
					$search[] = "{{FGCOLOUR}}";
					$replace[] = $event->fgcolor();
					$blank[] = "";
					break;

				case "{{TTTIME}}":
					$search[] = "{{TTTIME}}";
					$replace[] = "[[TTTIME]]";
					$blank[] = "";
					break;

				case "{{EVTTIME}}":
					$search[] = "{{EVTTIME}}";
					$replace[] = "[[EVTTIME]]";
					$blank[] = "";
					break;

				case "{{TOOLTIP}}":
					$search[] = "{{TOOLTIP}}";
					$replace[] = "[[TOOLTIP]]";
					$blank[] = "";
					break;

				case "{{CATEGORYLNK}}":
					$router = JRouter::getInstance("site");
					$vars = $router->getVars();
					$vars["catids"] = $event->catid();
					$eventlink = "index.php?";
					foreach ($vars as $key => $val)
					{
						$eventlink.= $key . "=" . $val . "&";
					}
					$eventlink = substr($eventlink, 0, strlen($eventlink) - 1);
					$eventlink = JRoute::_($eventlink);
					$catlink = '<a class="ev_link_cat" href="' . $eventlink . '"  title="' . JEventsHTML::special($event->catname()) . '">' . $event->catname() . '</a>';
					$search[] = "{{CATEGORYLNK}}";
					$replace[] = $catlink;
					$blank[] = "";
					break;

				case "{{CATEGORYIMG}}":
					$search[] = "{{CATEGORYIMG}}";
					$replace[] = $event->getCategoryImage();
					$blank[] = "";
					break;

				case "{{CATDESC}}":
					$search[] = "{{CATDESC}}";
					$replace[] = $event->getCategoryDescription();
					$blank[] = "";
					break;

				case "{{ICALDIALOG}}":
				case "{{ICALBUTTON}}":
				case "{{EDITDIALOG}}":
				case "{{EDITBUTTON}}":
					static $styledone = false;
					if (!$styledone)
					{
						$document = JFactory::getDocument();
						$document->addStyleDeclaration("div.jevdialogs {position:relative;margin-top:35px;text-align:left;}\n div.jevdialogs img{float:none!important;margin:0px}");
						$styledone = true;
					}

					if ($jevparams->get("showicalicon", 0) && !$jevparams->get("disableicalexport", 0))
					{
						JEVHelper::script('view_detail.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");
						$cssloaded = true;
						ob_start();
						?>
						<a href="javascript:void(0)" onclick='clickIcalButton()' title="<?php echo JText::_('JEV_SAVEICAL'); ?>">
							<img src="<?php echo JURI::root() . 'administrator/components/' . JEV_COM_COMPONENT . '/assets/images/jevents_event_sml.png' ?>" align="middle" name="image"  alt="<?php echo JText::_('JEV_SAVEICAL'); ?>" style="height:24px;"/>
						</a>
						<div class="jevdialogs">
							<?php
							$search[] = "{{ICALDIALOG}}";
							ob_start();
							$view->eventIcalDialog($event, $mask);
							$dialog = ob_get_clean();
							$replace[] = $dialog;
							$blank[] = "";
							echo $dialog;
							?>
						</div>

						<?php
						$search[] = "{{ICALBUTTON}}";
						$replace[] = ob_get_clean();
						$blank[] = "";
					}
					else
					{
						$search[] = "{{ICALBUTTON}}";
						$replace[] = "";
						$blank[] = "";
						$search[] = "{{ICALDIALOG}}";
						$replace[] = "";
						$blank[] = "";
					}
					if ((JEVHelper::canEditEvent($event) || JEVHelper::canPublishEvent($event) || JEVHelper::canDeleteEvent($event)) && !( $mask & MASK_POPUP ))
					{
						JEVHelper::script('view_detail.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");

						ob_start();
						?>
						<a href="javascript:void(0)" onclick='clickEditButton()' title="<?php echo JText::_('JEV_E_EDIT'); ?>">
					<?php echo JEVHelper::imagesite('edit.png', JText::_('JEV_E_EDIT')); ?>
						</a>
						<div class="jevdialogs">
							<?php
							$search[] = "{{EDITDIALOG}}";
							ob_start();
							$view->eventManagementDialog($event, $mask);
							$dialog = ob_get_clean();
							$replace[] = $dialog;
							$blank[] = "";
							echo $dialog;
							?>
						</div>

						<?php
						$search[] = "{{EDITBUTTON}}";
						$replace[] = ob_get_clean();
						$blank[] = "";
					}
					else
					{
						$search[] = "{{EDITBUTTON}}";
						$replace[] = "";
						$blank[] = "";
						$search[] = "{{EDITDIALOG}}";
						$replace[] = "";
						$blank[] = "";
					}

					break;


				case "{{CREATED}}":
					$created = JevDate::getDate($event->created());
					$search[] = "{{CREATED}}";
					$replace[] = $created->toFormat(JText::_("DATE_FORMAT_CREATED"));
					$blank[] = "";
					break;


				case "{{ACCESS}}":
					if (JVersion::isCompatible("1.6.0"))
					{
						$search[] = "{{ACCESS}}";
						$replace[] = $event->getAccessName();
						$blank[] = "";
					}
					break;


				case "{{REPEATSUMMARY}}":
				case "{{STARTDATE}}":
				case "{{ENDDATE}}":
				case "{{STARTTIME}}":
				case "{{ENDTIME}}":
					if ($template_name == "icalevent.detail_body")
					{
						$search[] = "{{REPEATSUMMARY}}";
						$replace[] = $event->repeatSummary();
						$blank[] = "";

						$row = $event;
						$start_date = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 0);
						$start_time = JEVHelper::getTime($row->getUnixStartTime(), $row->hup(), $row->minup());
						$stop_date = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), 0);
						$stop_time = JEVHelper::getTime($row->getUnixEndTime(), $row->hdn(), $row->mindn());
						$stop_time_midnightFix = $stop_time;
						$stop_date_midnightFix = $stop_date;
						if ($row->sdn() == 59 && $row->mindn() == 59)
						{
							$stop_time_midnightFix = JEVHelper::getTime($row->getUnixEndTime() + 1, 0, 0);
							$stop_date_midnightFix = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn() + 1, 0);
						}

						$search[] = "{{STARTDATE}}";
						$replace[] = $start_date;
						$blank[] = "";
						$search[] = "{{ENDDATE}}";
						$replace[] = $stop_date;
						$blank[] = "";
						$search[] = "{{STARTTIME}}";
						$replace[] = $start_time;
						$blank[] = "";
						$search[] = "{{ENDTIME}}";
						$replace[] = $stop_time_midnightFix;
						$blank[] = "";
					}
					else
					{
						$row = $event;
						$start_date = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 0);
						$start_time = JEVHelper::getTime($row->getUnixStartTime(), $row->hup(), $row->minup());
						$stop_date = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), 0);
						$stop_time = JEVHelper::getTime($row->getUnixEndTime(), $row->hdn(), $row->mindn());
						$stop_time_midnightFix = $stop_time;
						$stop_date_midnightFix = $stop_date;
						if ($row->sdn() == 59 && $row->mindn() == 59)
						{
							$stop_time_midnightFix = JEVHelper::getTime($row->getUnixEndTime() + 1, 0, 0);
							$stop_date_midnightFix = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn() + 1, 0);
						}
						$search[] = "{{STARTDATE}}";
						$replace[] = $start_date;
						$blank[] = "";
						$search[] = "{{ENDDATE}}";
						$replace[] = $stop_date;
						$blank[] = "";
						$search[] = "{{STARTTIME}}";
						$replace[] = $row->alldayevent()?"":$start_time;
						$blank[] = "";
						$search[] = "{{ENDTIME}}";
						$replace[] = ($row->noendtime()||$row->alldayevent())?"":$stop_time_midnightFix;
						$blank[] = "";

						// these would slow things down if not needed in the list
						$dorepeatsummary = (strpos($template->value, ":REPEATSUMMARY}}") !== false);
						if ($dorepeatsummary)
						{

							$cfg = & JEVConfig::getInstance();
							$jevtask = JRequest::getString("jevtask");
							$jevtask = str_replace(".listevents", "", $jevtask);

							$showyeardate = $cfg->get("showyeardate", 0);

							$row = $event;
							$times = "";
							if (($showyeardate && $jevtask == "year") || $jevtask == "search.results" || $jevtask == "month.calendar" || $jevtask == "cat" || $jevtask == "range")
							{

								$start_publish = $row->getUnixStartDate();
								$stop_publish = $row->getUnixEndDate();

								if ($stop_publish == $start_publish)
								{
									if ($row->noendtime())
									{
										$times = $start_time;
									}
									else if ($row->alldayevent())
									{
										$times = "";
									}
									else if ($start_time != $stop_time)
									{
										$times = $start_time . ' - ' . $stop_time_midnightFix;
									}
									else
									{
										$times = $start_time;
									}

									$times = $start_date . " " . $times . "<br/>";
								}
								else
								{
									if ($row->noendtime())
									{
										$times = $start_time;
									}
									else if ($row->alldayevent())
									{
										$times = "";
									}								
									else if ($start_time != $stop_time && !$row->alldayevent())
									{
										$times = $start_time . '&nbsp;-&nbsp;' . $stop_time_midnightFix;
									}
									$times = $start_date . ' - ' . $stop_date . " " . $times . "<br/>";
								}
							}
							else if (($jevtask == "day" || $jevtask == "week" ) && ($row->starttime() != $row->endtime()) && !($row->alldayevent()))
							{
								if ($row->noendtime())
								{
									if ($showyeardate && $jevtask == "year")
									{
										$times = $start_time . '&nbsp;-&nbsp;' . $stop_time_midnightFix . '&nbsp;';
									}
									else
									{
										$times = $start_time . '&nbsp;';
									}
								}
								else if ($row->alldayevent())
								{
									$times = "";
								}								
								else
								{
									$times = $start_time . '&nbsp;-&nbsp;' . $stop_time_midnightFix . '&nbsp;';
								}
							}
							$search[] = "{{REPEATSUMMARY}}";
							$replace[] = $times;
							$blank[] = "";
						}
					}
					break;


				case "{{PREVIOUSNEXT}}":
					static $doprevnext;
					if (!isset($doprevnext))
					{
						$doprevnext = (strpos($template->value, ":PREVIOUSNEXT}}") !== false);
					}
					if ($doprevnext)
					{
						$search[] = "{{PREVIOUSNEXT}}";
						$replace[] = $event->previousnextLinks();
						$blank[] = "";
					}
					break;

				case "{{CREATOR_LABEL}}":
					$search[] = "{{CREATOR_LABEL}}";
					$replace[] = JText::_('JEV_BY');
					$blank[] = "";
					break;

				case "{{CREATOR}}":
					$search[] = "{{CREATOR}}";
					$replace[] = $event->contactlink();
					$blank[] = "";
					break;

				case "{{HITS}}":
					$search[] = "{{HITS}}";
					$replace[] = "<span class='hitslabel'>" . JText::_('JEV_EVENT_HITS') . '</span> : ' . $event->hits();
					$blank[] = "";
					break;

				case "{{LOCATION_LABEL}}":
				case "{{LOCATION}}":
					if ($event->hasLocation())
					{
						$search[] = "{{LOCATION_LABEL}}";
						$replace[] = JText::_('JEV_EVENT_ADRESSE') . "&nbsp;";
						$blank[] = "";
						$search[] = "{{LOCATION}}";
						$replace[] = $event->location();
						$blank[] = "";
					}
					else
					{
						$search[] = "{{LOCATION_LABEL}}";
						$replace[] = "";
						$blank[] = "";
						$search[] = "{{LOCATION}}";
						$replace[] = "";
						$blank[] = "";
					}
					break;

				case "{{CONTACT_LABEL}}":
				case "{{CONTACT}}":
					if ($event->hasContactInfo())
					{
						if (strpos($event->contact_info(),'<script')===false){
							$dispatcher	=& JDispatcher::getInstance();
							JPluginHelper::importPlugin('content');

							//Contact
							$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
							if (strpos($event->contact_info(),'<a href=')===false && $event->contact_info()!=""){
								$event->contact_info(preg_replace('#(http://)('.$pattern.'*)#i', '<a href="\\1\\2">\\1\\2</a>', $event->contact_info()));
							}
							$tmprow = new stdClass();
							$tmprow->text = $event->contact_info();

							if (JVersion::isCompatible("1.6.0")) {
								$dispatcher->trigger( 'onContentPrepare', array('com_jevents', &$tmprow, &$params, 0 ));
							}
							else {
								$dispatcher->trigger( 'onPrepareContent', array( &$tmprow, &$params, 0 ));
							}
							$event->contact_info($tmprow->text);
						}
						$search[] = "{{CONTACT_LABEL}}";
						$replace[] = JText::_('JEV_EVENT_CONTACT') . "&nbsp;";
						$blank[] = "";
						$search[] = "{{CONTACT}}";
						$replace[] = $event->contact_info();
						$blank[] = "";
					}
					else
					{
						$search[] = "{{CONTACT_LABEL}}";
						$replace[] = "";
						$blank[] = "";
						$search[] = "{{CONTACT}}";
						$replace[] = "";
						$blank[] = "";
					}
					break;

				case "{{EXTRAINFO}}":
					//Extra
					if (strpos($event->extra_info(),'<script')===false  && $event->extra_info()!=""){
						$dispatcher	=& JDispatcher::getInstance();
						JPluginHelper::importPlugin('content');
						
						$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
						if (strpos($event->extra_info(),'<a href=')===false){
							$event->extra_info(preg_replace('#(http://)('.$pattern.'*)#i', '<a href="\\1\\2">\\1\\2</a>', $event->extra_info()));
						}
						//$row->extra_info(eregi_replace('[^(href=|href="|href=\')](((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','\\1', $row->extra_info()));
						$tmprow = new stdClass();
						$tmprow->text = $event->extra_info();

						if (JVersion::isCompatible("1.6.0")) {
							$dispatcher->trigger( 'onContentPrepare', array('com_jevents', &$tmprow, &$params, 0 ));
						}
						else {
							$dispatcher->trigger( 'onPrepareContent', array( &$tmprow, &$params, 0 ));
						}
						$event->extra_info($tmprow->text);
					}
					
					$search[] = "{{EXTRAINFO}}";
					$replace[] = $event->extra_info();
					$blank[] = "";
					break;

				default:
					break;
			}
		}

		// Now do the plugins
		// get list of enabled plugins

		$layout = ($template_name == "icalevent.list_row" || $template_name == "month.calendar_cell" || $template_name == "month.calendar_tip") ? "list" : "detail";

		$jevplugins = JPluginHelper::getPlugin("jevents");

		foreach ($jevplugins as $jevplugin)
		{
			$classname = "plgJevents" . ucfirst($jevplugin->name);
			if (is_callable(array($classname, "substitutefield")))
			{
				$fieldNameArray = call_user_func(array($classname, "fieldNameArray"), $layout);
				if (isset($fieldNameArray["values"]))
				{
					foreach ($fieldNameArray["values"] as $fieldname)
					{
						$search[] = "{{" . $fieldname . "}}";
						// is the event detail hidden - if so then hide any custom fields too!
						if (!isset($event->_privateevent) || $event->_privateevent != 3)
						{
							$replace[] = call_user_func(array($classname, "substitutefield"), $event, $fieldname);
							if (is_callable(array($classname, "blankfield")))
							{
								$blank[] = call_user_func(array($classname, "blankfield"), $event, $fieldname);
							}
							else
							{
								$blank[] = "";
							}
						}
						else
						{
							$blank[] = "";
							$replace[] = "";
						}
					}
				}
			}
		}

		// word counts etc.
		for ($s = 0; $s < count($search); $s++)
		{
			if (strpos($search[$s], "TRUNCATED_DESC:") > 0)
			{
				global $tempreplace, $tempevent, $tempsearch;
				$tempreplace = $replace[$s];
				$tempsearch = $search[$s];
				$tempevent = $event;
				$template_value = preg_replace_callback("|$tempsearch|", 'jevSpecialHandling', $template_value);
			}
		}

		for ($s = 0; $s < count($search); $s++)
		{
			global $tempreplace, $tempevent, $tempsearch, $tempblank;
			$tempreplace = $replace[$s];
			$tempblank = $blank[$s];
			$tempsearch = str_replace("}}", "#", $search[$s]);
			$tempevent = $event;
			$template_value = preg_replace_callback("|$tempsearch(.+?)}}|", 'jevSpecialHandling2', $template_value);
		}

		$template_value = str_replace($search, $replace, $template_value);

		// non greedy replacement - because of the ?
		$template_value = preg_replace_callback('|{{.*?}}|', 'cleanUnpublished', $template_value);

		// Call content plugins - BUT because emailcloak doesn't identify emails in input fields to a text substitution
		$template_value = str_replace("@", "@£@", $template_value);
		$params =new JParameter(null);
		$tmprow = new stdClass();
		$tmprow->text = $template_value;
		$dispatcher	=& JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		if (JVersion::isCompatible("1.6.0")) {
			$dispatcher->trigger( 'onContentPrepare', array('com_jevents', &$tmprow, &$params, 0 ));
		}
		else {
			$dispatcher->trigger( 'onPrepareContent', array( &$tmprow, &$params, 0 ));
		}
		$template_value = $tmprow->text;
		$template_value = str_replace( "@£@", "@",$template_value);
		
		echo $template_value;
		return true;

	}

	function cleanLabels($matches)
	{
		if (count($matches) == 1)
		{
			$parts = explode(":", $matches[0]);
			if (count($parts) > 0)
			{
				if (strpos($matches[0], "://") > 0)
				{
					return "{{" . $parts[count($parts) - 1];
				}
				array_shift($parts);
				return "{{" . implode(":", $parts);
			}
			return "";
		}
		return "";

	}

	function cleanUnpublished($matches)
	{
		if (count($matches) == 1)
		{
			return "";
		}
		return $matches;

	}

	function jevSpecialHandling($matches)
	{
		if (count($matches) == 1 && strpos($matches[0], ":") > 0)
		{
			global $tempreplace, $tempevent, $tempsearch;
			$parts = explode(":", $matches[0]);
			if (count($parts) == 2)
			{
				$wordcount = intval(str_replace("}}", "", $parts[1]));
				$value = strip_tags($tempreplace);

				$value = str_replace("  ", " ", $value);
				$words = explode(" ", $value);
				if (count($words) > $wordcount)
				{
					$words = array_slice($words, 0, $wordcount);
					$words[] = " ...";
				}
				return implode(" ", $words);
			}
			else
			{
				return $matches[0];
			}
		}
		else if (count($matches) == 1)
			return $matches[0];

	}

	function jevSpecialHandling2($matches)
	{
		if (count($matches) == 2 && strpos($matches[0], "#") > 0)
		{
			global $tempreplace, $tempevent, $tempsearch, $tempblank;
			$parts = explode("#", $matches[1]);
			if ($tempreplace == $tempblank)
			{
				if (count($parts) == 2)
				{
					return $parts[1];
				}
				else
					return "";
			}
			else if (count($parts) >= 1)
			{
				return sprintf($parts[0], $tempreplace);
			}
		}
		else
			return "";

	}

	