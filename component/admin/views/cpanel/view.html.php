<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 3543 2012-04-20 08:17:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminCPanelViewCPanel extends JEventsAbstractView
{

	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function cpanel($tpl = null)
	{
		jimport('joomla.html.pane');

// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0"))
			JHTML::stylesheet('administrator/components/' . JEV_COM_COMPONENT . '/assets/css/eventsadmin.css');
		else
			JHTML::stylesheet('eventsadmin.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'));

// Set toolbar items for the page
//JToolBarHelper::preferences('com_jevents', '580', '750');
		JToolBarHelper::title(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'), 'jevents');
		/*
		  $user= JFactory::getUser();
		  if ($user->authorise('core.admin','com_jevents.admin')) {
		  JToolBarHelper::preferences('com_jevents' , '600', $width = '950');
		  }
		 */

		$this->_hideSubmenu();


		if (JFactory::getApplication()->isAdmin())
		{
//JToolBarHelper::preferences(JEV_COM_COMPONENT, '580', '750');
		}
//JToolBarHelper::help( 'screen.cpanel', true);

		JSubMenuHelper::addEntry(JText::_('CONTROL_PANEL'), 'index.php?option=' . JEV_COM_COMPONENT, true);

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');

	}

	/**
	 * render News feed from JEvents portal
	 */
	function renderJEventsNews()
	{

		if (JVersion::isCompatible("1.6")){
			$cache = JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->setLifeTime(86400);
			// In Joomla 1.7 caching of feeds doesn't work!
			$cache->setCaching(true);

			// workaround for Joomla 2.5.7 reversion bug
			$app = JFactory::getApplication();
			if (!isset($app->registeredurlparams)){
				$app->registeredurlparams = new stdClass();
			}
			
			$cache->get($this, 'renderJEventsNewsCached');
		}
		else {
			return $this->renderJEventsNewsCached();
		}
		
	}

	function renderJEventsNewsCached()
	{

		$output = '';

		//  get RSS parsed object
		$options = array();
		$options['rssUrl'] = 'http://www.jevents.net/jevnews?format=feed&type=rss';
		if (JVersion::isCompatible("1.6")){
			$options['cache_time'] = 0;
		}
		else {
			$options['cache_time'] = 86400;
		}
		
		$rssDoc = & JFactory::getXMLparser('RSS', $options);

		if ($rssDoc == false)
		{
			$output = JText::_('Error: Feed not retrieved');
		}
		else
		{
// channel header and link
			$title = str_replace(" ", "_", $rssDoc->get_title());
			$link = $rssDoc->get_link();

			$output = '<table class="adminlist">';
			$output .= '<tr><th><a href="' . $link . '" target="_blank">' . JText::_($title) . '</th></tr>';

			$items = array_slice($rssDoc->get_items(), 0, 3);
			$numItems = count($items);
			if ($numItems == 0)
			{
				$output .= '<tr><th>' . JText::_('JEV_No_news') . '</th></tr>';
			}
			else
			{
				$k = 0;
				for ($j = 0; $j < $numItems; $j++)
				{
					$item = $items[$j];
					$output .= '<tr><td class="row' . $k . '">';
					$output .= '<a href="' . $item->get_link() . '" target="_blank">' . $item->get_title() . '</a>';
					if ($item->get_description())
					{
						$description = $this->limitText($item->get_description(), 50);
						$output .= '<br />' . $description;
					}
					$output .= '</td></tr>';
					$k = 1 - $k;
				}
			}

			$output .= '</table>';
		}
		// do not return the output because of differences between J15 and J17
		echo $output;
		

	}

	function renderVersionStatusReport(& $needsupdate)
	{
		if (JEVHelper::isAdminUser())
		{

//  get RSS parsed object
			$options = array();
			$rssUrl = 'http://www.jevents.net/versions.xml';
			$cache_time = 86400;

//$rssUrl = 'http://ubu.jev20j16.com/versions.xml';
//$cache_time = 1;

			jimport('simplepie.simplepie');

			if (JVersion::isCompatible("1.6"))
			{
				// this caching doesn't work!!!
				//$cache = JFactory::getCache('feed_parser', 'callback');
				//$cache->setLifeTime($cache_time);
				//$cache->setCaching(true);

				$rssDoc = new SimplePie(null, null, 0);

				$rssDoc->enable_cache(false);
				$rssDoc->set_feed_url($rssUrl);
				$rssDoc->force_feed(true);
				$rssDoc->set_item_limit(999);

				//$results = $cache->get(array($rssDoc, 'init'), null, false, false);
				$results = $rssDoc->init();
			}
			else
			{
				$rssDoc = new SimplePie(
								$rssUrl,
								JPATH_BASE . DS . 'cache',
								$cache_time
				);
				$rssDoc->force_feed(true);
				$rssDoc->handle_content_type();
				$results = $rssDoc->init();
			}

			if ($results == false)
			{
				return false;
			}
			else
			{
				$this->generateVersionsFile($rssDoc);

				$rows = array();
				$items = $rssDoc->get_items();

				foreach ($items as $item)
				{
					$apps = array();
					if (strpos($item->get_title(), "layout_") === 0)
					{
						$layout = str_replace("layout_", "", $item->get_title());
						if (JFolder::exists(JEV_PATH . "views/$layout"))
						{
// club layouts			 
							$xmlfiles1 = JFolder::files(JEV_PATH . "views/$layout", "manifest\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (realpath($manifest) != $manifest)
									continue;
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app = new stdClass();
								$app->name = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$apps["layout_" . basename(dirname($manifest))] = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "module_") === 0)
					{
						$module = str_replace("module_", "", $item->get_title());
// modules
						if (JFolder::exists(JPATH_SITE . "/modules/$module"))
						{

							$xmlfiles1 = JFolder::files(JPATH_SITE . "/modules/$module", "\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (realpath($manifest) != $manifest)
									continue;
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app = new stdClass();
								$app->name = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name = "module_" . str_replace(".xml", "", basename($manifest));
								$apps[$name] = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "plugin_") === 0)
					{
						$plugin = explode("_", str_replace("plugin_", "", $item->get_title()), 2);
						if (count($plugin) < 2)
							continue;
// plugins
						if ((JVersion::isCompatible("1.6") && JFolder::exists(JPATH_SITE . "/plugins/" . $plugin[0] . "/" . $plugin[1])) ||
							(!JVersion::isCompatible("1.6") && JFolder::exists(JPATH_SITE . "/plugins/" . $plugin[0]))) 
						{

// plugins
							if (JVersion::isCompatible("1.6"))
							{
								$xmlfiles1 = JFolder::files(JPATH_SITE . "/plugins/" . $plugin[0] . "/" . $plugin[1], "\.xml", true, true);
							}
							else
							{
								$xmlfiles1 = JFolder::files(JPATH_SITE . "/plugins/" . $plugin[0], $plugin[1] . "\.xml", true, true);
							}

							foreach ($xmlfiles1 as $manifest)
							{
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app = new stdClass();
								$app->name = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name = str_replace(".xml", "", basename($manifest));
								if (JVersion::isCompatible("1.6"))
								{
									$name = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;
								}
								else
								{
// simulate Joomla 1.7 directory structure
									$name = "plugin_" . basename(dirname($manifest)) . "_" . $name;
								}
								$apps[$name] = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "component_") === 0)
					{
						$component = str_replace("component_", "", $item->get_title());

						if (JFolder::exists(JPATH_ADMINISTRATOR . "/components/" . $component))
						{

// modules
							$xmlfiles1 = JFolder::files(JPATH_ADMINISTRATOR . "/components/" . $component, "\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app = new stdClass();
								$app->name = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name = "component_" . basename(dirname($manifest));
								$apps[$name] = $app;
							}
						}
					}
					else
					{
						continue;
					}

					foreach ($apps as $appname => $app)
					{
						$iteminfo = json_decode($item->get_description());
						if (version_compare($app->version, $iteminfo->version, "<"))
						{
							$link = $iteminfo->link != "" ? "<a href='" . $iteminfo->link . "' target='_blank'>" . $app->name . "</a>" : $app->name;
							if ($iteminfo->criticalversion != "" && version_compare($app->version, $iteminfo->criticalversion, "<"))
							{
								$rows[] = array($link, $appname, $app->version, $iteminfo->version, "<strong>" . $iteminfo->criticalversion . "</strong>");
							}
							else
							{
								$rows[] = array($link, $appname, $app->version, $iteminfo->version, "");
							}
						}
					}
				}

				if (count($rows) > 0)
				{
					$output = '<table class="versionstatuslist"><tr>';
					$output .= '<th>' . JText::_("JEV_APPNAME") . '</th>';
					$output .= '<th>' . JText::_("JEV_APPCODE") . '</th>';
					$output .= '<th>' . JText::_("JEV_CURRENTVERSION") . '</th>';
					$output .= '<th>' . JText::_("JEV_LATESTVERSION") . '</th>';
					$output .= '<th>' . JText::_("JEV_CRITICALVERSION") . '</th>';
					$output .= '</tr>';
					$k = 0;
					foreach ($rows as $row)
					{
						$output .= '<tr class="row' . $k . '"><td>';
						$output .= implode("</td><td>", $row);
						$output .= '</td></tr>';
						$k = ($k + 1) % 2;
					}
					$output .= '</table>';
					$needsupdate = true;
					return $output;
				}
			}
		}
		return false;

	}

	private function getValidManifestFile($manifest)
	{
		$filecontent = JFile::read($manifest);
		if (strpos($filecontent, "jevents.net") === false && strpos($filecontent, "gwesystems.com") === false && strpos($filecontent, "joomlacontenteditor") === false && strpos($filecontent, "virtuemart") === false)
		{
			return false;
		}
		$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
		if (!$manifestdata)
			return false;
		if (strpos($manifestdata["authorUrl"], "jevents") === false && strpos($manifestdata["authorUrl"], "gwesystems") === false && strpos($manifestdata["authorUrl"], "joomlacontenteditor") === false && strpos($manifestdata["authorUrl"], "virtuemart") === false)
		{
			return false;
		}
		return $manifestdata;

	}

	private function generateVersionsFile($rssDoc)
	{
		if (JRequest::getInt("versions",0)==0){
			return;
		}
		jimport("joomla.filesystem.folder");

		$apps = array();

// club layouts			 
		$xmlfiles1 = JFolder::files(JEV_PATH . "views", "manifest\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$apps["layout_" . basename(dirname($manifest))] = $app;
		}

// plugins
		if (JFolder::exists(JPATH_SITE . "/plugins"))
		{
			$xmlfiles2 = JFolder::files(JPATH_SITE . "/plugins", "\.xml", true, true);
		}
		else
		{
			$xmlfiles2 = array();
		}

		foreach ($xmlfiles2 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name = str_replace(".xml", "", basename($manifest));
			if (JVersion::isCompatible("1.6"))
			{
				$name = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;
			}
			else
			{
// simulate Joomla 1.7 directory structure
				$name = "plugin_" . basename(dirname($manifest)) . "_" . $name;
			}
			$apps[$name] = $app;
		}

// components (including JEvents
		$xmlfiles3 = JFolder::files(JPATH_ADMINISTRATOR . "/components", "manifest\.xml", true, true);
		foreach ($xmlfiles3 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name = "component_" . basename(dirname($manifest));
			$apps[$name] = $app;
		}


// modules
		if (JFolder::exists(JPATH_SITE . "/modules"))
		{
			$xmlfiles4 = JFolder::files(JPATH_SITE . "/modules", "\.xml", true, true);
		}
		else
		{
			$xmlfiles4 = array();
		}
		foreach ($xmlfiles4 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$app->criticalversion = "";
			$name = "module_" . str_replace(".xml", "", basename($manifest));
			$apps[$name] = $app;
		}

// setup the XML file for server	
		/*
		  $output = '$catmapping = array(' . "\n";
		  foreach ($apps as $appname => $app)
		  {
		  $output .='"' . $appname . '"=> 0,' . "\n";
		  }
		  $output = substr($output, 0, strlen($output) - 2) . ");\n\n";
		 */
		$criticaldata = JFile::read('http://ubu.jev20j16.com/importantversions.txt');
		$criticaldata = explode("\n", $criticaldata);
		$criticals = array();
		foreach ($criticaldata as $critical)
		{
			$critical = explode("|", $critical);
			if (count($critical) > 1)
			{
				$criticals[$critical[0]] = $critical[1];
			}
		}
		$catmapping = array(
			"layout_extplus" => 3,
			"layout_iconic" => 3,
			"layout_ruthin" => 3,
			"layout_smartphone" => 3,
			"layout_map" => 3,
			"plugin_acymailing_tagjevents" => 41,
			"plugin_community_jevents" => 7,
			"plugin_content_jevcreator" => 34,
			"plugin_content_jevent_embed" => 12,
			"plugin_jevents_agendaminutes" => 12,
			"plugin_jevents_jevanonuser" => 25,
			"plugin_jevents_jevcalendar" => 15,
			"plugin_jevents_jevcatcal" => 15,
			"plugin_jevents_jevcb" => 18,
			"plugin_jevents_jevcck" => 64,
			"plugin_jevents_jevcustomfields" => 10,
			"plugin_jevents_jevfacebook" => 46,
			"plugin_jevents_jevfeatured" => 16,
			"plugin_jevents_jevfiles" => 24,
			"plugin_jevents_jevhiddendetail" => 51,
			"plugin_jevents_jevjsstream" => 7,
			"plugin_jevents_jevjxcoments" => 19,
			"plugin_jevents_jevlocations" => 4,
			"plugin_jevents_jevmatchingevents" => 47,
			"plugin_jevents_jevmetatags" => 58,
			"plugin_jevents_jevmissingevents" => 56,
			"plugin_jevents_jevnotify" => 61,
			"plugin_jevents_jevpaidsubs" => 48,
			"plugin_jevents_jevpeople" => 13,
			"plugin_jevents_jevpopupdetail" => 50,
			"plugin_jevents_jevrsvp" => 14,
			"plugin_jevents_jevrsvppro" => 62,
			"plugin_jevents_jevsessions" => 21,
			"plugin_jevents_jevtags" => 9,
			"plugin_jevents_jevtimelimit" => 17,
			"plugin_jevents_jevusers" => 8,
			"plugin_jevents_jevweekdays" => 59,
			"plugin_jnews_jnewsjevents" => 24,
			"plugin_rsvppro_manual" => 62,
			"plugin_rsvppro_paypalipn" => 62,
			"plugin_rsvppro_virtuemart" => 62,
			"plugin_search_eventsearch" => 52,
			"plugin_search_jevlocsearch" => 4,
			"plugin_search_jevtagsearch" => 9,
			"plugin_system_autotweetjevents" => 45,
			"plugin_user_juser" => 24,
			"component_com_attend_jevents" => 21,
			//"component_com_jevents" => 52, // JEvents 2.0
			//"component_com_jevents" => 65, // JEvents 2.1
			"component_com_jevents" => 71, // JEvents 2.2
			"component_com_jeventstags" => 9,
			"component_com_jevlocations-old" => 4,
			"component_com_jevlocations" => 4,
			"component_com_jevpeople" => 13,
			"component_com_rsvppro" => 62,
			"module_mod_jevents_cal" => 52,
			"module_mod_jevents_categories" => 52,
			//"module_mod_jevents_filter" => 52,
			//"module_mod_jevents_latest" => 52,
			//"module_mod_jevents_legend" => 52,
			"module_mod_jevents_filter" => 71,
			"module_mod_jevents_latest" => 71,
			"module_mod_jevents_legend" => 71,
			"module_mod_jevents_notify" => 61,
			"module_mod_jevents_paidsubs" => 48,
			"module_mod_jevents_switchview" => 52
			);
		foreach ($apps as $appname => $app)
		{
			$row = new stdClass();
			$row->version = $app->version;
			$row->criticalversion = "";
			if (array_key_exists($appname, $criticals))
			{
				$row->criticalversion = $criticals[$appname];
			}
			$row->link = array_key_exists($appname, $catmapping) ? "http://www.jevents.net/downloads/category/" . $catmapping[$appname] : "";
			if ($row->link == "")
				continue;
			$output .= "<item>\n<title>$appname</title>\n<description><![CDATA[" . json_encode($row) . "]]></description>\n</item>\n";
		}
		$output .= "";
		$needsupdate = true;
		ob_end_clean();
		echo $output;
		die();

	}

	public function renderVersionsForClipboard(){
		if (!JEVHelper::isAdminUser())	{
			return;
		}

		jimport("joomla.filesystem.folder");
		$apps = array();

		// Joomla
		$app = new stdClass();
		$app->name = "Joomla";
		$version = new JVersion();
		$app->version = $version->getShortVersion();
		$apps[$app->name] = $app;
		
// components (including JEvents
		$xmlfiles3 = JFolder::files(JPATH_ADMINISTRATOR . "/components", "manifest\.xml", true, true);
		foreach ($xmlfiles3 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name = "component_" . basename(dirname($manifest));
			$apps[$name] = $app;
		}


// modules
		if (JFolder::exists(JPATH_SITE . "/modules"))
		{
			$xmlfiles4 = JFolder::files(JPATH_SITE . "/modules", "\.xml", true, true);
		}
		else
		{
			$xmlfiles4 = array();
		}
		foreach ($xmlfiles4 as $manifest)
		{
			if (strpos($manifest,"mod_")===false) continue;
			
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$app->criticalversion = "";
			$name = "module_" . str_replace(".xml", "", basename($manifest));
			$apps[$name] = $app;
		}
		
// club layouts			 
		$xmlfiles1 = JFolder::files(JEV_PATH . "views", "manifest\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (realpath($manifest) != $manifest)
				continue;
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$apps["layout_" . basename(dirname($manifest))] = $app;
		}

// plugins
		if (JFolder::exists(JPATH_SITE . "/plugins"))
		{
			$xmlfiles2 = JFolder::files(JPATH_SITE . "/plugins", "\.xml", true, true);
		}
		else
		{
			$xmlfiles2 = array();
		}

		foreach ($xmlfiles2 as $manifest)
		{
			if (strpos($manifest,"Zend")>0) continue;
			
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name = str_replace(".xml", "", basename($manifest));
			if (JVersion::isCompatible("1.6"))
			{
				$group =  basename(dirname(dirname($manifest))) ;
			}
			else
			{
				$group =   basename(dirname($manifest)) ;
			}
			$plugin = JPluginHelper::getPlugin( $group,$name);
			if (!$plugin) {
				$app->version .= " (not enabled)";
			} 
			
			$name = "plugin_" .$group. "_" . $name;
			$apps[$name] = $app;
		}

		$output = "<textarea rows='40' cols='80'>[code]\n";
		
		foreach ($apps as $appname => $app)
		{
			$output .= "$appname : $app->version\n";
		}
		$output .= "[/code]</textarea>";
		return $output;
	}
	
	function limitText($text, $wordcount)
	{
		if (!$wordcount)
		{
			return $text;
		}

		$texts = explode(' ', $text);
		$count = count($texts);

		if ($count > $wordcount)
		{
			$text = '';
			for ($i = 0; $i < $wordcount; $i++)
			{
				$text .= ' ' . $texts[$i];
			}
			$text .= '...';
		}

		return $text;

	}
}

