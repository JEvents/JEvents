<?php
defined('_JEXEC') or die('Restricted access');

function DefaultLoadedFromTemplate($view, $template_name, $event, $mask, $template_value = false, $runplugins = true)
{
	$db = JFactory::getDBO();
	// find published template
	static $templates;
	static $fieldNameArray;
	if (!isset($templates))
	{
		$templates = array();
		$fieldNameArray = array();
		$rawtemplates = array();
	}
	$specialmodules = false;
	static $allcat_catids;
	$loadedFromFile = false;

	if (!$template_value)
	{
		if (!array_key_exists($template_name, $templates))
		{

			$db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= " . $db->Quote($template_name) . " AND value<>'' AND " . 'language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			$rawtemplates = $db->loadObjectList();
			$templates[$template_name] = array();
			if ($rawtemplates){
				foreach ($rawtemplates as $rt){
					if (!isset($templates[$template_name][$rt->language])){
						$templates[$template_name][$rt->language] = array();
					}
					$templates[$template_name][$rt->language][$rt->catid] = $rt;
				}
			}

			if (!isset($templates[$template_name]['*'][0])) {
				try {
					$viewname = $view->getViewName();
				}
				catch (Exception $e){
					$viewname = "default";
				}
				$templatefile = JPATH_BASE . '/' . 'templates' . '/' . JFactory::getApplication()->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT ."/$viewname/defaults/$template_name.html";
				if (!JFile::exists($templatefile)){
					$templatefile = JPATH_BASE . '/' . 'templates' . '/' . JFactory::getApplication()->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT ."/defaults/$template_name.html";
				}
				if (!JFile::exists($templatefile)){
					$templatefile = JEV_VIEWS."/$viewname/defaults/$template_name.html";
				}
				if (!JFile::exists($templatefile)){
					$templatefile = JEV_ADMINPATH."views/defaults/tmpl/$template_name.html";
				}
				// Fall back to html version
				if (JFile::exists($templatefile))
				{
					$loadedFromFile = true;
                                        if (!isset($templates[$template_name]['*'])){
                                            $templates[$template_name]['*'] = array();
                                        }
					$templates[$template_name]['*'][0] =new stdClass();
					$templates[$template_name]['*'][0]->value = file_get_contents($templatefile);
					$templates[$template_name]['*'][0]->params = null;
					$templates[$template_name]['*'][0]->fromfile = true;
				}
				else {
					return false;
				}
			}

			if (isset($templates[$template_name][JFactory::getLanguage()->getTag()]))
			{
				$templateArray = $templates[$template_name][JFactory::getLanguage()->getTag()];
				// We have the most specific by language now fill in the gaps
				 if (isset($templates[$template_name]["*"]))
				{
					foreach ($templates[$template_name]["*"] as $cat => $cattemplates){
						if (!isset($templateArray[$cat])){
							$templateArray[$cat] = $cattemplates;
						}
					}
				}
				$templates[$template_name] = 	$templateArray;
			}
			else if (isset($templates[$template_name]["*"]))
			{
				$templates[$template_name] = $templates[$template_name]["*"];
			}
			else if (is_array($templates[$template_name]) && count($templates[$template_name]) == 0)
			{
				$templates[$template_name] = null;
			}
			else if (is_array($templates[$template_name]) && count($templates[$template_name]) > 0)
			{
				$templates[$template_name] = current($templates[$template_name]);
			}
			else
			{
				$templates[$template_name] = null;
			}

			$matched = false;
			foreach ( array_keys($templates[$template_name]) as $catid){
				if ( $templates[$template_name][$catid]->value != "") {
					if (isset($templates[$template_name][$catid]->params))
					{
						$templates[$template_name][$catid]->params = new JRegistry($templates[$template_name][$catid]->params);
						$specialmodules = $templates[$template_name][$catid]->params;
					}

					// Adjust template_value to include dynamic module output then strip it out afterwards
					if ($specialmodules)
					{
						$modids = $specialmodules->get("modid", array());
						if (count($modids)>0){
							$modvals = $specialmodules->get("modval", array());
							// not sure how this can arise :(
							if (is_object($modvals)){
								$modvals = get_object_vars($modvals);
							}
							$modids = array_values($modids);
							$modvals = array_values($modvals);

							for ($count=0;$count<count($modids) && $count<count($modvals) && trim($modids[$count])!="";$count++) {
								$templates[$template_name][$catid]->value .= "{{module start:MODULESTART#".$modids[$count]."}}";
								// cleaned later!
								//$templates[$template_name][$catid]->value .= preg_replace_callback('|{{.*?}}|', 'cleanLabels', $modvals[$count]);
								$templates[$template_name][$catid]->value .= $modvals[$count];
								$templates[$template_name][$catid]->value .= "{{module end:MODULEEND}}";
							}
						}
					}

					// strip carriage returns other wise the preg replace doesn;y work - needed because wysiwyg editor may add the carriage return in the template field
					$templates[$template_name][$catid]->value = str_replace("\r", '', $templates[$template_name][$catid]->value);
					$templates[$template_name][$catid]->value = str_replace("\n", '', $templates[$template_name][$catid]->value);
					// non greedy replacement - because of the ?
					$templates[$template_name][$catid]->value = preg_replace_callback('|{{.*?}}|', 'cleanLabels', $templates[$template_name][$catid]->value);

					$matchesarray = array();
					preg_match_all('|{{.*?}}|', $templates[$template_name][$catid]->value, $matchesarray);

					$templates[$template_name][$catid]->matchesarray = $matchesarray;
				}
			}

		}

		if (is_null($templates[$template_name])){
			return false;
		}
		
		$catids = ($event->catids() && count($event->catids())) ? $event->catids() : array($event->catid());
		$catids[]=0;

		// find the overlap
		$catids = array_intersect($catids, array_keys($templates[$template_name]));

		// At present must be an EXACT category match - no inheriting allowed!
		if (count($catids)==0){
			if (!isset($templates[$template_name][0]) || $templates[$template_name][0]->value == ""){
				return false;
			}
		}

		$template = false;
		foreach ($catids as $catid){
			// use the first matching non-empty layout
			if ($templates[$template_name][$catid]->value!=""){
				$template = $templates[$template_name][$catid];
				break;
			}
		}
		if (!$template) {
			return false;
		}

		$template_value = $template->value;
		$specialmodules = $template->params;

		$matchesarray = $template->matchesarray;
		$loadedFromFile = isset($template->fromfile);
	}
	else
	{
		if ($runplugins && JRequest::getString("option")!="com_jevents"){
			// This is a special scenario where we call this function externally e.g. from RSVP Pro messages
			// In this scenario we have not gone through the displaycustomfields plugin
			static $pluginscalled = array();
			if (!isset($pluginscalled[$event->rp_id()]))
			{
				$dispatcher = JEventDispatcher::getInstance();
				JPluginHelper::importPlugin("jevents");
				$customresults = $dispatcher->trigger('onDisplayCustomFields', array(&$event));
				$pluginscalled[$event->rp_id()] = $event;
			}
			else
			{
				$event = $pluginscalled[$event->rp_id()];
			}
		}

		// Adjust template_value to include dynamic module output then strip it out afterwards
		if ($specialmodules)
		{
			$modids = $specialmodules->get("modid", array());
			if (count($modids)>0){
				$modvals = $specialmodules->get("modval", array());
				// not sure how this can arise :(
				if (is_object($modvals)){
					$modvals = get_object_vars($modvals);
				}
				$modids = array_values($modids);
				$modvals = array_values($modvals);

				for ($count=0;$count<count($modids) && $count<count($modvals) && trim($modids[$count])!="";$count++) {
					$template_value .= "{{module start:MODULESTART#".$modids[$count]."}}";
					// cleaned later!
					//$template_value .= preg_replace_callback('|{{.*?}}|', 'cleanLabels', $modvals[$count]);
					$template_value .= $modvals[$count];
					$template_value .= "{{module end:MODULEEND}}";
				}
			}
		}

		// strip carriage returns other wise the preg replace doesn;y work - needed because wysiwyg editor may add the carriage return in the template field
		$template_value = str_replace("\r", '', $template_value);
		$template_value = str_replace("\n", '', $template_value);
		// non greedy replacement - because of the ?
		$template_value = preg_replace_callback('|{{.*?}}|', 'cleanLabels', $template_value);

		$matchesarray = array();
		preg_match_all('|{{.*?}}|', $template_value, $matchesarray);
	}
	if ($template_value == "")
		return;
	if (count($matchesarray) == 0)
		return;

// now replace the fields
	$search = array();
	$replace = array();
	$blank = array();
	$rawreplace = array();

	$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);

	for ($i = 0; $i < count($matchesarray[0]); $i++)
	{
		$strippedmatch = preg_replace('/(#|:|;)+[^}]*/', '', $matchesarray[0][$i]);

		if (in_array($strippedmatch, $search))
		{
			continue;
		}
		// translation string
		if (JString::strpos($strippedmatch, "{{_") === 0 && JString::strpos($strippedmatch, " ") === false)
		{
			$search[] = $strippedmatch;
			$strippedmatch = JString::substr($strippedmatch, 3, JString::strlen($strippedmatch) - 5);
			$replace[] = JText::_($strippedmatch);
			$blank[] = "";
			continue;
		}
		// Built in fields	
		switch ($strippedmatch) {
			case "{{TITLE}}":
				$search[] = "{{TITLE}}";
				$replace[] = $event->title();
				$blank[] = "";
				break;
			case "{{TRUNCATED_TITLE}}":
				$search[] = "{{TRUNCATED_TITLE:.*?}}";
				$replace[] = $event->title();
				$blank[] = "";
				break;
			case "{{PRIORITY}}":
				$search[] = "{{PRIORITY}}";
				$replace[] = $event->priority();
				$blank[] = "";
				break;

			case "{{LINK}}":
			case "{{LINKSTART}}":
			case "{{LINKEND}}":
			case "{{TITLE_LINK}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array( "{{LINK}}", $search)) {
					// Title link
					$rowlink = $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), false);
					if ($view)
					{
						$rowlink = JRoute::_($rowlink . $view->datamodel->getCatidsOutLink());
					}
					ob_start();
						?>
						<a class="ev_link_row" href="<?php echo $rowlink; ?>" title="<?php echo JEventsHTML::special($event->title()); ?>">
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
				}
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
					if ($view)
					{
						ob_start();
						$view->_viewNavAdminPanel();
						$replace[] = ob_get_clean();
					}
					else
					{
						$replace[] = "";
					}
					$blank[] = "";
					break;

				case "{{CATEGORY}}":
					$search[] = "{{CATEGORY}}";
					$replace[] = $event->catname();
					$blank[] = "";
					break;

				case "{{ALLCATEGORIES}}":
					$search[] = "{{ALLCATEGORIES}}";

					if (!isset($allcat_catids))
					{
						$db = JFactory::getDBO();
						$arr_catids = array();
						$catsql = "SELECT cat.id, cat.title as name, cat.params FROM #__categories  as cat WHERE cat.extension='com_jevents' ";
						$db->setQuery($catsql);
						$allcat_catids = $db->loadObjectList('id');
					}
					$db = JFactory::getDbo();
					$db->setQuery("Select catid from #__jevents_catmap  WHERE evid = " . $event->ev_id());
					$allcat_eventcats = $db->loadColumn();

					$allcats = array();
					foreach ($allcat_eventcats as $catid)
					{
						if (isset($allcat_catids[$catid]))
						{
							$allcats[] = $allcat_catids[$catid]->name;
						}
					}
					$replace[] = implode(", ", $allcats);
					$blank[] = "";
					break;

				case "{{CALENDAR}}":
					$search[] = "{{CALENDAR}}";
					$replace[] = $event->getCalendarName();
					$blank[] = "";
					break;

				case "{{COLOUR}}":
				case "{{colour}}":
					$bgcolor = $event->bgcolor();
					$search[] = $strippedmatch;
					$replace[] = $bgcolor == "" ? "#ffffff" : $bgcolor;
					$blank[] = "";
					break;

				case "{{RGBA}}":
					$bgcolor = $event->bgcolor();
					$search[] = $strippedmatch;
					$bgcolor = $bgcolor == "" ? "#ffffff" : $bgcolor;
                                        // skip the #
                                        if (strlen($bgcolor) == 7){
                                            $bgcolor = substr($bgcolor, 1);
                                        }
                                        if (strlen($bgcolor) == 6)
                                            list($r, $g, $b) = array($bgcolor[0].$bgcolor[1],
                                                                     $bgcolor[2].$bgcolor[3],
                                                                     $bgcolor[4].$bgcolor[5]);
                                        elseif (strlen($bgcolor) == 3)
                                            list($r, $g, $b) = array($bgcolor[0].$bgcolor[0], $bgcolor[1].$bgcolor[1], $bgcolor[2].$bgcolor[2]);
                                        else
                                            return false;

                                        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
                                        $replace[] = "rgba($r, $g, $b, 0.3)";
                                        
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

				// deprecated
				case "{{TOOLTIP}}":
					$search[] = "{{TOOLTIP}}";
					$replace[] = "[[TOOLTIP]]";
					$blank[] = "";
					break;

				// new version for bootstrap
				case "{{TOOLTIPTITLE}}":
					$search[] = "{{TOOLTIPTITLE}}";
					$replace[] = "[[TOOLTIPTITLE]]";
					$blank[] = "";
					break;

				case "{{TOOLTIPCONTENT}}":
					$search[] = "{{TOOLTIPCONTENT}}";
					$replace[] = "[[TOOLTIPCONTENT]]";
					$blank[] = "";
					break;

				case "{{CATEGORYLNK}}":
					$router = JRouter::getInstance("site");
					$catlinks = array();
					if ($jevparams->get("multicategory", 0))
					{
						$catids = $event->catids();
						$catdata = $event->getCategoryData();
					}
					else
					{
						$catids = array($event->catid());
						$catdata = array($event->getCategoryData());
					}

					$vars = $router->getVars();
					foreach ($catids as $cat)
					{
						$vars["catids"] = $cat;
						$catname = "xxx";
						foreach ($catdata as $cg)
						{
							if ($cat == $cg->id)
							{
								$catname = $cg->name;
								break;
							}
						}
						$eventlink = "index.php?";
						foreach ($vars as $key => $val)
						{
							// this is only used in the latest events module so do not perpetuate it here
							if ($key == "filter_reset")
								continue;
							if ($key == "task" && ($val == "icalrepeat.detail" || $val == "icalevent.detail"))
							{
								$val = "week.listevents";
							}
							$eventlink.= $key . "=" . $val . "&";
						}
						$eventlink = JString::substr($eventlink, 0, JString::strlen($eventlink) - 1);
						$eventlink = JRoute::_($eventlink);

						$catlinks[] = '<a class="ev_link_cat" href="' . $eventlink . '"  title="' . JEventsHTML::special($catname) . '">' . $catname . '</a>';
					}
					$search[] = "{{CATEGORYLNK}}";
					$replace[] = implode(", ", $catlinks);
					$blank[] = "";
					break;

				case "{{CATEGORYIMG}}":
					$search[] = "{{CATEGORYIMG}}";
					$replace[] = $event->getCategoryImage();
					$blank[] = "";
					break;

				case "{{CATEGORYIMGS}}":
					$search[] = "{{CATEGORYIMGS}}";
					$replace[] = $event->getCategoryImage(true);
					$blank[] = "";
					break;

				case "{{ALLCATEGORYIMGS}}":
					$search[] = "{{ALLCATEGORYIMGS}}";
					if (!isset($allcat_catids))
					{
						$db = JFactory::getDBO();
						$arr_catids = array();
						$catsql = "SELECT cat.id, cat.title as name, cat.params FROM #__categories  as cat WHERE cat.extension='com_jevents' ";
						$db->setQuery($catsql);
						$allcat_catids = $db->loadObjectList('id');
					}

					$db = JFactory::getDbo();
					$db->setQuery("Select catid from #__jevents_catmap  WHERE evid = " . $event->ev_id());
					$allcat_eventcats = $db->loadColumn();

					$output = "";
					if (is_array($allcat_eventcats)) {
						foreach ($allcat_eventcats as $catid)
						{
							if (isset($allcat_catids[$catid]))
							{
								$params = json_decode($allcat_catids[$catid]->params);
								if (isset($params->image) && $params->image!=""){
									$output .= "<img src = '".JURI::root().$params->image."' class='catimage'  alt='categoryimage' />";
								}
							}
						}
					}
                                        
					$replace[] = $output;
					$blank[] = "";
					break;
                                    
				case "{{CATDESC}}":
					$search[] = "{{CATDESC}}";
					$replace[] = $event->getCategoryDescription();
					$blank[] = "";
					break;
				case "{{CATID}}":
					$search[] = "{{CATID}}";
					$replace[] = $event->catid();
					$blank[] = "";
					break;
				case "{{PARENT_CATEGORY}}":
					$search[] = "{{PARENT_CATEGORY}}";
					$replace[] = $event->getParentCategory();
					$blank[] = "";
					break;

				case "{{ICALDIALOG}}":
				case "{{ICALBUTTON}}":
				case "{{EDITDIALOG}}":
				case "{{EDITBUTTON}}":
					// no need to repeat this for each of the matching 'case's
					if (!in_array( "{{EDITBUTTON}}", $search)) {

						if ($jevparams->get("showicalicon", 0) && !$jevparams->get("disableicalexport", 0))
						{
							$cssloaded = true;
							ob_start();
							$view->eventIcalButton($event);
							?>
							<div class="jevdialogs" style="position:relative;">
							<?php
							$search[] = "{{ICALDIALOG}}";
							if ($view)
							{
								ob_start();
								$view->eventIcalDialog($event, $mask, true);
								$dialog = ob_get_clean();
								$replace[] = $dialog;
							}
							else
							{
								$replace[] = "";
							}
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
						if ((JEVHelper::canEditEvent($event) || JEVHelper::canPublishEvent($event) || JEVHelper::canDeleteEvent($event)) )
						{
							ob_start();
							$view->eventManagementButton($event);
							?>
							<div class="jevdialogs">
							<?php
							$search[] = "{{EDITDIALOG}}";
							if ($view)
							{
								ob_start();
								$view->eventManagementDialog($event, $mask, true);
								$dialog = ob_get_clean();
								$replace[] = $dialog;
							}
							else
							{
								$replace[] = "";
							}
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
					}

					break;

				case "{{CREATED}}":
					$compparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
					$jtz = $compparams->get("icaltimezonelive", "");
					if ($jtz == "")
					{
						$jtz = null;
					}
					$created = JevDate::getDate($event->created(), $jtz);
					$search[] = "{{CREATED}}";
					$replace[] = $created->toFormat(JText::_("DATE_FORMAT_CREATED"));
					$blank[] = "";
					break;

				case "{{ACCESS}}":
					$search[] = "{{ACCESS}}";
					$replace[] = $event->getAccessName();
					$blank[] = "";
					break;

				case "{{JEVSTARTED}}":
				case "{{JEVENDED}}":
					// no need to repeat this for each of the matching 'case's
					if (!in_array( "{{JEVSTARTED}}", $search)) {

						$search[] = "{{JEVSTARTED}}";

						$now = new JevDate("+0 seconds");
						$now = $now->toFormat("%Y-%m-%d %H:%M:%S");

						$replace[] = $event->publish_up() < $now ? JText::_("JEV_EVENT_STARTED") : "";
						$blank[] = "";
						$search[] = "{{JEVENDED}}";
						$replace[] = $event->publish_down() < $now ? JText::_("JEV_EVENT_ENDED") : "";
						$blank[] = "";
					}
					break;

				case "{{REPEATSUMMARY}}":
				case "{{STARTDATE}}":
				case "{{ENDDATE}}":
				case "{{STARTTIME}}":
				case "{{ENDTIME}}":
				case "{{STARTTZ}}":
				case "{{ENDTZ}}":
				case "{{ISOSTART}}":
				case "{{ISOEND}}":
				case "{{DURATION}}":
				case "{{COUNTDOWN}}":
				case "{{MULTIENDDATE}}":
					// no need to repeat this for each of the matching 'case's
					if (!in_array( "{{COUNTDOWN}}", $search)) {

						if ($template_name == "icalevent.detail_body")
						{
							$search[] = "{{REPEATSUMMARY}}";
							$repeatsummary = $view->repeatSummary($event);
							if (!$repeatsummary)
							{
								$repeatsummary = $event->repeatSummary();
							}
							if ($jevparams->get("com_repeatview",1)) {
								$replace[] = $repeatsummary;
							}
							else {
								$replace[] = "";
							}
							//$replace[] = $event->repeatSummary();
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
							$replace[] = $row->alldayevent() ? "" : $start_time;
							$blank[] = "";
							$search[] = "{{ENDTIME}}";
							$replace[] = ($row->noendtime() || $row->alldayevent()) ? "" : $stop_time_midnightFix;
							$blank[] = "";
							$search[] = "{{MULTIENDDATE}}";
							$replace[]  = $row->endDate() > $row->startDate() ? $stop_date : "";
							$blank[] = "";
							$search[] = "{{STARTTZ}}";
							$replace[] = $row->alldayevent() ? "" : $start_time;
							$blank[] = "";
							$search[] = "{{ENDTZ}}";
							$replace[] = ($row->noendtime() || $row->alldayevent()) ? "" : $stop_time_midnightFix;
							$blank[] = "";

							$rawreplace["{{STARTDATE}}"]= $row->getUnixStartDate();
							$rawreplace["{{ENDDATE}}"]= $row->getUnixEndDate();
							$rawreplace["{{STARTTIME}}"] = $row->alldayevent() ? "" : $row->getUnixStartTime();
							$rawreplace["{{ENDTIME}}"] =  ($row->noendtime() || $row->alldayevent()) ? "" : $row->getUnixEndTime();
							$rawreplace["{{STARTTZ}}"] = $row->yup()."-".$row->mup()."-".$row->dup()." ".$row->hup().":".$row->minup().":".$row->sup();
							$rawreplace["{{ENDTZ}}"] = $row->ydn()."-".$row->mdn()."-".$row->ddn()." ".$row->hdn().":".$row->mindn().":".$row->sdn();
							$rawreplace["{{MULTIENDDATE}}"] = $row->endDate() > $row->startDate() ? $row->getUnixEndDate() : "";

							if (JString::strpos($template_value, "{{ISOSTART}}") !== false || JString::strpos($template_value, "{{ISOEND}}") !== false)
							{
								$search[] = "{{ISOSTART}}";
								$replace[] = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hup(), $row->minup());
								$blank[] = "";
								$search[] = "{{ISOEND}}";
								$replace[] = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hdn(), $row->mindn());
								$blank[] = "";
							}
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
							$replace[] = $row->alldayevent() ? "" : $start_time;
							$blank[] = "";
							$search[] = "{{ENDTIME}}";
							$replace[] = ($row->noendtime() || $row->alldayevent()) ? "" : $stop_time_midnightFix;
							$blank[] = "";
							$search[] = "{{MULTIENDDATE}}";
							$replace[] = $row->endDate() > $row->startDate() ? $stop_date : "";
							$blank[] = "";
							$search[] = "{{STARTTZ}}";
							$replace[] = $row->alldayevent() ? "" : $start_time;
							$blank[] = "";
							$search[] = "{{ENDTZ}}";
							$replace[] = ($row->noendtime() || $row->alldayevent()) ? "" : $stop_time_midnightFix;
							$blank[] = "";

							$rawreplace["{{STARTDATE}}"]= $row->getUnixStartDate();
							$rawreplace["{{ENDDATE}}"]= $row->getUnixEndDate();
							$rawreplace["{{STARTTIME}}"] = $row->alldayevent() ? "" : $row->getUnixStartTime();
							$rawreplace["{{ENDTIME}}"] =  ($row->noendtime() || $row->alldayevent()) ? "" : $row->getUnixEndTime();
							$rawreplace["{{STARTTZ}}"] = $row->yup()."-".$row->mup()."-".$row->dup()." ".$row->hup().":".$row->minup().":".$row->sup();
							$rawreplace["{{ENDTZ}}"] = $row->ydn()."-".$row->mdn()."-".$row->ddn()." ".$row->hdn().":".$row->mindn().":".$row->sdn();
							$rawreplace["{{MULTIENDDATE}}"] = $row->endDate() > $row->startDate() ? $row->getUnixEndDate() : "";

							if (JString::strpos($template_value, "{{ISOSTART}}") !== false || JString::strpos($template_value, "{{ISOEND}}") !== false)
							{
								$search[] = "{{ISOSTART}}";
								$replace[] = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hup(), $row->minup());
								$blank[] = "";
								$search[] = "{{ISOEND}}";
								$replace[] = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hdn(), $row->mindn());
								$blank[] = "";
							}

							// these would slow things down if not needed in the list
							$dorepeatsummary = (JString::strpos($template_value, "{{REPEATSUMMARY}}") !== false);
							if ($dorepeatsummary)
							{

								$cfg = JEVConfig::getInstance();
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

						$search[] = "{{COUNTDOWN}}";
						$timedelta = $row->getUnixStartTime() - JevDate::mktime();
						$eventPassed = !($timedelta >= 0);
						$fieldval = JText::_("JEV_COUNTDOWN_FORMAT");
						$shownsign = false;
						if (stripos($fieldval, "%nopast") !== false)
						{
							if (!$eventPassed)
							{
								$fieldval = str_ireplace("%nopast", "", $fieldval);
							}
							else
							{
								$fieldval = JText::_('JEV_EVENT_ALREADY_STARTED');
							}
						}
						if (stripos($fieldval, "%d") !== false)
						{
							$days = intval($timedelta / (60 * 60 * 24));
							$timedelta -= $days * 60 * 60 * 24;
							$fieldval = str_ireplace("%d", $days, $fieldval);
							$shownsign = true;
						}
						if (stripos($fieldval, "%h") !== false)
						{
							$hours = intval($timedelta / (60 * 60));
							$timedelta -= $hours * 60 * 60;
							if ($shownsign)
								$hours = abs($hours);
							$hours = sprintf("%02d", $hours);
							$fieldval = str_ireplace("%h", $hours, $fieldval);
							$shownsign = true;
						}
						if (stripos($fieldval, "%m") !== false)
						{
							$mins = intval($timedelta / 60);
							$timedelta -= $hours * 60;
							if ($mins)
								$mins = abs($mins);
							$mins = sprintf("%02d", $mins);
							$fieldval = str_ireplace("%m", $mins, $fieldval);
						}
						$replace[] = $fieldval;
						$blank[] = "";

						$search[] = "{{DURATION}}";
						$timedelta = $row->noendtime() ? "" : $row->getUnixEndTime() - $row->getUnixStartTime();
						if ($row->alldayevent())
						{
							$timedelta = $row->getUnixEndDate() - $row->getUnixStartDate() + 60 * 60 * 24;
						}
						$fieldval = JText::_("JEV_DURATION_FORMAT");
						$shownsign = false;
						// whole days!
						if (stripos($fieldval, "%wd") !== false)
						{
							$days = intval($timedelta / (60 * 60 * 24));
							$timedelta -= $days * 60 * 60 * 24;

							if ($timedelta > 3610)
							{
								//if more than 1 hour and 10 seconds over a day then round up the day output
								$days +=1;
							}

							$fieldval = str_ireplace("%d", $days, $fieldval);
							$shownsign = true;
						}
						if (stripos($fieldval, "%d") !== false)
						{
							$days = intval($timedelta / (60 * 60 * 24));
							$timedelta -= $days * 60 * 60 * 24;
							/*
							  if ($timedelta>3610){
							  //if more than 1 hour and 10 seconds over a day then round up the day output
							  $days +=1;
							  }
							 */
							$fieldval = str_ireplace("%d", $days, $fieldval);
							$shownsign = true;
						}
						if (stripos($fieldval, "%h") !== false)
						{
							$hours = intval($timedelta / (60 * 60));
							$timedelta -= $hours * 60 * 60;
							if ($shownsign)
								$hours = abs($hours);
							$hours = sprintf("%02d", $hours);
							$fieldval = str_ireplace("%h", $hours, $fieldval);
							$shownsign = true;
						}
						if (stripos($fieldval, "%m") !== false)
						{
							$mins = intval($timedelta / 60);
							$timedelta -= $hours * 60;
							if ($mins)
								$mins = abs($mins);
							$mins = sprintf("%02d", $mins);
							$fieldval = str_ireplace("%m", $mins, $fieldval);
						}

						$replace[] = $fieldval;
						$blank[] = "";
					}
					break;

				case "{{PREVIOUSNEXT}}":
					static $doprevnext;
					if (!isset($doprevnext))
					{
						$doprevnext = (JString::strpos($template_value, "{{PREVIOUSNEXT}}") !== false);
					}
					if ($doprevnext)
					{
						$search[] = "{{PREVIOUSNEXT}}";
						$replace[] = $event->previousnextLinks();
						$blank[] = "";
					}
					break;

				case "{{PREVIOUSNEXTEVENT}}":
					static $doprevnextevent;
					if (!isset($doprevnextevent))
					{
						$doprevnextevent = (JString::strpos($template_value, "{{PREVIOUSNEXTEVENT}}") !== false);
					}
					if ($doprevnextevent)
					{
						$search[] = "{{PREVIOUSNEXTEVENT}}";
						$replace[] = $event->previousnextEventLinks();
						$blank[] = "";
					}
					break;

                                        case "{{FIRSTREPEAT}}":
                                        case "{{FIRSTREPEATSTART}}":
                                        case "{{JEVAGE}}":
					// no need to repeat this for each of the matching 'case's
					if (!in_array( "{{FIRSTREPEAT}}", $search)) {

						static $dofirstrepeat;
						if (!isset($dofirstrepeat))
						{
							$dofirstrepeat = (JString::strpos($template_value, "{{FIRSTREPEAT") !== false
									|| JString::strpos($template_value, "{{FIRSTREPEATSTART") !== false
									 || JString::strpos($template_value, "{{JEVAGE") !== false);
						}
						if ($dofirstrepeat)
						{
							$search[] = "{{FIRSTREPEAT}}";
							$firstrepeat = $event->getFirstRepeat();
							if ($firstrepeat->rp_id() == $event->rp_id())
							{
								$replace[] = "";
							}
							else
							{
								$replace[] = "<a class='ev_firstrepeat' href='" . $firstrepeat->viewDetailLink($firstrepeat->yup(), $firstrepeat->mup(), $firstrepeat->dup(), true) . "' title='" . JText::_('JEV_FIRSTREPEAT') . "' >" . JText::_('JEV_FIRSTREPEAT') . "</a>";
							}
							$blank[] = "";

							$search[] = "{{FIRSTREPEATSTART}}";
							if ($firstrepeat->rp_id() == $event->rp_id())
							{
								$replace[] = "";
							}
							else
							{
								$replace[] = JEventsHTML::getDateFormat($firstrepeat->yup(), $firstrepeat->mup(), $firstrepeat->dup(), 0);
								$rawreplace["{{FIRSTREPEATSTART}}"] = $firstrepeat->yup() . "-" . $firstrepeat->mup() . "-" . $firstrepeat->dup() . " " . $firstrepeat->hup() . ":" . $firstrepeat->minup() . ":" . $firstrepeat->sup();
							}
							$blank[] = "";

							$search[] = "{{JEVAGE}}";
							if ($firstrepeat->rp_id() == $event->rp_id())
							{
								$replace[] = "";
							}
							else
							{
								$replace[] = ($event->yup()>$firstrepeat->yup() && $event->mup()==$firstrepeat->mup()  && $event->dup()==$firstrepeat->dup() )   ?  $event->yup()-$firstrepeat->yup() : "";
							}
							$blank[] = "";

						}
					}
					break;
				case "{{LASTREPEAT}}":
				case "{{LASTREPEATEND}}":
					// no need to repeat this for each of the matching 'case's
					if (!in_array( "{{LASTREPEAT}}", $search)) {

						static $dolastrepeat;
						if (!isset($dolastrepeat))
						{
							$dolastrepeat = (JString::strpos($template_value, "{{LASTREPEAT}}") !== false || JString::strpos($template_value, "{{LASTREPEATEND}}") !== false);
						}
						if ($dolastrepeat)
						{
							$search[] = "{{LASTREPEAT}}";
							$lastrepeat = $event->getLastRepeat();
							if ($lastrepeat->rp_id() == $event->rp_id())
							{
								$replace[] = "";
							}
							else
							{
								$replace[] = "<a class='ev_lastrepeat' href='" . $lastrepeat->viewDetailLink($lastrepeat->yup(), $lastrepeat->mup(), $lastrepeat->dup(), true) . "' title='" . JText::_('JEV_LASTREPEAT') . "' >" . JText::_('JEV_LASTREPEAT') . "</a>";
							}
							$blank[] = "";

							$search[] = "{{LASTREPEATEND}}";
							if ($lastrepeat->rp_id() != $event->rp_id())
							{
								$replace[] = JEventsHTML::getDateFormat($lastrepeat->ydn(), $lastrepeat->mdn(), $lastrepeat->ddn(), 0);
								$rawreplace["{{LASTREPEATEND}}"] =  $lastrepeat->ydn()."-".$lastrepeat->mdn()."-".$lastrepeat->ddn()." ".$lastrepeat->hdn().":".$lastrepeat->mindn().":".$lastrepeat->sdn();
							}
							else {
								$replace[] = "";
							}
							$blank[] = "";
						}
					}
					break;

				case "{{CREATOR_LABEL}}":
					$search[] = "{{CREATOR_LABEL}}";
					if ($jevparams->get("com_byview",1) || $template_name!="icalevent.detail_body") {
						$replace[] = JText::_('JEV_BY');
					}
					else {
						$replace[] = "";
					}
					$blank[] = "";
					break;

				case "{{CREATOR}}":
					$search[] = "{{CREATOR}}";
					if ($jevparams->get("com_byview",1) || $template_name!="icalevent.detail_body") {
						$replace[] = $event->contactlink();
					}
					else {
						$replace[] = "";
					}
					$blank[] = "";
					break;

				case "{{HITS}}":
					$search[] = "{{HITS}}";
					if ($jevparams->get("com_hitsview",1) || $template_name!="icalevent.detail_body") {
						$replace[] = "<span class='hitslabel'>" . JText::_('JEV_EVENT_HITS') . '</span> : ' . $event->hits();
					}
					else {
						$replace[] = "";
					}
					$blank[] = "";
					break;

				case "{{LOCATION_LABEL}}":
				case "{{LOCATION}}":
					// no need to repeat this for each of the matching 'case's
					if (!in_array( "{{LOCATION}}", $search)) {

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
					}
					break;

				case "{{CONTACT_LABEL}}":
				case "{{CONTACT}}":
					// no need to repeat this for each of the matching 'case's
					if (!in_array( "{{CONTACT}}", $search)) {

						if ($event->hasContactInfo())
						{
							if (JString::strpos($event->contact_info(), '<script') === false)
							{
								$dispatcher = JEventDispatcher::getInstance();
								JPluginHelper::importPlugin('content');

								//Contact
								$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
								if (JString::strpos($event->contact_info(), '<a href=') === false && $event->contact_info() != "")
								{
									$event->contact_info(preg_replace('@(https?://)(' . $pattern . '*)@i', '<a href="\\1\\2">\\1\\2</a>', $event->contact_info()));
								}
								// NO need to call conContentPrepate since its called on the template value below here
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
					}
					break;

				case "{{EXTRAINFO}}":
					//Extra
					if (JString::strpos($event->extra_info(), '<script') === false && $event->extra_info() != "")
					{
						$dispatcher = JEventDispatcher::getInstance();
						JPluginHelper::importPlugin('content');

						$pattern = '[a-zA-Z0-9&?_.,=%\-\/#]';
						if (JString::strpos($event->extra_info(), '<a href=') === false)
						{
							$event->extra_info(preg_replace('@(https?://)(' . $pattern . '*)@i', '<a href="\\1\\2">\\1\\2</a>', $event->extra_info()));
						}
						//$row->extra_info(eregi_replace('[^(href=|href="|href=\')](((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','\\1', $row->extra_info()));
						// NO need to call conContentPrepate since its called on the template value below here
					}

					$search[] = "{{EXTRAINFO}}";
					$replace[] = $event->extra_info();
					$blank[] = "";
					break;

				case "{{RPID}}":
					$search[] = "{{RPID}}";
					$replace[] = $event->rp_id();
					$blank[] = "";
					break;
				case "{{EVID}}":
					$search[] = "{{EVID}}";
					$replace[] = $event->ev_id();
					$blank[] = "";
					break;
			
				default:
					$strippedmatch = str_replace(array("{", "}"), "", $strippedmatch);
					if (is_callable(array($event, $strippedmatch)))
					{
						$search[] = "{{" . $strippedmatch . "}}";
						$replace[] = $event->$strippedmatch();
						$blank[] = "";
					}
					break;
			}
		}

		// Now do the plugins
		// get list of enabled plugins

		$layout = ($template_name == "icalevent.list_row" || $template_name == "month.calendar_cell" || $template_name == "month.calendar_tip") ? "list" : "detail";

		if ($runplugins){
			$jevplugins = JPluginHelper::getPlugin("jevents");

			foreach ($jevplugins as $jevplugin)
			{
				$classname = "plgJevents" . ucfirst($jevplugin->name);
				if (is_callable(array($classname, "substitutefield")))
				{

					if (!isset($fieldNameArray[$classname]))
					{
						$fieldNameArray[$classname] = array();
					}
					if (!isset($fieldNameArray[$classname][$layout]))
					{

						//list($usec, $sec) = explode(" ", microtime());
						//$starttime = (float) $usec + (float) $sec;

						$fieldNameArray[$classname][$layout] = call_user_func(array($classname, "fieldNameArray"), $layout);

						//list ($usec, $sec) = explode(" ", microtime());
						//$time_end = (float) $usec + (float) $sec;
						//echo  "$classname::fieldNameArray = ".round($time_end - $starttime, 4)."<br/>";
					}
					if (isset($fieldNameArray[$classname][$layout]["values"]))
					{
						foreach ($fieldNameArray[$classname][$layout]["values"] as $fieldname)
						{
                                                        $fieldnames = array();
                                                        // Special case where $fielename has option value in it e.g. sizedimages 
                                                        if (strpos($fieldname, ";")>0){
                                                            $temp = explode(";", $fieldname);
                                                            $fn = $temp[0];
                                                            // What is the list of them 
                                                            $temp = array();
                                                            preg_match_all('@\{{'.$fn.';(.*?)[#|}}]@', $template_value, $temp);
                                                            if (count($temp)==2 && count($temp[1])){
                                                                $fieldnames = array();
                                                                foreach ($temp[1] as $tmp){
                                                                    $fieldnames[] = $fn.";".$tmp;
                                                                }
                                                            }
                                                        }
                                                        else {
                                                            $fieldnames = array($fieldname);
                                                        }

                                                        foreach ($fieldnames as $fn){
                                                            if (!JString::strpos($template_value, $fn) !== false)
                                                            {
                                                                    continue;
                                                            }
                                                            
                                                            $search[] = "{{" . $fn . "}}";
                                                            // is the event detail hidden - if so then hide any custom fields too!
                                                            if (!isset($event->_privateevent) || $event->_privateevent != 3)
                                                            {
                                                                    $replace[] = call_user_func(array($classname, "substitutefield"), $event, $fn);
                                                                    if (is_callable(array($classname, "blankfield")))
                                                                    {
                                                                            $blank[] = call_user_func(array($classname, "blankfield"), $event, $fn);
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
			}
		}

		// word counts etc.
		for ($s = 0; $s < count($search); $s++)
		{
			if (JString::strpos($search[$s], "TRUNCATED_DESC:") > 0 || JString::strpos($search[$s], "TRUNCATED_TITLE:") > 0)
			{
				global $tempreplace, $tempevent, $tempsearch;
				$tempreplace = $replace[$s];
				$tempsearch = $search[$s];
				$tempevent = $event;
				$template_value = preg_replace_callback("|$tempsearch|", 'jevSpecialHandling', $template_value);
			}
		}

		// Date/time formats etc.
		for ($s = 0; $s < count($search); $s++)
		{
			if (JString::strpos($search[$s], "STARTDATE") > 0 || JString::strpos($search[$s], "STARTTIME") > 0 || JString::strpos($search[$s], "ENDDATE") > 0 || JString::strpos($search[$s], "ENDTIME") > 0
				|| JString::strpos($search[$s], "ENDTZ") > 0 || JString::strpos($search[$s], "STARTTZ") > 0 || JString::strpos($search[$s], "MULTIENDDATE") > 0
				|| JString::strpos($search[$s], "FIRSTREPEATSTART") > 0 || JString::strpos($search[$s], "LASTREPEATEND") > 0)
			{
				if (!isset($rawreplace[$search[$s]]) || !$rawreplace[$search[$s]]){
					continue;
				}
				global $tempreplace, $tempevent, $tempsearch, $tempblank;
				$tempreplace = $rawreplace[$search[$s]];
				$tempblank = $blank[$s];
				$tempsearch = str_replace("}}",";.*?}}",$search[$s]);
				$tempevent = $event;
				$template_value = preg_replace_callback("~$tempsearch~", 'jevSpecialDateFormatting', $template_value);
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

		// The universal search and replace to finish
		$template_value = str_replace($search, $replace, $template_value);

		if ($specialmodules)
		{
			$reg =  JRegistry::getInstance("com_jevents");

			$parts = explode("{{MODULESTART#", $template_value);
			$dynamicmodules = array();
			foreach ($parts as $part)
			{
				$currentdynamicmodules = $reg->get("dynamicmodules", false);
				if (JString::strpos($part, "{{MODULEEND}}") === false)
				{
					// strip out BAD HTML tags left by WYSIWYG editors
					if (JString::substr($part, JString::strlen($part) - 3) == "<p>")
					{
						$template_value = JString::substr($part, 0, JString::strlen($part) - 3);
					}
					else
					{
						$template_value = $part;
					}
					continue;
				}
				// start with module name
				$modname = JString::substr($part, 0, JString::strpos($part, "}}"));
				$modulecontent = JString::substr($part, JString::strpos($part, "}}") + 2);
				$modulecontent = JString::substr($modulecontent, 0, JString::strpos($modulecontent, "{{MODULEEND}}"));
				// strip out BAD HTML tags left by WYSIWYG editors
				if (JString::strpos($modulecontent, "</p>") === 0)
				{
					$modulecontent = "<p>x@#" . $modulecontent;
				}
				if (JString::substr($modulecontent, JString::strlen($modulecontent) - 3) == "<p>")
				{
					$modulecontent .= "x@#</p>";
				}

				$modulecontent = str_replace("<p>x@#</p>", "", $modulecontent);
				if (isset($currentdynamicmodules[$modname])){
					if (!is_array($currentdynamicmodules[$modname])){
						$currentdynamicmodules[$modname] = array($currentdynamicmodules[$modname]);
					}
					$currentdynamicmodules[$modname] [] = $modulecontent;
					$dynamicmodules[$modname] = $currentdynamicmodules[$modname];
				}
				else {
					$dynamicmodules[$modname] = $modulecontent;
				}
			}
			$reg->set("dynamicmodules", $dynamicmodules);
		}

		// non greedy replacement - because of the ?
		$template_value = preg_replace_callback('|{{.*?}}|', 'cleanUnpublished', $template_value);

		// replace [[ with { to that other content plugins can work ok - but not for calendar cell or tooltip since we use [[ there already!
		if ($template_name!="month.calendar_cell" && $template_name!="month.calendar_tip"){
			$template_value = str_replace(array("[[","]]"), array("{","}"), $template_value);
		}

		//We add new line characters again to avoid being marked as SPAM when using tempalte in emails
		// do this before calling content plugins in case these add javascript etc. to layout
		$template_value = preg_replace("@(<\s*(br)*\s*\/\s*(p|td|tr|table|div|ul|li|ol|dd|dl|dt)*\s*>)+?@i","$1\n",$template_value);

		// Call content plugins - BUT because emailcloak doesn't identify emails in input fields to a text substitution
		$template_value = str_replace("@", "@£@", $template_value);
		$params = new JRegistry(null);
		$tmprow = new stdClass();
		$tmprow->text = $template_value;
		$tmprow->event = $event;
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onContentPrepare', array('com_jevents', &$tmprow, &$params, 0));
		$template_value = $tmprow->text;
		$template_value = str_replace("@£@", "@", $template_value);

		echo $template_value;
		return !$loadedFromFile;

	}

	function cleanLabels($matches)
	{
		if (count($matches) == 1)
		{
			$parts = explode(":", $matches[0]);
			if (count($parts) > 0)
			{
				if (JString::strpos($matches[0], "://") > 0)
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
		if (count($matches) == 1 && JString::strpos($matches[0], ":") > 0)
		{
			global $tempreplace, $tempevent, $tempsearch;
			$parts = explode(":", $matches[0]);
			if (count($parts) == 2)
			{
				$wordcount = str_replace("}}", "", $parts[1]);
				$charcount = 0;
				if (JString::strpos($wordcount, "chars") > 0)
				{
					$charcount = intval(str_replace("chars", "", $wordcount));
					$wordcount = 0;
				}
				else
				{
					$wordcount = intval($wordcount);
				}
				$value = strip_tags($tempreplace);

				$value = str_replace("  ", " ", $value);
				$words = explode(" ", $value);
				if ($wordcount > 0 && count($words) > $wordcount)
				{
					$words = array_slice($words, 0, $wordcount);
					$words[] = " ...";
					return implode(" ", $words);
				}
				if ($charcount > 0 && JString::strlen($value) > $charcount)
				{
					return JString::substr($value, 0, $charcount) . " ...";
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

	function jevSpecialDateFormatting($matches){
		if (count($matches) == 1 && JString::strpos($matches[0], ";") > 0)
		{
			global $tempreplace, $tempevent, $tempsearch,$tempblank;
			$parts = explode(";", $matches[0]);
			if (count($parts) == 2)
			{
				$fmt = str_replace(array("}}","}"), "", $parts[1]);
				if (strpos($fmt, "#")!==false){
					$fmtparts = explode("#", $fmt);
					if ($tempreplace == $tempblank)
					{
						if (count($fmtparts) == 3)
						{
							$fmt = $fmtparts[2];
						}
						else
							return  "";
					}
					else if (count($fmtparts) >= 2)
					{
						$fmt = sprintf($fmtparts[1], $fmtparts[0]);
					}
				}
				//return strftime($fmt, strtotime(strip_tags($tempreplace)));
				if (!is_int($tempreplace)){
					$tempreplace =strtotime(strip_tags($tempreplace));
				}
				return JEV_CommonFunctions::jev_strftime($fmt, $tempreplace);
			}
			// TZ specified
			else if (count($parts) == 3)
			{
				$fmt = $parts[1];

				// Must get this each time otherwise modules can't set their own timezone
				$compparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$jtz = $compparams->get("icaltimezonelive", "");
				if ($jtz != "")
				{
					$jtz = new DateTimeZone($jtz);
				}
				else
				{
					$jtz = new DateTimeZone(@date_default_timezone_get());
				}
				$outputtz = str_replace(array("}}","}"),"",$parts[2]);

				if (strpos($outputtz, "#")!==false){
					$outputtzparts = explode("#", $outputtz);
					$outputtz = $outputtzparts[0];
					if ($tempreplace == $tempblank)
					{
						if (count($outputtzparts) == 3)
						{
							$fmt = $outputtzparts[2];
						}
						else
							return  "";
					}
					else if (count($outputtzparts) >= 2)
					{
						$fmt = sprintf($outputtzparts[1], $fmt);
					}
				}


				if (strtolower($outputtz) == "user" || strtolower($outputtz) == "usertz"){
					$user = JFactory::getUser();
					$outputtz = $user->getParam("timezone", $compparams->get("icaltimezonelive", @date_default_timezone_get()));
				}
				$outputtz = new DateTimeZone($outputtz);

				if (is_integer($tempreplace)){
					$tempreplace = JEV_CommonFunctions::jev_strftime("%Y-%m-%d %H:%M:%S", $tempreplace);
				}
				$indate = new DateTime($tempreplace, $jtz);
				$offset1 = $indate->getOffset();

				// set the new timezone
				$indate->setTimezone($outputtz);
				$offset2 = $indate->getOffset();;

				$indate = $indate->getTimestamp()+$offset2-$offset1;
				return JEV_CommonFunctions::jev_strftime($fmt, intval($indate));
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
		if (count($matches) == 2 && JString::strpos($matches[0], "#") > 0)
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
