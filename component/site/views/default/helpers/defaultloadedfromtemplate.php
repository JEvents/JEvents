<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\LayoutHelper;

function DefaultLoadedFromTemplate($view, $template_name, $event, $mask, $template_value = false, $runplugins = true, $skipfiles = false)
{
	static $processedCss = array();
	static $processedJs = array();

	$jevparams  = ComponentHelper::getParams(JEV_COM_COMPONENT);
	$db         = Factory::getDbo();
	$app        = Factory::getApplication();
	$input      = $app->input;

	static $allcatids;
	if (!isset($allcatids))
	{
		$query = $db->getQuery(true);

		$query->select('a.id, a.parent_id');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');

		// Filter on extension.
		$query->where('a.extension = "com_jevents"');
		$query->where('a.published = 1');
		$query->where('a.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		$query->order('a.lft');

		$db->setQuery($query);
		$allcatids = $db->loadObjectList('id');
	}

	// find published template
	static $templates;
	static $fieldNameArray;
	if (!isset($templates))
	{
		$templates      = array();
		$fieldNameArray = array();
		$rawtemplates   = array();
	}
	$specialmodules = false;
	static $allcat_catids;
	$loadedFromFile = false;

	if (!$template_value)
	{
		if (!array_key_exists($template_name, $templates))
		{

			$db->setQuery("SELECT * FROM #__jev_defaults WHERE state=1 AND name= " . $db->Quote($template_name) . " AND value<>'' AND " . 'language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			$rawtemplates              = $db->loadObjectList();
			$templates[$template_name] = array();
			if ($rawtemplates)
			{
				foreach ($rawtemplates as $rt)
				{
					if (!isset($templates[$template_name][$rt->language]))
					{
						$templates[$template_name][$rt->language] = array();
					}
					$templates[$template_name][$rt->language][$rt->catid] = $rt;
				}
			}

			if (!isset($templates[$template_name]['*'][0]) && !$skipfiles)
			{
				try
				{
					if ($view !== false)
					{
						$viewname = $view->getViewName();
					}
					else
					{
						$viewname = "default";
					}
				}
				catch (Exception $e)
				{
					$viewname = "default";
				}
				$templatefile = JPATH_BASE . '/' . 'templates' . '/' . $app->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT . "/$viewname/defaults/$template_name.html";
				if (!File::exists($templatefile))
				{
					$templatefile = JPATH_BASE . '/' . 'templates' . '/' . $app->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT . "/defaults/$template_name.html";
				}
				if (!File::exists($templatefile))
				{
					$templatefile = JEV_VIEWS . "/$viewname/defaults/$template_name.html";
				}
				if (!File::exists($templatefile))
				{
					$templatefile = JEV_ADMINPATH . "views/defaults/tmpl/$template_name.html";
				}
				// Fall back to html version
				if (File::exists($templatefile))
				{
					$loadedFromFile = true;
					if (!isset($templates[$template_name]['*']))
					{
						$templates[$template_name]['*'] = array();
					}
					$templates[$template_name]['*'][0]        = new stdClass();
					$templates[$template_name]['*'][0]->value = file_get_contents($templatefile);

					$templateparams = new stdClass();
					// is there custom css or js - if so push into the params
					if (strpos($templates[$template_name]['*'][0]->value, '{{CUSTOMJS}') !== false)
					{
						preg_match('|' . preg_quote('{{CUSTOMJS}}') . '(.+?)' . preg_quote('{{/CUSTOMJS}}') . '|s', $templates[$template_name]['*'][0]->value, $matches);

						if (count($matches) == 2)
						{
							$templateparams->customjs                 = $matches[1];
							$templates[$template_name]['*'][0]->value = str_replace($matches[0], "", $templates[$template_name]['*'][0]->value);
						}
					}
					if (strpos($templates[$template_name]['*'][0]->value, '{{CUSTOMCSS}') !== false)
					{
						preg_match('|' . preg_quote('{{CUSTOMCSS}}') . '(.+?)' . preg_quote('{{/CUSTOMCSS}}') . '|s', $templates[$template_name]['*'][0]->value, $matches);

						if (count($matches) == 2)
						{
							$templateparams->customcss                = $matches[1];
							$templates[$template_name]['*'][0]->value = str_replace($matches[0], "", $templates[$template_name]['*'][0]->value);
						}
					}
					if (isset($templateparams->customcss) && !empty($templateparams->customcss))
					{
						if (!in_array($templateparams->customcss, $processedCss))
						{
							$processedCss[] = $templateparams->customcss;
							//Factory::getDocument()->addStyleDeclaration($templateparams->customcss);
						}
					}
					if (isset($templateparams->customjs) && !empty($templateparams->customjs))
					{
						if (!in_array($templateparams->customjs, $processedJs))
						{
							$processedJs[] = $templateparams->customjs;
							Factory::getDocument()->addScriptDeclaration($templateparams->customjs);
						}
					}

					$templates[$template_name]['*'][0]->params   = json_encode($templateparams);
					$templates[$template_name]['*'][0]->fromfile = true;
				}
				else
				{
					return false;
				}
			}

			if (isset($templates[$template_name][Factory::getLanguage()->getTag()]))
			{
				$templateArray = $templates[$template_name][Factory::getLanguage()->getTag()];
				// We have the most specific by language now fill in the gaps
				if (isset($templates[$template_name]["*"]))
				{
					foreach ($templates[$template_name]["*"] as $cat => $cattemplates)
					{
						if (!isset($templateArray[$cat]))
						{
							$templateArray[$cat] = $cattemplates;
						}
					}
				}
				$templates[$template_name] = $templateArray;
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

			$hasLocationOrIsOnline = false;
			$onlineevent = $jevparams->get("sevd_onlineeventfield", 0);
			if ($onlineevent !== 0 && isset($event->customfields) && isset($event->customfields[$onlineevent]) && !empty($event->customfields[$onlineevent]['value']))
			{
				$hasLocationOrIsOnline = true;
			}
			if (isset($event->_jevlocation)
				&& !empty($event->_jevlocation))
			{
				$hasLocationOrIsOnline = true;
			}

			$matched = false;
			foreach (array_keys($templates[$template_name]) as $catid)
			{
				if ($templates[$template_name][$catid]->value != "")
				{
					// Add structured data output
					if ($template_name === "icalevent.detail_body"
						&& $jevparams->get("enable_gsed", 0)
						&& $jevparams->get("sevd_imagename", 0)
						&& $jevparams->get("permatarget", 0)
						&& $hasLocationOrIsOnline
					)
					{
						$templates[$template_name][$catid]->value .= "<script type='application/ld+json'>{{Structured Data:LDJSON}}</script>";
					}

					if (isset($templates[$template_name][$catid]->params))
					{
						$templates[$template_name][$catid]->params = new JevRegistry($templates[$template_name][$catid]->params);
						$specialmodules                            = $templates[$template_name][$catid]->params;

					}

					// Adjust template_value to include dynamic module output then strip it out afterwards
					if ($specialmodules)
					{
						$modids = $specialmodules->get("modid", array());
						if (count($modids) > 0)
						{
							$modvals = $specialmodules->get("modval", array());
							// not sure how this can arise :(
							if (is_object($modvals))
							{
								$modvals = get_object_vars($modvals);
							}
							$modids  = array_values($modids);
							$modvals = array_values($modvals);

							for ($count = 0; $count < count($modids) && $count < count($modvals) && trim($modids[$count]) != ""; $count++)
							{
								$templates[$template_name][$catid]->value .= "{{module start:MODULESTART#" . $modids[$count] . "}}";
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

					$customcss = $templates[$template_name][$catid]->params->get('customcss', '');
					$customcss = preg_replace_callback('|{{.*?}}|', 'cleanLabels', $customcss);

					$matchesarray = array();
					preg_match_all('|{{.*?}}|', $templates[$template_name][$catid]->value . $customcss, $matchesarray);

					$templates[$template_name][$catid]->matchesarray = $matchesarray;
				}
			}
		}

		if (is_null($templates[$template_name]))
		{
			return false;
		}

		if ($event === null)
		{
			return $templates[$template_name];
		}

		$catids = ($event->catids() && count($event->catids())) ? $event->catids() : array($event->catid());
		$catids[] = 0;

		// find the overlap
		$catids = array_values(array_intersect($catids, array_keys($templates[$template_name])));

		// If no categories match - check for parent, one level
		if (count($catids) == 0 || (count($catids) == 1 && $catids[0] == 0))
		{
			$catids   = ($event->catids() && count($event->catids())) ? $event->catids() : array($event->catid());
			$catcount = count($catids);
			for ($c = 0; $c < $catcount; $c++)
			{
				if (isset($allcatids[$catids[$c]]) && $allcatids[$catids[$c]]->parent_id > 0)
				{
					$catids[] = $allcatids[$catids[$c]]->parent_id;
				}
			}
			$catids[] = 0;
			// find the overlap
			$catids = array_values(array_intersect($catids, array_keys($templates[$template_name])));

			// If no categories match - check for parent, one level
			if (count($catids) == 0 || (count($catids) == 1 && $catids[0] == 0))
			{
				$catids   = ($event->catids() && count($event->catids())) ? $event->catids() : array($event->catid());
				$catcount = count($catids);
				for ($c = 0; $c < $catcount; $c++)
				{
					if (isset($allcatids[$catids[$c]]) && $allcatids[$catids[$c]]->parent_id > 0)
					{
						$catids[] = $allcatids[$catids[$c]]->parent_id;
					}
				}
				$catids[] = 0;
				// find the overlap
				$catids = array_values(array_intersect($catids, array_keys($templates[$template_name])));
			}
		}

		// At present must be an EXACT category match - no inheriting allowed!
		if (count($catids) == 0)
		{
			if (!isset($templates[$template_name][0]) || $templates[$template_name][0]->value == "")
			{
				return false;
			}
		}

		$template = false;
		foreach ($catids as $catid)
		{
			// use the first matching non-empty layout
			if ($templates[$template_name][$catid]->value != "")
			{
				$template = $templates[$template_name][$catid];
				break;
			}
		}
		if (!$template)
		{
			return false;
		}

		$template_value = $template->value;
		$specialmodules = $template->params;

		$matchesarray   = $template->matchesarray;
		$loadedFromFile = isset($template->fromfile);

		$customcss = $template->params->get('customcss', '');
		if (!in_array($customcss, $processedCss))
		{
			$processedCss[] = $customcss;
			//Factory::getDocument()->addStyleDeclaration($customcss);
		}

		$customjs = $template->params->get('customjs', '');
		if (!in_array($customjs, $processedJs))
		{
			$processedJs[] = $customjs;
			Factory::getDocument()->addScriptDeclaration($customjs);
		}

	}
	else
	{
		if ($runplugins && $input->getString("option") != "com_jevents")
		{
			// This is a special scenario where we call this function externally e.g. from RSVP Pro messages
			// In this scenario we have not gone through the displaycustomfields plugin
			static $pluginscalled = array();
			if (!isset($pluginscalled[$event->rp_id()]))
			{
				PluginHelper::importPlugin("jevents");
				$customresults                  = Factory::getApplication()->triggerEvent('onDisplayCustomFields', array(&$event));
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
			if (count($modids) > 0)
			{
				$modvals = $specialmodules->get("modval", array());
				// not sure how this can arise :(
				if (is_object($modvals))
				{
					$modvals = get_object_vars($modvals);
				}
				$modids  = array_values($modids);
				$modvals = array_values($modvals);

				for ($count = 0; $count < count($modids) && $count < count($modvals) && trim($modids[$count]) != ""; $count++)
				{
					$template_value .= "{{module start:MODULESTART#" . $modids[$count] . "}}";
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

		$templateparams = new stdClass();
		// is there custom css or js - if so push into the params
		if (strpos($template_value, '{{CUSTOMJS}') !== false)
		{
			preg_match('|' . preg_quote('{{CUSTOMJS}}') . '(.*?)' . preg_quote('{{/CUSTOMJS}}') . '|s', $template_value, $matches);

			if (count($matches) == 2)
			{
				$templateparams->customjs = $matches[1];
				$template_value = str_replace($matches[0], "",	$template_value);
			}
		}
		if (strpos($template_value, '{{CUSTOMCSS}') !== false)
		{
			preg_match('|' . preg_quote('{{CUSTOMCSS}}') . '(.*?)' . preg_quote('{{/CUSTOMCSS}}') . '|s', $template_value, $matches);

			if (count($matches) == 2)
			{
				$templateparams->customcss = $matches[1];
				$template_value = str_replace($matches[0], "",	$template_value);
			}
		}
		if (isset($templateparams->customcss) && !empty($templateparams->customcss) )
		{
			if (!in_array($templateparams->customcss, $processedCss))
			{
				$processedCss[] = $templateparams->customcss;
				//Factory::getDocument()->addStyleDeclaration($templateparams->customcss);
			}
		}
		if (isset($templateparams->customjs) && !empty($templateparams->customjs) )
		{
			if (!in_array($templateparams->customjs, $processedJs))
			{
				$processedJs[] = $templateparams->customjs;
				Factory::getDocument()->addScriptDeclaration($templateparams->customjs);
			}
		}

		// non greedy replacement - because of the ?
		$template_value = preg_replace_callback('|{{.*?}}|', 'cleanLabels', $template_value . implode("\n", $processedCss));

		$matchesarray = array();
		preg_match_all('|{{.*?}}|', $template_value, $matchesarray);
	}
	if ($template_value == "")
		return;
	if (count($matchesarray) == 0)
		return;

// now replace the fields
	$search     = array();
	$replace    = array();
	$blank      = array();
	$rawreplace = array();

	$jevparams = ComponentHelper::getParams(JEV_COM_COMPONENT);

	for ($i = 0; $i < count($matchesarray[0]); $i++)
	{
		$strippedmatch = preg_replace('/(#|:|;)+[^}]*/', '', $matchesarray[0][$i]);

		if (in_array($strippedmatch, $search, false))
		{
			continue;
		}
		// translation string
		if (StringHelper::strpos($strippedmatch, "{{_") === 0 && StringHelper::strpos($strippedmatch, " ") === false)
		{
			$search[]      = $strippedmatch;
			$strippedmatch = StringHelper::substr($strippedmatch, 3, StringHelper::strlen($strippedmatch) - 5);
			$replace[]     = Text::_($strippedmatch);
			$blank[]       = "";
			continue;
		}
		// Built in fields
		switch ($strippedmatch)
		{
			case "{{ATTACH}}":
				$search[]  = "{{ATTACH}}";
				if (isset($event->_evrawdata) && !empty($event->_evrawdata))
				{
					try
					{
						$rawdata = unserialize($event->_evrawdata);
						if (isset($rawdata["ATTACH"]) && count($rawdata["ATTACH"]))
						{
							$replace[] = $rawdata["ATTACH"][0];
						}
					}
					catch (Exception $e)
					{
						$replace[] = "";
					}
				}
				else
				{
					$replace[] = "";
				}
				$blank[]   = "";
				break;
			case "{{TITLE}}":
				$search[]  = "{{TITLE}}";
				$replace[] = $event->title();
				$blank[]   = "";
				break;
			case "{{TRUNCATED_TITLE}}":
				$search[]  = "{{TRUNCATED_TITLE:.*?}}";
				$replace[] = $event->title();
				$blank[]   = "";
				break;
			case "{{PRIORITY}}":
				$search[]  = "{{PRIORITY}}";
				$replace[] = $event->priority();
				$blank[]   = "";
				break;

			case "{{LINK}}":
			case "{{LINKSTART}}":
			case "{{LINKEND}}":
			case "{{TITLE_LINK}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array("{{LINK}}", $search, false))
				{
					// Title link
					$rowlink = $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), false);
					if ($view)
					{
						$rowlink = Route::_($rowlink . $view->datamodel->getCatidsOutLink());
					}
					ob_start();
					?>
                    <a class="ev_link_row" href="<?php echo $rowlink; ?>" title="<?php echo JEventsHTML::special($event->title()); ?>">
					<?php
					$linkstart = ob_get_clean();
					$search[]  = "{{LINK}}";
					$replace[] = $rowlink;
					$blank[]   = "";
					$search[]  = "{{LINKSTART}}";
					$replace[] = $linkstart;
					$blank[]   = "";
					$search[]  = "{{LINKEND}}";
					$replace[] = "</a>";
					$blank[]   = "";

					$fulllink  = $linkstart . $event->title() . '</a>';
					$search[]  = "{{TITLE_LINK}}";
					$replace[] = $fulllink;
					$blank[]   = "";
				}
				break;

			case "{{TRUNCTITLE}}":

				// for month calendar cell only
				if (isset($event->truncatedtitle))
				{
					$search[]  = "{{TRUNCTITLE}}";
					$replace[] = $event->truncatedtitle;
					$blank[]   = "";
				}
				else
				{
					$search[]  = "{{TRUNCTITLE}}";
					$replace[] = $event->title();
					$blank[]   = "";
				}

				break;

			case "{{URL}}":
				$search[]  = "{{URL}}";
				$replace[] = $event->url();
				$blank[]   = "";
				break;

			case "{{TRUNCATED_DESC}}":
				$search[]  = "{{TRUNCATED_DESC:.*?}}";
				$replace[] = $event->content();
				$blank[]   = "";
				//	$search[]="|{{TRUNCATED_DESC:(.*)}}|";$replace[]=$event->content();
				break;

			case "{{DESCRIPTION_ADDSLASHES}}":
				$search[]  = "{{DESCRIPTION_ADDSLASHES}}";
				$replace[] = addslashes($event->content());
				$blank[]   = "";
				break;

			case "{{DESCRIPTION}}":
				$search[]  = "{{DESCRIPTION}}";
				$replace[] = $event->content();
				$blank[]   = "";
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
				$search[]  = "{{CATEGORY}}";
				$replace[] = $event->catname();
				$blank[]   = "";
				break;
			case "{{CATEGORY_ALIAS}}":
				$db     = Factory::getDbo();
				$catsql = "SELECT alias FROM #__categories WHERE extension = 'com_jevents' AND id = '" . $event->catid() . "'";
				$db->setQuery($catsql);
				$cat_alias = $db->loadResult();
				$search[]  = "{{CATEGORY_ALIAS}}";
				$replace[] = $cat_alias;
				$blank[]   = "";
				break;

			case "{{ALLCATEGORIES}}":
				$search[] = "{{ALLCATEGORIES}}";

				if (!isset($allcat_catids))
				{
					$db         = Factory::getDbo();
					$catsql     = "SELECT cat.id, cat.title as name, cat.params FROM #__categories  as cat WHERE cat.extension='com_jevents' ";
					$db->setQuery($catsql);
					$allcat_catids = $db->loadObjectList('id');
				}
				$db = Factory::getDbo();
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
				$blank[]   = "";
				break;

			case "{{ALLCATEGORIESLUGS}}":
				$search[] = "{{ALLCATEGORIESLUGS}}";

				if (!isset($allcat_catids))
				{
					$db         = JFactory::getDbo();
					$catsql     = "SELECT cat.id, cat.title AS name, cat.alias AS slug, cat.params FROM #__categories  AS cat WHERE cat.extension='com_jevents' ";
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
						$allcats[] = 'jevcat-' . $allcat_catids[$catid]->slug;
					}
				}
				$replace[] = implode(" ", $allcats);
				$blank[]   = "";
				break;

			case "{{ALLCATEGORIES_CAT_COLOURED}}":
                $search[] = "{{ALLCATEGORIES_CAT_COLOURED}}";

                if (!isset($allcat_catids))
                {
                    $db         = Factory::getDbo();
                    $catsql     = "SELECT cat.id, cat.title as name, cat.params FROM #__categories  as cat WHERE cat.extension='com_jevents' ";
                    $db->setQuery($catsql);
                    $allcat_catids = $db->loadObjectList('id');
                }
                $db = Factory::getDbo();
                $db->setQuery("Select catid from #__jevents_catmap  WHERE evid = " . $event->ev_id());
                $allcat_eventcats = $db->loadColumn();

                $allcats = array();
                foreach ($allcat_eventcats as $catid)
                {
                    if (isset($allcat_catids[$catid]))
                    {
                        $params    = json_decode($allcat_catids[$catid]->params);
                        $style = '';

                        if(!empty($params->catcolour)) {
                            $style = ' style="color:' . $params->catcolour . ';"';
                        }
                        $allcats[] = '<span ' . $style . '>' . $allcat_catids[$catid]->name . '</span>';
                    }
                }
                $replace[] = implode(", ", $allcats);
                $blank[]   = "";
                break;

            case "{{CALENDAR}}":
				$search[]  = "{{CALENDAR}}";
				$replace[] = $event->getCalendarName();
				$blank[]   = "";
				break;

			case "{{COLOUR}}":
			case "{{colour}}":
				$bgcolor   = $event->bgcolor();
				$search[]  = $strippedmatch;
				$replace[] = $bgcolor == "" ? "#ffffff" : $bgcolor;
				$blank[]   = "";
				break;

			case "{{RGBA}}":
				$bgcolor  = $event->bgcolor();
				$search[] = $strippedmatch;
				$bgcolor  = $bgcolor == "" ? "#ffffff" : $bgcolor;
				// skip the #
				if (strlen($bgcolor) == 7)
				{
					$bgcolor = substr($bgcolor, 1);
				}
				if (strlen($bgcolor) == 6)
					list($r, $g, $b) = array($bgcolor[0] . $bgcolor[1],
						$bgcolor[2] . $bgcolor[3],
						$bgcolor[4] . $bgcolor[5]);
                elseif (strlen($bgcolor) == 3)
					list($r, $g, $b) = array($bgcolor[0] . $bgcolor[0], $bgcolor[1] . $bgcolor[1], $bgcolor[2] . $bgcolor[2]);
				else
					return false;

				$r         = hexdec($r);
				$g         = hexdec($g);
				$b         = hexdec($b);
				$replace[] = "rgba($r, $g, $b, 0.3)";

				$blank[] = "";
				break;

			case "{{FGCOLOUR}}":
				$search[]  = "{{FGCOLOUR}}";
				$replace[] = $event->fgcolor();
				$blank[]   = "";
				break;

			case "{{TTTIME}}":
				$search[]  = "{{TTTIME}}";
				$replace[] = "[[TTTIME]]";
				$blank[]   = "";
				break;

			case "{{EVTTIME}}":
				$search[]  = "{{EVTTIME}}";
				$replace[] = "[[EVTTIME]]";
				$blank[]   = "";
				break;

			// deprecated
			case "{{TOOLTIP}}":
				$search[]  = "{{TOOLTIP}}";
				$replace[] = "[[TOOLTIP]]";
				$blank[]   = "";
				break;

			// new version for bootstrap
			case "{{TOOLTIPTITLE}}":
				$search[]  = "{{TOOLTIPTITLE}}";
				$replace[] = "[[TOOLTIPTITLE]]";
				$blank[]   = "";
				break;

			case "{{TOOLTIPCONTENT}}":
				$search[]  = "{{TOOLTIPCONTENT}}";
				$replace[] = "[[TOOLTIPCONTENT]]";
				$blank[]   = "";
				break;

			case "{{CATEGORYLNK}}" :
			case "{{CATEGORYLINK_RAW}}":
				$router   = Router::getInstance("site");
				$catlinks = array();
				if ($jevparams->get("multicategory", 0))
				{
					$catids  = $event->catids();
					$catdata = $event->getCategoryData();
				}
				else
				{
					$catids  = array($event->catid());
					$catdata = array($event->getCategoryData());
				}
				// Is this being called from the latest events module - if so then use the target item instead of current Itemid
				$reg       = JevRegistry::getinstance("jevents");
				$modparams = $reg->get("jevents.moduleparams", new Registry);
				$modItemid = $modparams->get("target_itemid", Factory::getApplication()->input->getInt("Itemid", 0));
				$menuItem  = Factory::getApplication()->getMenu('site')->getItem($modItemid);
				$vars      = $menuItem->query;
				foreach ($catids as $cat)
				{
					$vars["catids"] = $cat;
					$catname        = "xxx";
					foreach ($catdata as $cg)
					{
						if ($cat == $cg->id)
						{
							$catname = $cg->name;
							break;
						}
					}
					$eventlink = "index.php?";
					$itemidSet = false;
					$hastask   = false;
					$task      = "";
					foreach ($vars as $key => $val)
					{
						// this is only used in the latest events module so do not perpetuate it here
						if ($key == "filter_reset")
							continue;
						if ($key == "task")
						{
							$hastask = true;
							$task    = $val;
						}
						if ($key == "view")
						{
							$task = $val;
						}
						if ($key == "layout")
						{
							$task .= "." . $val;
						}

						if ($key == "task" && ($val == "icalrepeat.detail" || $val == "icalevent.detail"))
						{
							$val = "cat.listevents";
						}
						if ($key == "Itemid" && $modItemid)
						{
							$val       = $modItemid;
							$itemidSet = true;
						}
						$eventlink .= $key . "=" . $val . "&";
					}
					if (!$hastask && $task)
					{
						$eventlink .= "task=" . $task . "&";
					}
					if (!$itemidSet && $modItemid)
					{
						$eventlink .= "Itemid=" . $modItemid . "&";
					}
					$eventlink = StringHelper::substr($eventlink, 0, StringHelper::strlen($eventlink) - 1);

					$eventlink = Route::_($eventlink);

					$catlinks_raw[] = $eventlink;

					$catlinks[] = '<a class="ev_link_cat" href="' . $eventlink . '"  title="' . JEventsHTML::special($catname) . '">' . $catname . '</a>';
				}
				$search[]  = "{{CATEGORYLNK_RAW}}";
				$replace[] = implode(", ", $catlinks_raw);
				$blank[]   = "";

				$search[]  = "{{CATEGORYLNK}}";
				$replace[] = implode(", ", $catlinks);
				$blank[]   = "";
				break;

			case "{{CATEGORYIMG}}":
				$search[]  = "{{CATEGORYIMG}}";
				$replace[] = $event->getCategoryImage();
				$blank[]   = "";
				break;

			case "{{CATEGORYIMGS}}":
				$search[]  = "{{CATEGORYIMGS}}";
				$replace[] = $event->getCategoryImage(true);
				$blank[]   = "";
				break;

			case "{{ALLCATEGORYIMGS}}":
				$search[] = "{{ALLCATEGORYIMGS}}";
				if (!isset($allcat_catids))
				{
					$db         = Factory::getDbo();
					$catsql     = "SELECT cat.id, cat.title as name, cat.params FROM #__categories  as cat WHERE cat.extension='com_jevents' ";
					$db->setQuery($catsql);
					$allcat_catids = $db->loadObjectList('id');
				}

				$db = Factory::getDbo();
				$db->setQuery("Select catid from #__jevents_catmap  WHERE evid = " . $event->ev_id());
				$allcat_eventcats = $db->loadColumn();

				$output = "";
				if (is_array($allcat_eventcats))
				{
					foreach ($allcat_eventcats as $catid)
					{
						if (isset($allcat_catids[$catid]))
						{
							$params = json_decode($allcat_catids[$catid]->params);
							if (isset($params->image) && $params->image != "")
							{
								$alt_text = ($params->image_alt == '') ? Text::_('JEV_CAT_ALT_DEFAULT_TEXT', true) : $params->image_alt;
								$output   .= "<img src = '" . Uri::root() . $params->image . "' class='catimage'  alt='" . $alt_text . "' />";
							}
						}
					}
				}

				$replace[] = $output;
				$blank[]   = "";
				break;

			case "{{CATDESC}}":
				$search[]  = "{{CATDESC}}";
				$replace[] = $event->getCategoryDescription();
				$blank[]   = "";
				break;
			case "{{CATID}}":
				$search[]  = "{{CATID}}";
				$replace[] = $event->catid();
				$blank[]   = "";
				break;
			case "{{PARENT_CATEGORY}}":
				$search[]  = "{{PARENT_CATEGORY}}";
				$replace[] = $event->getParentCategory();
				$blank[]   = "";
				break;

			case "{{ICALDIALOG}}":
			case "{{ICALBUTTON}}":
			case "{{EDITDIALOG}}":
			case "{{EDITBUTTON}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array("{{EDITBUTTON}}", $search, false))
				{

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
					if ((JEVHelper::canEditEvent($event) || JEVHelper::canPublishEvent($event) || JEVHelper::canDeleteEvent($event)))
					{
						ob_start();
						$view->eventManagementButton($event);
						$button = ob_get_clean();
						ob_start();
						echo $button;
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
							$dialog = "";
							$replace[] = "";
						}
						$blank[] = "";
						echo $dialog;
						?>
						</div>

						<?php
						$search[] = "{{EDITBUTTON}}";
						if (!empty($dialog))
						{
						$replace[] = ob_get_clean();
						}
						else {
						 $junk = ob_get_clean();
						 $replace[] = $button;
						}
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
				$jtz        = $jevparams->get("icaltimezonelive", "");
				if ($jtz == "")
				{
					$jtz = null;
				}
				$created   = JevDate::getDate($event->created(), $jtz);
				$search[]  = "{{CREATED}}";
				$replace[] = $created->toFormat(Text::_("DATE_FORMAT_CREATED"));
				$blank[]   = "";
				break;
			case "{{ICALSAVE}}":
				$search[] = "{{ICALSAVE}}";
				$replace[] = $event->vCalExportLink(false, true);
				$blank[] = "";
			    break;;

			case "{{ICALGOOGLE}}":
				$search[] = "{{ICALGOOGLE}}";
				include_once JEV_HELPERS.'/jevExportHelper.php';
				$replace[] = JevExportHelper::getAddToGCal($event);
				$blank[] = "";
				break;

			case "{{ICALOUTLOOKLIVE}}":
				$search[] = "{{ICALOUTLOOKLIVE}}";
				include_once JEV_HELPERS.'/jevExportHelper.php';
				$replace[] = JevExportHelper::getAddToOutlookLive($event);
				$blank[] = "";
				break;

			case "{{ICALOUTLOOK}}":
				$search[] = "{{ICALOUTLOOK}}";
				include_once JEV_HELPERS.'/jevExportHelper.php';
				$replace[] = JevExportHelper::getAddToMsOutlook($event);
				$blank[] = "";
				break;

			case "{{ACCESS}}":
				$search[]  = "{{ACCESS}}";
				$replace[] = $event->getAccessName();
				$blank[]   = "";
				break;
/*
			case "{{JOOMLATAGS}}":
				$search[] = "{{JOOMLATAGS}}";
				if (!empty($event->tags->itemTags))
				{
					$replace[]        = LayoutHelper::render('joomla.content.tags', $event->tags->itemTags);
				}
				else
				{
					$replace[]        = "";
				}
				$replace[] = $event->getAccessName();
				$blank[]   = "";
				break;
*/
			case "{{JEVSTARTED}}":
			case "{{JEVENDED}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array("{{JEVSTARTED}}", $search, false))
				{

					$search[] = "{{JEVSTARTED}}";

					$now = new JevDate("+0 seconds");
					$now = $now->toFormat("%Y-%m-%d %H:%M:%S");

					$replace[] = $event->publish_up() < $now ? Text::_("JEV_EVENT_STARTED") : "";
					$blank[]   = "";
					$search[]  = "{{JEVENDED}}";
					$replace[] = $event->publish_down() < $now ? Text::_("JEV_EVENT_ENDED") : "";
					$blank[]   = "";
				}
				break;

			case "{{TODAY}}" :
			case "{{TOMORROW}}" :
				if(strtotime($event->startDate()) === strtotime(date( 'Y-m-d'))) {
					$search[]   = '{{TODAY}}';
					$replace[]  = JText::_('JEV_EVENT_TODAY');
				}

				if(strtotime($event->startDate()) === strtotime(date( 'Y-m-d') . '+1 day')) {
					$search[]   = '{{TOMORROW}}';
					$replace[]  = JText::_('JEV_EVENT_TOMORROW');
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
			case "{{DURATION_ROUNDUP}}":
			case "{{COUNTDOWN}}":
			case "{{PAST_OR_FUTURE}}":
			case "{{MULTIENDDATE}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array("{{COUNTDOWN}}", $search, false))
				{
					if ($template_name == "icalevent.detail_body")
					{
						$search[]      = "{{REPEATSUMMARY}}";
						$repeatsummary = $view->repeatSummary($event);
						if (!$repeatsummary)
						{
							$repeatsummary = $event->repeatSummary();
						}
						if ($jevparams->get("com_repeatview", 1))
						{
							$replace[] = $repeatsummary;
						}
						else
						{
							$replace[] = "";
						}
						//$replace[] = $event->repeatSummary();
						$blank[]               = "";
						$row                   = $event;
						$start_date            = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 0);
						$start_time            = JEVHelper::getTime($row->getUnixStartTime(), $row->hup(), $row->minup());
						$stop_date             = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), 0);
						$stop_time             = JEVHelper::getTime($row->getUnixEndTime(), $row->hdn(), $row->mindn());
						$stop_time_midnightFix = $stop_time;
						$stop_date_midnightFix = $stop_date;
						if ($row->sdn() == 59 && $row->mindn() == 59)
						{
							$stop_time_midnightFix = JEVHelper::getTime($row->getUnixEndTime() + 1, 0, 0);
							$stop_date_midnightFix = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn() + 1, 0);
						}

						$search[]  = "{{STARTDATE}}";
						$replace[] = $start_date;
						$blank[]   = "";
						$search[]  = "{{ENDDATE}}";
						$replace[] = $stop_date;
						$blank[]   = "";
						$search[]  = "{{STARTTIME}}";
						$replace[] = $row->alldayevent() ? "" : $start_time;
						$blank[]   = "";
						$search[]  = "{{ENDTIME}}";
						$replace[] = ($row->noendtime() || $row->alldayevent()) ? "" : $stop_time_midnightFix;
						$blank[]   = "";
						$search[]  = "{{MULTIENDDATE}}";
						$replace[] = $row->endDate() > $row->startDate() ? $stop_date : "";
						$blank[]   = "";
						$search[]  = "{{STARTTZ}}";
						$replace[] = $row->alldayevent() ? "" : $start_time;
						$blank[]   = "";
						$search[]  = "{{ENDTZ}}";
						$replace[] = ($row->noendtime() || $row->alldayevent()) ? "" : $stop_time_midnightFix;
						$blank[]   = "";

						$rawreplace["{{STARTDATE}}"]    = $row->getUnixStartDate();
						$rawreplace["{{ENDDATE}}"]      = $row->getUnixEndDate();
						$rawreplace["{{STARTTIME}}"]    = $row->alldayevent() ? "" : $row->getUnixStartTime();
						$rawreplace["{{ENDTIME}}"]      = ($row->noendtime() || $row->alldayevent()) ? "" : $row->getUnixEndTime();
						$rawreplace["{{STARTTZ}}"]      = $row->yup() . "-" . $row->mup() . "-" . $row->dup() . " " . $row->hup() . ":" . $row->minup() . ":" . $row->sup();
						$rawreplace["{{ENDTZ}}"]        = $row->ydn() . "-" . $row->mdn() . "-" . $row->ddn() . " " . $row->hdn() . ":" . $row->mindn() . ":" . $row->sdn();
						$rawreplace["{{MULTIENDDATE}}"] = $row->endDate() > $row->startDate() ? $row->getUnixEndDate() : "";

						if (StringHelper::strpos($template_value, "{{ISOSTART}}") !== false || StringHelper::strpos($template_value, "{{ISOEND}}") !== false)
						{
							$search[]  = "{{ISOSTART}}";
							$replace[] = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hup(), $row->minup());
							$blank[]   = "";
							$search[]  = "{{ISOEND}}";
							$replace[] = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hdn(), $row->mindn());
							$blank[]   = "";
						}
					}
					else
					{
						$row                   = $event;
						$start_date            = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 0);
						$start_time            = JEVHelper::getTime($row->getUnixStartTime(), $row->hup(), $row->minup());
						$stop_date             = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), 0);
						$stop_time             = JEVHelper::getTime($row->getUnixEndTime(), $row->hdn(), $row->mindn());
						$stop_time_midnightFix = $stop_time;
						$stop_date_midnightFix = $stop_date;
						if ($row->sdn() == 59 && $row->mindn() == 59)
						{
							$stop_time_midnightFix = JEVHelper::getTime($row->getUnixEndTime() + 1, 0, 0);
							$stop_date_midnightFix = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn() + 1, 0);
						}
						$search[]  = "{{STARTDATE}}";
						$replace[] = $start_date;
						$blank[]   = "";
						$search[]  = "{{ENDDATE}}";
						$replace[] = $stop_date;
						$blank[]   = "";
						$search[]  = "{{STARTTIME}}";
						$replace[] = $row->alldayevent() ? "" : $start_time;
						$blank[]   = "";
						$search[]  = "{{ENDTIME}}";
						$replace[] = ($row->noendtime() || $row->alldayevent()) ? "" : $stop_time_midnightFix;
						$blank[]   = "";
						$search[]  = "{{MULTIENDDATE}}";
						$replace[] = $row->endDate() > $row->startDate() ? $stop_date : "";
						$blank[]   = "";
						$search[]  = "{{STARTTZ}}";
						$replace[] = $row->alldayevent() ? "" : $start_time;
						$blank[]   = "";
						$search[]  = "{{ENDTZ}}";
						$replace[] = ($row->noendtime() || $row->alldayevent()) ? "" : $stop_time_midnightFix;
						$blank[]   = "";

						$rawreplace["{{STARTDATE}}"]    = $row->getUnixStartDate();
						$rawreplace["{{ENDDATE}}"]      = $row->getUnixEndDate();
						$rawreplace["{{STARTTIME}}"]    = $row->alldayevent() ? "" : $row->getUnixStartTime();
						$rawreplace["{{ENDTIME}}"]      = ($row->noendtime() || $row->alldayevent()) ? "" : $row->getUnixEndTime();
						$rawreplace["{{STARTTZ}}"]      = $row->yup() . "-" . $row->mup() . "-" . $row->dup() . " " . $row->hup() . ":" . $row->minup() . ":" . $row->sup();
						$rawreplace["{{ENDTZ}}"]        = $row->ydn() . "-" . $row->mdn() . "-" . $row->ddn() . " " . $row->hdn() . ":" . $row->mindn() . ":" . $row->sdn();
						$rawreplace["{{MULTIENDDATE}}"] = $row->endDate() > $row->startDate() ? $row->getUnixEndDate() : "";

						if (StringHelper::strpos($template_value, "{{ISOSTART}}") !== false || StringHelper::strpos($template_value, "{{ISOEND}}") !== false)
						{
							$search[]  = "{{ISOSTART}}";
							$replace[] = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hup(), $row->minup());
							$blank[]   = "";
							$search[]  = "{{ISOEND}}";
							$replace[] = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hdn(), $row->mindn());
							$blank[]   = "";
						}

						// these would slow things down if not needed in the list
						$dorepeatsummary = (StringHelper::strpos($template_value, "{{REPEATSUMMARY}}") !== false);

						if ($dorepeatsummary)
						{

							$cfg     = JEVConfig::getInstance();
							$jevtask = $input->getString("jevtask");
							$jevtask = str_replace(".listevents", "", $jevtask);

							$showyeardate = $cfg->get("showyeardate", 0);

							$row   = $event;
							$times = "";

							if (($showyeardate && $jevtask == "year") || $jevtask == "list.events" || $jevtask == "search.results" || $jevtask == "month.calendar" || $jevtask == "cat" || ($showyeardate && $jevtask == "range"))
							{

								$start_publish = $row->getUnixStartDate();
								$stop_publish  = $row->getUnixEndDate();

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
							else if (($jevtask == "day" || $jevtask == "week") && ($row->starttime() != $row->endtime()) && !($row->alldayevent()))
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
							$search[]  = "{{REPEATSUMMARY}}";
							$replace[] = $times;
							$blank[]   = "";
						}
					}

					$search[]    = "{{COUNTDOWN}}";
					$timedelta   = $row->getUnixStartTime() - JevDate::mktime();
					$eventPassed = !($timedelta >= 0);
					$fieldval    = Text::_("JEV_COUNTDOWN_FORMAT");
					$shownsign   = false;
					if (stripos($fieldval, "%nopast") !== false)
					{
						if (!$eventPassed)
						{
							$fieldval = str_ireplace("%nopast", "", $fieldval);
						}
						else
						{
							$fieldval = Text::_('JEV_EVENT_ALREADY_STARTED');
						}
					}
					if (stripos($fieldval, "%d") !== false)
					{
						$days      = intval($timedelta / (60 * 60 * 24));
						$timedelta -= $days * 60 * 60 * 24;
						$fieldval  = str_ireplace("%d", $days, $fieldval);
						$shownsign = true;
					}
					if (stripos($fieldval, "%h") !== false)
					{
						$hours     = intval($timedelta / (60 * 60));
						$timedelta -= $hours * 60 * 60;
						if ($shownsign)
							$hours = abs($hours);
						$hours     = sprintf("%02d", $hours);
						$fieldval  = str_ireplace("%h", $hours, $fieldval);
						$shownsign = true;
					}
					if (stripos($fieldval, "%m") !== false)
					{
						$hours     = intval($timedelta / (60 * 60));
						$mins      = intval($timedelta / 60);
						$timedelta -= $hours * 60;
						if ($mins)
							$mins = abs($mins);
						$mins     = sprintf("%02d", $mins);
						$fieldval = str_ireplace("%m", $mins, $fieldval);
					}
					$replace[] = $fieldval;
					$blank[]   = "";


					$search[]    = "{{PAST_OR_FUTURE}}";
					$timedelta   = $row->getUnixStartTime() - JevDate::mktime();
					$eventPassed = !($timedelta >= 0);
					$shownsign   = false;
					if (!$eventPassed)
					{
						$replace[] = 'future';
					}
					else
					{
						$replace[] = 'past';
					}
					$blank[] = "";

					$search[]  = "{{DURATION}}";
					$timedelta = $row->noendtime() ? 0 : $row->getUnixEndTime() - $row->getUnixStartTime();
					if ($row->alldayevent())
					{
						$timedelta = $row->getUnixEndDate() - $row->getUnixStartDate() + 60 * 60 * 24;
					}
					$fieldval  = Text::_("JEV_DURATION_FORMAT");
					$shownsign = false;
					// whole days!
					if (stripos($fieldval, "%wd") !== false)
					{
						$days      = intval($timedelta / (60 * 60 * 24));
						$timedelta -= $days * 60 * 60 * 24;

						if ($timedelta > 3610)
						{
							// If more than 1 hour and 10 seconds over a day then round up the day output
							++$days;
						}

						$fieldval  = str_ireplace("%d", $days, $fieldval);
						$shownsign = true;
					}
					if (stripos($fieldval, "%d") !== false)
					{
						$days      = intval($timedelta / (60 * 60 * 24));
						$timedelta -= $days * 60 * 60 * 24;
						/*
						  if ($timedelta>3610){
						  // If more than 1 hour and 10 seconds over a day then round up the day output
						  $days +=1;
						  }
						 */
						$fieldval  = str_ireplace("%d", $days, $fieldval);
						$shownsign = true;
					}
					if (stripos($fieldval, "%h") !== false)
					{
						$hours     = intval($timedelta / (60 * 60));
						$timedelta -= $hours * 60 * 60;
						if ($shownsign)
							$hours = abs($hours);
						$hours     = sprintf("%02d", $hours);
						$fieldval  = str_ireplace("%h", $hours, $fieldval);
						$shownsign = true;
					}
					if (stripos($fieldval, "%k") !== false)
					{
						$hours     = intval($timedelta / (60 * 60));
						$timedelta -= $hours * 60 * 60;
						if ($shownsign)
							$hours = abs($hours);
						$fieldval  = str_ireplace("%k", $hours, $fieldval);
						$shownsign = true;
					}
					if (stripos($fieldval, "%m") !== false)
					{
						$hours     = intval($timedelta / (60 * 60));
						$mins      = intval($timedelta / 60);
						$timedelta -= $hours * 60;
						if ($mins)
							$mins = abs($mins);
						$mins     = sprintf("%02d", $mins);
						$fieldval = str_ireplace("%m", $mins, $fieldval);
					}

					$replace[] = $fieldval;
					$blank[]   = "";

					// Round UP Search / Replace
                    $search[]  = "{{DURATION_ROUNDUP}}";
                    $timedelta = $row->getUnixEndTime() - $row->getUnixStartTime();

                    if ($row->alldayevent())
                    {
                        $timedelta = $row->getUnixEndDate() - $row->getUnixStartDate() + 60 * 60 * 24;
                    }

                    $fieldval  = Text::_("JEV_DURATION_FORMAT");
                    $shownsign = false;

                    // Whole days!
                    $days      = intval($timedelta / (60 * 60 * 24));
                    $timedelta -= $days * 60 * 60 * 24;

                    if (stripos($fieldval, "%wd") !== false)
                    {
                        if ($timedelta > 3610 || $row->noendtime())
                        {
                            // If more than 1 hour and 10 seconds over a day then round up the day output
                            ++$days;
                        }

                        $fieldval  = str_ireplace("%d", $days, $fieldval);
                    }

                    if (stripos($fieldval, "%d") !== false)
                    {
                          if ($timedelta>3610 || $row->noendtime()){
                              // If more than 1 hour and 10 seconds over a day then round up the day output
                              ++$days;
                          }

                        $fieldval  = str_ireplace("%d", $days, $fieldval);
                    }
                    if (stripos($fieldval, "%h") !== false)
                    {
                        $fieldval  = str_ireplace("%h", 0, $fieldval);
                    }
                    if (stripos($fieldval, "%k") !== false)
                    {
                        $fieldval  = str_ireplace("%k", 0, $fieldval);
                    }
                    if (stripos($fieldval, "%m") !== false)
                    {
                        $fieldval = str_ireplace("%m", 0, $fieldval);
                    }

                    $replace[] = $fieldval;
                    $blank[]   = "";
				}
				break;

			case "{{PREVIOUSNEXT}}":
				static $doprevnext;
				if (!isset($doprevnext))
				{
					$doprevnext = (StringHelper::strpos($template_value, "{{PREVIOUSNEXT}}") !== false);
				}
				if ($doprevnext)
				{
					$search[]  = "{{PREVIOUSNEXT}}";
					$replace[] = $event->previousnextLinks();
					$blank[]   = "";
				}
				break;

			case "{{PREVIOUSNEXTEVENT}}":
				static $doprevnextevent;
				if (!isset($doprevnextevent))
				{
					$doprevnextevent = (StringHelper::strpos($template_value, "{{PREVIOUSNEXTEVENT}}") !== false);
				}
				if ($doprevnextevent)
				{
					$search[]  = "{{PREVIOUSNEXTEVENT}}";
					$replace[] = $event->previousnextEventLinks();
					$blank[]   = "";
				}
				break;

			case "{{FIRSTREPEAT}}":
			case "{{FIRSTREPEATSTART}}":
			case "{{JEVAGE}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array("{{FIRSTREPEAT}}", $search, false))
				{

					static $dofirstrepeat;
					if (!isset($dofirstrepeat))
					{
						$dofirstrepeat = (StringHelper::strpos($template_value, "{{FIRSTREPEAT") !== false || StringHelper::strpos($template_value, "{{FIRSTREPEATSTART") !== false || StringHelper::strpos($template_value, "{{JEVAGE") !== false);
					}
					if ($dofirstrepeat)
					{
						$search[]    = "{{FIRSTREPEAT}}";
						$firstrepeat = $event->getFirstRepeat();
						if ($firstrepeat->rp_id() == $event->rp_id())
						{
							$replace[] = "";
						}
						else
						{
							$replace[] = "<a class='ev_firstrepeat' href='" . $firstrepeat->viewDetailLink($firstrepeat->yup(), $firstrepeat->mup(), $firstrepeat->dup(), true) . "' title='" . Text::_('JEV_FIRSTREPEAT') . "' >" . Text::_('JEV_FIRSTREPEAT') . "</a>";
						}
						$blank[] = "";

						$search[] = "{{FIRSTREPEATSTART}}";
						if ($firstrepeat->rp_id() == $event->rp_id())
						{
							$replace[] = "";
						}
						else
						{
							$replace[]                          = JEventsHTML::getDateFormat($firstrepeat->yup(), $firstrepeat->mup(), $firstrepeat->dup(), 0);
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
							$replace[] = ($event->yup() > $firstrepeat->yup() && $event->mup() == $firstrepeat->mup() && $event->dup() == $firstrepeat->dup()) ? $event->yup() - $firstrepeat->yup() : "";
						}
						$blank[] = "";
					}
				}
				break;
			case "{{ISMULTIDAY}}" :
				$search[]   = '{{ISMULTIDAY}}';
				if($event->multiday() && isset($event->_length)) {
                    $replace[]    = Text::_('JEV_IS_MULTIDAY_EVENT');
                } else {
				    $replace[] = '';
                }
				$blank[] = '';
			    break;
			case "{{LASTREPEAT}}":
			case "{{LASTREPEATEND}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array("{{LASTREPEAT}}", $search, false))
				{

					static $dolastrepeat;
					if (!isset($dolastrepeat))
					{
						$dolastrepeat = (StringHelper::strpos($template_value, "{{LASTREPEAT}}") !== false || StringHelper::strpos($template_value, "{{LASTREPEATEND}}") !== false);
					}
					if ($dolastrepeat)
					{
						$search[]   = "{{LASTREPEAT}}";
						$lastrepeat = $event->getLastRepeat();
						if ($lastrepeat->rp_id() == $event->rp_id())
						{
							$replace[] = "";
						}
						else
						{
							$replace[] = "<a class='ev_lastrepeat' href='" . $lastrepeat->viewDetailLink($lastrepeat->yup(), $lastrepeat->mup(), $lastrepeat->dup(), true) . "' title='" . Text::_('JEV_LASTREPEAT') . "' >" . Text::_('JEV_LASTREPEAT') . "</a>";
						}
						$blank[] = "";

						$search[] = "{{LASTREPEATEND}}";
						if ($lastrepeat->rp_id() != $event->rp_id())
						{
							$replace[]                       = JEventsHTML::getDateFormat($lastrepeat->ydn(), $lastrepeat->mdn(), $lastrepeat->ddn(), 0);
							$rawreplace["{{LASTREPEATEND}}"] = $lastrepeat->ydn() . "-" . $lastrepeat->mdn() . "-" . $lastrepeat->ddn() . " " . $lastrepeat->hdn() . ":" . $lastrepeat->mindn() . ":" . $lastrepeat->sdn();
						}
						else
						{
							$replace[] = "";
						}
						$blank[] = "";
					}
				}
				break;

			case "{{CREATOR_LABEL}}":
				$search[] = "{{CREATOR_LABEL}}";
				if ($jevparams->get("com_byview", 1))
				{
					$replace[] = Text::_('JEV_BY');
				}
				else
				{
					$replace[] = "";
				}
				$blank[] = "";
				break;

			case "{{CREATOR}}":
				$search[] = "{{CREATOR}}";
				if ($jevparams->get("com_byview", 1))
				{
					$replace[] = $event->contactlink();
				}
				else
				{
					$replace[] = "";
				}
				$blank[] = "";
				break;
            case "{{CREATOR_ID}}":
                $search[]   = "{{CREATOR_ID}}";
                $replace[]  = $event->created_by();
                break;
			case "{{CREATOR_DISPLAY_BEFORE_FIRST_SPACE}}":
				$search[] = "{{CREATOR_DISPLAY_BEFORE_FIRST_SPACE}}";
				if ($jevparams->get("com_byview", 1))
				{
					$value      = JFactory::getUser($event->created_by())->name;
					$expParts   = explode(' ', $value);
					$newValue   = $expParts[0];
					$replace[]  = $newValue;
				}
				else
				{
					$replace[] = "";
				}
				$blank[] = "";
				break;
			case "{{CREATOR_DISPLAY_AFTER_FIRST_SPACE}}":
				$search[] = "{{CREATOR_DISPLAY_AFTER_FIRST_SPACE}}";
				if ($jevparams->get("com_byview", 1))
				{
					$value      = JFactory::getUser($event->created_by())->name;
					$expParts   = explode(' ', $value);
					$newValue   = $expParts[0];
					$replace[]  = $newValue;
					if(count($expParts) > 1) {
						$newValue  = $expParts[1];
						$replace[]  = $newValue;
					} else {
						$newValue  = $expParts[0];
						$replace[]  = $newValue;
					}
				}
				else
				{
					$replace[] = "";
				}
				$blank[] = "";
				break;
			case "{{HITS}}":
				$search[] = "{{HITS}}";
				if ($jevparams->get("com_hitsview", 1) || $template_name != "icalevent.detail_body")
				{
					$replace[] = "<span class='hitslabel'>" . Text::_('JEV_EVENT_HITS') . '</span> : ' . $event->hits();
				}
				else
				{
					$replace[] = "";
				}
				$blank[] = "";
				break;

			case "{{LOCATION_LABEL}}":
			case "{{LOCATION}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array("{{LOCATION}}", $search, false))
				{

					if ($event->hasLocation())
					{
						$search[]  = "{{LOCATION_LABEL}}";
						$replace[] = Text::_('JEV_EVENT_ADRESSE') . "&nbsp;";
						$blank[]   = "";
						$search[]  = "{{LOCATION}}";
						$replace[] = $event->location();
						$blank[]   = "";
					}
					else
					{
						$search[]  = "{{LOCATION_LABEL}}";
						$replace[] = "";
						$blank[]   = "";
						$search[]  = "{{LOCATION}}";
						$replace[] = "";
						$blank[]   = "";
					}
				}
				break;

			case "{{CONTACT_LABEL}}":
			case "{{CONTACT}}":
				// no need to repeat this for each of the matching 'case's
				if (!in_array("{{CONTACT}}", $search, false))
				{

					if ($event->hasContactInfo())
					{
						if (StringHelper::strpos($event->contact_info(), '<script') === false)
						{
							PluginHelper::importPlugin('content');

							// Contact
							$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';
							if (StringHelper::strpos($event->contact_info(), '<a href=') === false && $event->contact_info() != "")
							{
								$event->contact_info(preg_replace('@(https?://)(' . $pattern . '*)@i', '<a href="\\1\\2">\\1\\2</a>', $event->contact_info()));
							}

							// Need to call conContentPrepare even thought its called on the template value below here
							// because is the field appears twice it won't do the replacement on the second item
							$params        = new JevRegistry(null);
							$tmprow        = new stdClass();
							$tmprow->text  = $event->contact_info();
							$tmprow->event = $event;
							PluginHelper::importPlugin('content');
							$app->triggerEvent('onContentPrepare', array('com_jevents', &$tmprow, &$params, 0));
							// Make sure each instance is replaced properly
							// New Joomla code for mail cloak only works once on a page !!!
							// Random number
							$rand         = rand(1, 100000);
							$tmprow->text = preg_replace("/cloak[0-9]*/i", "cloak" . $rand, $tmprow->text);
							$event->contact_info($tmprow->text);
						}
						$search[]  = "{{CONTACT_LABEL}}";
						$replace[] = Text::_('JEV_EVENT_CONTACT') . "&nbsp;";
						$blank[]   = "";
						$search[]  = "{{CONTACT}}";
						$replace[] = $event->contact_info();
						$blank[]   = "";
					}
					else
					{
						$search[]  = "{{CONTACT_LABEL}}";
						$replace[] = "";
						$blank[]   = "";
						$search[]  = "{{CONTACT}}";
						$replace[] = "";
						$blank[]   = "";
					}
				}
				break;

			case "{{EXTRAINFO}}":
				//Extra
				if (StringHelper::strpos($event->extra_info(), '<script') === false && $event->extra_info() != "")
				{
					PluginHelper::importPlugin('content');

					$pattern = '[a-zA-Z0-9&?_.,=%\-\/#]';
					if (StringHelper::strpos($event->extra_info(), '<a href=') === false)
					{
						$event->extra_info(preg_replace('@(https?://)(' . $pattern . '*)@i', '<a href="\\1\\2">\\1\\2</a>', $event->extra_info()));
					}
					//$row->extra_info(eregi_replace('[^(href=|href="|href=\')](((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','\\1', $row->extra_info()));
					// NO need to call conContentPrepate since its called on the template value below here
				}

				$search[]  = "{{EXTRAINFO}}";
				$replace[] = $event->extra_info();
				$blank[]   = "";
				break;

			case "{{RPID}}":
				$search[]  = "{{RPID}}";
				$replace[] = $event->rp_id();
				$blank[]   = "";
				break;
			case "{{TZID}}":
				$jtz       = $jevparams->get("icaltimezonelive", "");
				$jtz       = isset($event->_tzid) && !empty($event->_tzid) ? $event->_tzid : $jtz;
				$search[]  = "{{TZID}}";
				$replace[] = $jtz;
				$blank[]   = "";
				break;
			case "{{EVID}}":
				$search[]  = "{{EVID}}";
				$replace[] = $event->ev_id();
				$blank[]   = "";
				break;
			case "{{SITEROOT}}":
				$search[]  = "{{SITEROOT}}";
				$replace[] = Uri::root();
				$blank[]   = "";
				break;
			case "{{SITEBASE}}":
				$search[]  = "{{SITEBASE}}";
				$replace[] = Uri::base();
				$blank[]   = "";
				break;

			case "{{LDJSON}}" :
				if ($template_name === "icalevent.detail_body"
					&& $jevparams->get("enable_gsed", 0)
					&& $jevparams->get("sevd_imagename", 0)
					&& $jevparams->get("permatarget", 0)
					&& $hasLocationOrIsOnline
				)
				{

					$lddata = array();
					$lddata["@context"] = "https://schema.org";
					$lddata["@type"] =  "Event";
					$lddata["name"] =  $event->title();
					$lddata["description"] =  $jevparams->get("ldjson_striptags", 1) ?  strip_tags( $event->content() ) : $event->content();

					// Timezone
					// event tzid
					// icaltimezonelive
					// icaltimezone
					$jtz        = $jevparams->get("icaltimezonelive", "");
					$jtz        = isset($event->_tzid) && !empty($event->_tzid) ? $event->_tzid : $jtz;
					if (!empty($jtz))
					{
						$jtz = new DateTimeZone($jtz);
					}
					else
					{
						$jtz = new DateTimeZone(@date_default_timezone_get());
					}

					if ($event->alldayevent())
					{
						$lddata["startDate"] = JEventsHTML::getDateFormat($event->yup(), $event->mup(), $event->dup(), "%Y-%m-%d") ;
					}
					else
					{
						$startDate = JEventsHTML::getDateFormat($event->yup(), $event->mup(), $event->dup(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $event->hup(), $event->minup());
						$indate  = new DateTime($startDate, $jtz);
						$offset = $indate->format('P');
						$lddata["startDate"] = $startDate . $offset;
					}

					if ($event->getUnixEndTime() > $event->getUnixStartTime())
					{
						if ($event->noendtime() || $event->alldayevent())
						{
							$lddata["endDate"] = JEventsHTML::getDateFormat($event->ydn(), $event->mdn(), $event->ddn(), "%Y-%m-%d");
						}
						else
						{
							$endDate = JEventsHTML::getDateFormat($event->ydn(), $event->mdn(), $event->ddn(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $event->hdn(), $event->mindn()) ;
							$indate  = new DateTime($endDate, $jtz);
							$offset = $indate->format('P');
							$lddata["endDate"] = $endDate . $offset;
						}
					}

					$imageurl = "_imageurl" . $jevparams->get("sevd_imagename", 0);
					if (isset($event->$imageurl))
					{

						$imgplugin  = PluginHelper::getPlugin("jevents", "jevfiles");
						$imgpluginparams = new Registry($imgplugin->params);

						$resetparams = false;
						if ($jevparams->get("sevd_defaultimage", false) && $imgpluginparams->get("defaultimage", '') === '')
						{
							$imgpluginparams->set("defaultimage", $jevparams->get("sevd_defaultimage", false));
							$imgplugin->params = json_encode($imgpluginparams);
							$resetparams = true;
						}

						try
						{
							$lddata["image"] = plgJEventsjevfiles::getSizedImageUrl($event, $imageurl, $jevparams->get('sevd_imagesize', '1920x1920'), $imgpluginparams);
						}
						catch (Exception $e)
						{
							// for sites that haven't upgraded standard images
							$lddata["image"] = $event->$imageurl;
							if (strpos($lddata["image"], "/") === 0)
							{
								$lddata["image"] = substr($lddata["image"], 1);
							}
							// no need to add host details to call to getSizedImage Url
							$lddata["image"] = array(JURI::root(false)  . $lddata["image"]);
						}

						if ($resetparams)
						{
							$imgpluginparams->set("defaultimage", "");
							$imgplugin->params = json_encode($imgpluginparams);
						}
					}

					if (isset($event->_jevlocation))
					{
						$loc = array();
						$loc["@type"] = "Place";
						$loc["name"] = $event->_jevlocation->title;
						$address = array();
						if (isset($event->_jevlocation->street) && !empty($event->_jevlocation->street))
						{
							$address["streetAddress"] = $event->_jevlocation->street;
						}
						if (isset($event->_jevlocation->postcode) && !empty($event->_jevlocation->postcode))
						{
							$address["postalCode"] = $event->_jevlocation->postcode;
						}
						if (isset($event->_jevlocation->city) && !empty($event->_jevlocation->city))
						{
							$address["addressLocality"] = $event->_jevlocation->city;
						}
						if (isset($event->_jevlocation->state) && !empty($event->_jevlocation->state))
						{
							$address["addressRegion"] = $event->_jevlocation->state;
						}
						if (isset($event->_jevlocation->country) && !empty($event->_jevlocation->country))
						{
							$address["addressCountry"] = $event->_jevlocation->country;
						}
						// Structured data needs a valid address
						if (!empty($address))
						{
							$address["@type"] = "PostalAddress";
							$loc["address"] = $address;

							$lddata["location"] = $loc;
							$lddata["eventAttendanceMode"] = "https://schema.org/OfflineEventAttendanceMode";
						}
					}

					if (isset($event->_jevpeople) && count($event->_jevpeople))
					{
						foreach ($event->_jevpeople as $person)
						{
							if ($person->type_id == $jevparams->get("sevd_peopletype", -1))
							{
								$pdata = array();
								$pdata["@type"] = "PerformingGroup";
								$pdata["name"] = $person->title;

								$lddata["performer"] = $pdata;
								break;
							}
						}
						foreach ($event->_jevpeople as $person)
						{
							if ($person->type_id == $jevparams->get("sevd_organizertype", -1))
							{
								$pdata = array();
								$pdata["@type"] = "Organization";
								$pdata["name"] = $person->title;
								if (isset($person->www) && !empty($person->www))
								{
									$pdata["url"] = $person->www;
								}

								$lddata["organizer"] = $pdata;
								break;
							}
						}
					}

					$onlineevent = $jevparams->get("sevd_onlineeventfield", 0);
					if ($onlineevent !== 0 && isset($event->customfields) && isset($event->customfields[$onlineevent]) && !empty($event->customfields[$onlineevent]['value']))
					{
						if (isset($lddata["location"]))
						{
							$lddata["location"] = array($lddata["location"]);

							$loc = array();
							$loc["@type"] = "VirtualLocation";
							$loc["url"] = $event->customfields[$onlineevent]['value'];

							$lddata["location"][] = $loc;

							$lddata["eventAttendanceMode"] = "https://schema.org/MixedEventAttendanceMode";
						}
						else
						{
							$lddata["eventAttendanceMode"] = "https://schema.org/OnlineEventAttendanceMode";
							$loc = array();
							$loc["@type"] = "VirtualLocation";
							$loc["url"] = $event->customfields[$onlineevent]['value'];

							$lddata["location"] = $loc;
						}

					}

					$eventstatus = $jevparams->get("sevd_eventstatus", 0);
					if ($eventstatus !== 0 && isset($event->customfields) && isset($event->customfields[$eventstatus]) && !empty($event->customfields[$eventstatus]['rawvalue']))
					{
						$eventStatus = array(
							1 => "EventScheduled",
							2 => "EventCancelled",
							3 => "EventMovedOnline",
							4 => "EventPostponed",
							5 => "EventRescheduled"
						);

						$lddata["eventStatus"] = array();
						$statuses = explode(",", $eventStatus[$event->customfields[$eventstatus]['rawvalue']]);
						foreach ($statuses as $status)
						{
							$lddata["eventStatus"][] = "https://schema.org/" . trim($status);
						}
					}

					// TODO RSVP Pro
					/*
					$offer = array();
					$offer["@type"] = "Offer";
					//$offer["price"] = 0;
					//$offer["priceCurrency"] = "USD";
					$offer["availability"] = "https://schema.org/InStock";
					//$offer["availability"] = "https://schema.org/SoldOut";
					$offer["validFrom"] = "2017-01-20T16:20-08:00";

					$lddata["offers"] = array($offer);
					*/

					$search[]  = "{{LDJSON}}";
					// Structured data needs a valid address
					$replace[] = isset($lddata["location"]) ? json_encode($lddata) : "";
					$blank[]   = "";
				}
				break;

			default:
				$strippedmatch = str_replace(array("{", "}"), "", $strippedmatch);
				if (is_callable(array($event, $strippedmatch)))
				{
					$search[]  = "{{" . $strippedmatch . "}}";
					$replace[] = $event->$strippedmatch();
					$blank[]   = "";
				}
				break;

		}
	}

	// Now do the plugins
	// get list of enabled plugins

	$layout = ($template_name == "icalevent.list_row" || $template_name == "month.calendar_cell" || $template_name == "month.calendar_tip") ? "list" : "detail";

	if ($runplugins)
	{
		$jevplugins = PluginHelper::getPlugin("jevents");

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
						// Special case where $fieldname has option value in it e.g. sizedimages
						if (strpos($fieldname, ";") > 0)
						{
							$temp = explode(";", $fieldname);
							$fn   = $temp[0];
							// What is the list of them
							$temp = array();
							preg_match_all('@\{{' . $fn . ';(.*?)[#|}}]@', $template_value, $temp);
							if (count($temp) == 2 && count($temp[1]))
							{
								$fieldnames = array();
								foreach ($temp[1] as $tmp)
								{
									$fieldnames[] = $fn . ";" . $tmp;
								}
							}
						}
						else
						{
							$fieldnames = array($fieldname);
						}

						foreach ($fieldnames as $fn)
						{
							if (empty($fn) || !StringHelper::strpos($template_value, $fn) !== false)
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
								$blank[]   = "";
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
		if (StringHelper::strpos($search[$s], "TRUNCATED_DESC:") > 0 || StringHelper::strpos($search[$s], "TRUNCATED_TITLE:") > 0)
		{
			global $tempreplace, $tempevent, $tempsearch;
			$tempreplace    = $replace[$s];
			$tempsearch     = $search[$s];
			$tempevent      = $event;
			$template_value = preg_replace_callback("|$tempsearch|", 'jevSpecialHandling', $template_value);
		}
	}

	// Date/time formats etc.
	for ($s = 0; $s < count($search); $s++)
	{
		if (StringHelper::strpos($search[$s], "STARTDATE") > 0 || StringHelper::strpos($search[$s], "STARTTIME") > 0 || StringHelper::strpos($search[$s], "ENDDATE") > 0 || StringHelper::strpos($search[$s], "ENDTIME") > 0 || StringHelper::strpos($search[$s], "ENDTZ") > 0 || StringHelper::strpos($search[$s], "STARTTZ") > 0 || StringHelper::strpos($search[$s], "MULTIENDDATE") > 0 || StringHelper::strpos($search[$s], "FIRSTREPEATSTART") > 0 || StringHelper::strpos($search[$s], "LASTREPEATEND") > 0)
		{
			global $tempreplace, $tempevent, $tempsearch, $tempblank;
			$tempreplace = !empty($rawreplace[$search[$s]]) ? $rawreplace[$search[$s]] : $blank[$s];
			$tempblank   = $blank[$s];
			$tempsearch  = str_replace("}}", ";.*?}}", $search[$s]);
			$tempevent   = $event;
			if (!isset($rawreplace[$search[$s]]) || !$rawreplace[$search[$s]])
			{
				$template_value = preg_replace_callback("~$tempsearch~", 'jevStripDateFormatting', $template_value);
			}
			else
			{
				$template_value = preg_replace_callback("~$tempsearch~", 'jevSpecialDateFormatting', $template_value);
			}
		}
	}

	$processedCssString = implode("\n", $processedCss);
	// non greedy replacement - because of the ?
	$processedCssString = preg_replace_callback('|{{.*?}}|', 'cleanLabels', $processedCssString);


	for ($s = 0; $s < count($search); $s++)
	{
		global $tempreplace, $tempevent, $tempsearch, $tempblank;
		$tempreplace    = $replace[$s];
		$tempblank      = $blank[$s];
		$tempsearch     = str_replace("}}", "#", $search[$s]);
		$tempevent      = $event;
		$template_value = preg_replace_callback("|$tempsearch(.+?)}}|", 'jevSpecialHandling2', $template_value);
		$processedCssString = preg_replace_callback("|$tempsearch(.+?)}}|", 'jevSpecialHandling2', $processedCssString);
	}

	// The universal search and replace to finish
	$template_value = str_replace($search, $replace, $template_value);
	$processedCssString = str_replace($search, $replace, $processedCssString);

	if ($specialmodules && strpos($template_value, "{{MODULESTART#") !== false)
	{
		$reg = JevRegistry::getInstance("com_jevents");

		$parts          = explode("{{MODULESTART#", $template_value);
		$dynamicmodules = array();
		foreach ($parts as $part)
		{
			$currentdynamicmodules = $reg->get("dynamicmodules", false);
			if (StringHelper::strpos($part, "{{MODULEEND}}") === false)
			{
				// strip out BAD HTML tags left by WYSIWYG editors
				if (StringHelper::substr($part, StringHelper::strlen($part) - 3) == "<p>")
				{
					$template_value = StringHelper::substr($part, 0, StringHelper::strlen($part) - 3);
				}
				else
				{
					$template_value = $part;
				}
				continue;
			}
			// start with module name
			$modname       = StringHelper::substr($part, 0, StringHelper::strpos($part, "}}"));
			$modulecontent = StringHelper::substr($part, StringHelper::strpos($part, "}}") + 2);
			$modulecontent = StringHelper::substr($modulecontent, 0, StringHelper::strpos($modulecontent, "{{MODULEEND}}"));
			// strip out BAD HTML tags left by WYSIWYG editors
			if (StringHelper::strpos($modulecontent, "</p>") === 0)
			{
				$modulecontent = "<p>x@#" . $modulecontent;
			}
			if (StringHelper::substr($modulecontent, StringHelper::strlen($modulecontent) - 3) == "<p>")
			{
				$modulecontent .= "x@#</p>";
			}

			$modulecontent = str_replace("<p>x@#</p>", "", $modulecontent);
			if (isset($currentdynamicmodules[$modname]))
			{
				if (!is_array($currentdynamicmodules[$modname]))
				{
					$currentdynamicmodules[$modname] = array($currentdynamicmodules[$modname]);
				}
				$currentdynamicmodules[$modname] [] = $modulecontent;
				$dynamicmodules[$modname]           = $currentdynamicmodules[$modname];
			}
			else
			{
				$dynamicmodules[$modname] = $modulecontent;
			}
		}
		$reg->set("dynamicmodules", $dynamicmodules);
	}

	// non greedy replacement - because of the ?
	$template_value = preg_replace_callback('|{{.*?}}|', 'cleanUnpublished', $template_value);
	$processedCssString = preg_replace_callback('|{{.*?}}|', 'cleanUnpublished', $processedCssString);

	Factory::getDocument()->addStyleDeclaration($processedCssString);

	// replace [[ with { to that other content plugins can work ok - but not for calendar cell or tooltip since we use [[ there already!
	if ($template_name != "month.calendar_cell" && $template_name != "month.calendar_tip")
	{
		// making sure we don't trip over closing CDATA tags which look like ]]>
		$template_value = str_replace(array("[[", "]]>", "]]", "]&*$^]"), array("{", "]&*$^]", "}", "]]>"), $template_value);
	}

	//We add new line characters again to avoid being marked as SPAM when using tempalte in emails
	// do this before calling content plugins in case these add javascript etc. to layout
	$template_value = preg_replace("@(<\s*(br)*\s*\/\s*(p|td|tr|table|div|ul|li|ol|dd|dl|dt)*\s*>)+?@i", "$1\n", $template_value);

	// Call content plugins - BUT because emailcloak doesn't identify emails in input fields to a text substitution
	$template_value = str_replace("@", "@£@", $template_value);
	$params         = new JevRegistry(null);
	$tmprow         = new stdClass();
	$tmprow->text   = $template_value;
	$tmprow->event  = $event;
	ob_start();
	PluginHelper::importPlugin('content');
	ob_end_clean();
	$app->triggerEvent('onContentPrepare', array('com_jevents', &$tmprow, &$params, 0));
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
			if (StringHelper::strpos($matches[0], "://") > 0)
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

	if (count($matches) == 1 && StringHelper::strpos($matches[0], ":") > 0)
	{
		global $tempreplace, $tempevent, $tempsearch;
		$parts = explode(":", $matches[0]);
		if (count($parts) == 2)
		{
			$wordcount = str_replace("}}", "", $parts[1]);
			$charcount = 0;
			if (StringHelper::strpos($wordcount, "chars") > 0)
			{
				$charcount = intval(str_replace("chars", "", $wordcount));
				$wordcount = 0;
			}
			else if (StringHelper::strpos($wordcount, "word") > 0)
			{
				$wordcount = intval(str_replace("words", "", $wordcount));
				$value     = Truncator::truncate($tempreplace, $wordcount, " ...");

				return $value;
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
				$words   = array_slice($words, 0, $wordcount);
				$words[] = " ...";

				return implode(" ", $words);
			}
			if ($charcount > 0 && StringHelper::strlen($value) > $charcount)
			{
				return StringHelper::substr($value, 0, $charcount) . " ...";
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

function jevStripDateFormatting($matches)
{
    if (count($matches) == 1 && StringHelper::strpos($matches[0], ";") > 0)
    {
        global $tempreplace, $tempevent, $tempsearch, $tempblank;
        $parts = explode(";", $matches[0]);
        if (count($parts) == 2)
        {
            $fmt = str_replace(array("}}", "}"), "", $parts[1]);
            if (strpos($fmt, "#") !== false)
            {
                $fmtparts = explode("#", $fmt);
                // remove the time format
                if (count($fmtparts) == 2)
                {
                    return "";
                }
                else
                {
                    return $fmtparts[2];
                }
            }
        }
    }

    return $matches[0];
}

function jevSpecialDateFormatting($matches)
{

	if (count($matches) == 1 && StringHelper::strpos($matches[0], ";") > 0)
	{
		global $tempreplace, $tempevent, $tempsearch, $tempblank;
		$parts = explode(";", $matches[0]);
		if (count($parts) == 2)
		{
			$fmt = str_replace(array("}}", "}"), "", $parts[1]);
			if (strpos($fmt, "#") !== false)
			{
				$fmtparts = explode("#", $fmt);
				if ($tempreplace == $tempblank)
				{
					if (count($fmtparts) == 3)
					{
						$fmt = $fmtparts[2];
					}
					else
						return "";
				}
				else if (count($fmtparts) >= 2)
				{
					$fmt = sprintf($fmtparts[1], $fmtparts[0]);
				}
			}
			//return strftime($fmt, strtotime(strip_tags($tempreplace)));
			if (!is_int($tempreplace))
			{
				$tempreplace = strtotime(strip_tags($tempreplace));
			}
			if (strpos($fmt, "%") === false)
			{
				return date($fmt, $tempreplace);
			}

			return JEV_CommonFunctions::jev_strftime($fmt, $tempreplace);
		}
		// TZ specified
		else if (count($parts) == 3)
		{
			$fmt = $parts[1];

			// Must get this each time otherwise modules can't set their own timezone
			$jevparams  = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$jtz        = $jevparams->get("icaltimezonelive", "");
			if ($jtz != "")
			{
				$jtz = new DateTimeZone($jtz);
			}
			else
			{
				$jtz = new DateTimeZone(@date_default_timezone_get());
			}
			$outputtz = str_replace(array("}}", "}"), "", $parts[2]);

			if (strpos($outputtz, "#") !== false)
			{
				$outputtzparts = explode("#", $outputtz);
				$outputtz      = $outputtzparts[0];
				if ($tempreplace == $tempblank)
				{
					if (count($outputtzparts) == 3)
					{
						$fmt = $outputtzparts[2];
					}
					else
						return "";
				}
				else if (count($outputtzparts) >= 2)
				{
					$fmt = sprintf($outputtzparts[1], $fmt);
				}
			}


			if (strtolower($outputtz) == "user" || strtolower($outputtz) == "usertz")
			{
				$user     = Factory::getUser();
				$outputtz = $user->getParam("timezone", $jevparams->get("icaltimezonelive", @date_default_timezone_get()));
			}
			$outputtz = new DateTimeZone($outputtz);

			if (is_integer($tempreplace))
			{
				$tempreplace = JEV_CommonFunctions::jev_strftime("%Y-%m-%d %H:%M:%S", $tempreplace);
			}
			$indate  = new DateTime($tempreplace, $jtz);
			$offset1 = $indate->getOffset();

			// set the new timezone
			$indate->setTimezone($outputtz);
			$offset2 = $indate->getOffset();

			$indate = $indate->getTimestamp() + $offset2 - $offset1;

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

	if (count($matches) == 2 && StringHelper::strpos($matches[0], "#") > 0)
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
			try
			{
				return sprintf($parts[0], $tempreplace);
			}
			catch (Exception $e)
			{
				return "Invalid format string in custom layout <br>" . $matches[1] . "<br>Please report to site manager.";
			}
		}
	}
	else
		return "";
}

if (!class_exists("InvalidHtmlException"))
{

	class InvalidHtmlException extends \Exception
	{

	}

}

if (!class_exists("Truncator") && !function_exists('ht_strlen'))
{
	if (function_exists('grapheme_strlen'))
	{
		function ht_strlen($string)
		{

			return grapheme_strlen($string);
		}

		function ht_substr($string, $from, $to = 2147483647)
		{

			return grapheme_substr($string, $from, $to);
		}

	}
	else if (function_exists('mb_strlen'))
	{
		function ht_strlen($string)
		{

			return mb_strlen($string);
		}

		function ht_substr($string, $from, $to = 2147483647)
		{

			return mb_substr($string, $from, $to);
		}

	}
	else if (function_exists('iconv_strlen'))
	{
		function ht_strlen($string)
		{

			return iconv_strlen($string);
		}

		function ht_substr($string, $from, $to = 2147483647)
		{

			return iconv_substr($string, $from, $to);
		}

	}
	else
	{
		function ht_strlen($string)
		{

			return strlen($string);
		}

		function ht_substr($string, $from, $to = 2147483647)
		{

			return substr($string, $from, $to);
		}

	}

	if (function_exists('mb_strtolower'))
	{
		function ht_strtolower($string)
		{

			return mb_strtolower($string);
		}

		function ht_strtoupper($string)
		{

			return mb_strtoupper($string);
		}

	}
	else
	{
		function ht_strtolower($string)
		{

			return strtolower($string);
		}

		function ht_strtoupper($string)
		{

			return strtoupper($string);
		}

	}

	class Truncator
	{

		public static $default_options = array(
			'ellipsis'        => '…',
			'length_in_chars' => false,
		);
		// These tags are allowed to have an ellipsis inside
		public static $ellipsable_tags = array(
			'p', 'ol', 'ul', 'li',
			'div', 'header', 'article', 'nav',
			'section', 'footer', 'aside',
			'dd', 'dt', 'dl',
		);
		public static $self_closing_tags = array(
			'br', 'hr', 'img',
		);

		/**
		 * Truncate given HTML string to specified length.
		 * If length_in_chars is false it's trimmed by number
		 * of words, otherwise by number of characters.
		 *
		 * @param  string       $html
		 * @param  integer      $length
		 * @param  string|array $opts
		 *
		 * @return string
		 */
		public static function truncate($html, $length, $opts = array())
		{

			if (is_string($opts))
				$opts = array('ellipsis' => $opts);
			$opts = array_merge(static::$default_options, $opts);
			// wrap the html in case it consists of adjacent nodes like <p>foo</p><p>bar</p>
			//$html = "<div>" . static::utf8_for_xml($html) . "</div>";

			// see http://php.net/manual/en/domdocument.loadhtml.php
			$html      = '<html><head><meta content="text/html; charset=utf-8" http-equiv="Content-Type"></head><body><div>' . $html . '</div></body>';
			$root_node = null;

			// Parse using HTML5Lib if it's available.
			if (class_exists('HTML5Lib\\Parser'))
			{
				try
				{
					$doc       = \HTML5Lib\Parser::parse($html);
					$root_node = $doc->documentElement->lastChild->lastChild;
				}
				catch (\Exception $e)
				{
					;
				}
			}

			if ($root_node === null)
			{
				// HTML5Lib not available so we'll have to use DOMDocument
				// We'll only be able to parse HTML5 if it's valid XML
				$doc                     = new DOMDocument;
				$doc->formatOutput       = false;
				$doc->preserveWhitespace = true;
				// loadHTML will fail with HTML5 tags (article, nav, etc)
				// so we need to suppress errors and if it fails to parse we
				// retry with the XML parser instead
				$prev_use_errors = libxml_use_internal_errors(true);
				if ($doc->loadHTML($html))
				{
					$root_node = $doc->documentElement->lastChild->lastChild;
				}
				else if ($doc->loadXML($html))
				{
					$root_node = $doc->documentElement;
				}
				else
				{
					libxml_use_internal_errors($prev_use_errors);
					throw new InvalidHtmlException;
				}
				libxml_use_internal_errors($prev_use_errors);
			}

			list($text, $_, $opts) = static::_truncate_node($doc, $root_node, $length, $opts);
			$text = ht_substr(ht_substr($text, 0, -6), 5);

			return $text;
		}

		protected static function _truncate_node($doc, $node, $length, $opts)
		{

			if ($length === 0 && !static::ellipsable($node))
			{
				return array('', 1, $opts);
			}
			list($inner, $remaining, $opts) = static::_inner_truncate($doc, $node, $length, $opts);
			if (0 === ht_strlen($inner))
			{
				return array(in_array(ht_strtolower($node->nodeName), static::$self_closing_tags) ? $doc->saveXML($node) : "", $length - $remaining, $opts);
			}
			while ($node->firstChild)
			{
				$node->removeChild($node->firstChild);
			}
			$newNode = $doc->createDocumentFragment();
			$newNode->appendXml($inner);
			$node->appendChild($newNode);

			return array($doc->saveXML($node), $length - $remaining, $opts);
		}

		protected static function _inner_truncate($doc, $node, $length, $opts)
		{

			$inner     = '';
			$remaining = $length;
			foreach ($node->childNodes as $childNode)
			{
				if ($childNode->nodeType === XML_ELEMENT_NODE)
				{
					list($txt, $nb, $opts) = static::_truncate_node($doc, $childNode, $remaining, $opts);
				}
				else if ($childNode->nodeType === XML_TEXT_NODE)
				{
					list($txt, $nb, $opts) = static::_truncate_text($doc, $childNode, $remaining, $opts);
				}
				else
				{
					$txt = '';
					$nb  = 0;
				}
				$remaining -= $nb;
				$inner     .= $txt;
				if ($remaining < 0)
				{
					if (static::ellipsable($node))
					{
						$inner                 = preg_replace('/(?:[\s\pP]+|(?:&(?:[a-z]+|#[0-9]+);?))*$/', '', $inner) . $opts['ellipsis'];
						$opts['ellipsis']      = '';
						$opts['was_truncated'] = true;
					}
					break;
				}
			}

			return array($inner, $remaining, $opts);
		}

		protected static function _truncate_text($doc, $node, $length, $opts)
		{

			$xhtml = $node->ownerDocument->saveXML($node);
			preg_match_all('/\s*\S+/', $xhtml, $words);
			$words = $words[0];
			if ($opts['length_in_chars'])
			{
				$count = ht_strlen($xhtml);
				if ($count <= $length && $length > 0)
				{
					return array($xhtml, $count, $opts);
				}
				if (count($words) > 1)
				{
					$content = '';

					foreach ($words as $word)
					{
						if (ht_strlen($content) + ht_strlen($word) > $length)
						{
							break;
						}

						$content .= $word;
					}

					return array($content, $count, $opts);
				}

				return array(ht_substr($node->textContent, 0, $length), $count, $opts);
			}
			else
			{
				$count = count($words);
				if ($count <= $length && $length > 0)
				{
					return array($xhtml, $count, $opts);
				}

				return array(implode('', array_slice($words, 0, $length)), $count, $opts);
			}
		}

		protected static function ellipsable($node)
		{

			return ($node instanceof DOMDocument) || in_array(ht_strtolower($node->nodeName), static::$ellipsable_tags);
		}

		protected static function utf8_for_xml($string)
		{

			return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
		}

	}

}
