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
class AdminCpanelViewCpanel extends JEventsAbstractView
{

	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function cpanel($tpl = null)
	{
		jimport('joomla.html.pane');

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'), 'jevents');

		JEventsHelper::addSubmenu();

		JHTML::_('behavior.tooltip');

		if (JevJoomlaVersion::isCompatible("3.0"))
		{
			$this->sidebar = JHtmlSidebar::render();
		}
		else
		{
			$this->setLayout("cpanel25");
		}

		$this->setUpdateUrls();

	}

	/**
	 * render News feed from JEvents portal
	 */
	function renderJEventsNews()
	{
		$cache = JFactory::getCache(JEV_COM_COMPONENT, 'view');
		$cache->setLifeTime(86400);
		// In Joomla 1.7 caching of feeds doesn't work!
		$cache->setCaching(true);

		$app = JFactory::getApplication();
		if (!isset($app->registeredurlparams))
		{
			$app->registeredurlparams = new stdClass();
		}

		$cache->get($this, 'renderJEventsNewsCached');

	}

	function renderJEventsNewsCached()
	{

		$output = '';

		//  get RSS parsed object
		$options = array();
		$options['rssUrl'] = 'http://www.jevents.net/jevnews?format=feed&type=rss';
		$options['cache_time'] = 0;

		error_reporting(0);
		ini_set('display_errors',0);

		$rssDoc = JFactory::getFeedParser($options['rssUrl'], $options['cache_time']);

		//$rssDoc =  JFactory::getXMLparser('RSS', $options);

		if ($rssDoc == false)
		{
			$output = JText::_('Error: Feed not retrieved');
		}
		else
		{
// channel header and link
			$title = str_replace(" ", "_", $rssDoc->get_title());
			$link = $rssDoc->get_link();

			$output = '<table class="adminlist   table table-striped">';
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
		jimport("joomla.filesystem.folder");
		if (JEVHelper::isAdminUser())
		{

//  get RSS parsed object
			$options = array();
			// point Joomla 2.5+ users towards the new versions of everything
			if (JevJoomlaVersion::isCompatible("2.5"))
			{
				$rssUrl = 'http://www.jevents.net/versions30.xml';
			}
			else
			{
				$rssUrl = 'http://www.jevents.net/versions.xml';
			}
			$cache_time = 86400;

			error_reporting(0);
			ini_set('display_errors',0);

			jimport('simplepie.simplepie');

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
							if ($xmlfiles1 && count($xmlfiles1) > 0)
							{
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
						// package version
						if (JFolder::exists(JPATH_ADMINISTRATOR . "/manifests/files"))
						{
// club layouts
							$xmlfiles1 = JFolder::files(JPATH_ADMINISTRATOR . "/manifests/files", "$layout\.xml", true, true);
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
						if ((JFolder::exists(JPATH_SITE . "/plugins/" . $plugin[0] . "/" . $plugin[1])))
						{
// plugins
							$xmlfiles1 = JFolder::files(JPATH_SITE . "/plugins/" . $plugin[0] . "/" . $plugin[1], "\.xml", true, true);

							foreach ($xmlfiles1 as $manifest)
							{
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app = new stdClass();
								$app->name = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name = str_replace(".xml", "", basename($manifest));
								$name = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;
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

	private
			function getValidManifestFile($manifest)
	{
		$filecontent = JFile::read($manifest);
		if (stripos($filecontent, "jevents.net") === false && stripos($filecontent, "gwesystems.com") === false && stripos($filecontent, "joomlacontenteditor") === false && stripos($filecontent, "virtuemart") === false && stripos($filecontent, "sh404sef") === false && stripos($filecontent, "TechJoomla") === false && stripos($filecontent, "hikashop") === false )
		{
			return false;
		}
		// for JCE and Virtuemart only check component version number
		if (stripos($filecontent, "joomlacontenteditor") !== false || stripos($filecontent, "virtuemart") !== false || stripos($filecontent, "sh404sef") !== false || strpos($filecontent, "JCE") !== false || strpos($filecontent, "TechJoomla") !== false || strpos($filecontent, "hikashop") !== false)
		{
			if (strpos($filecontent, "type='component'") === false && strpos($filecontent, 'type="component"') === false)
			{
				return false;
			}
		}

		$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
		if (!$manifestdata)
			return false;
		if (strpos($manifestdata["authorUrl"], "jevents") === false && strpos($manifestdata["authorUrl"], "gwesystems") === false && strpos($manifestdata["authorUrl"], "joomlacontenteditor") === false && strpos($manifestdata["authorUrl"], "virtuemart") === false && strpos($manifestdata['name'], "sh404SEF") === false && strpos($manifestdata['author'], "TechJoomla") === false && strpos($manifestdata['name'], "HikaShop") === false)
		{
			return false;
		}
		return $manifestdata;

	}

	private
			function generateVersionsFile($rssDoc)
	{
		if (JRequest::getInt("versions", 0) == 0)
		{
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

		$xmlfiles1 = JFolder::files(JPATH_MANIFESTS . "/files", "\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$apps["layout_" . str_replace(".xml", "", basename($manifest))] = $app;
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

			$name = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;

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
			"plugin_content_jevent_embed" => 113,
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
			"plugin_jevents_jevmissingevent" => 56,
			"plugin_jevents_jevnotify" => 61,
			"plugin_jevents_jevpaidsubs" => 48,
			"plugin_jevents_jevpeople" => 13,
			"plugin_jevents_jevpopupdetail" => 50,
			"plugin_jevents_jevrsvp" => 14,
			"plugin_jevents_jevrsvppro" => 62,
			"plugin_jevents_jevsendfb" => 45,
			"plugin_jevents_jevsessions" => 21,
			"plugin_jevents_jevtags" => 9,
			"plugin_jevents_jevtimelimit" => 17,
			"plugin_jevents_jevusers" => 8,
			"plugin_jevents_jevweekdays" => 59,
			"plugin_jnews_jnewsjevents" => 24,
			"plugin_rsvppro_manual" => 62,
			"plugin_rsvppro_paypalipn" => 62,
			"plugin_rsvppro_virtuemart" => 62,
			//"plugin_search_eventsearch" => 52, // JEvents 2.2
			"plugin_search_eventsearch" => 71,
			"plugin_search_jevlocsearch" => 4,
			"plugin_search_jevtagsearch" => 9,
			"plugin_system_autotweetjevents" => 45,
			"plugin_user_juser" => 24,
			"component_com_attend_jevents" => 21,
			//"component_com_jevents" => 52, // JEvents 2.0
			//"component_com_jevents" => 65, // JEvents 2.1
			"component_com_jevents" => 71, // JEvents 3.0
			"component_com_jeventstags" => 9,
			"component_com_jevlocations-old" => 4,
			"component_com_jevlocations" => 4,
			"component_com_jevpeople" => 13,
			"component_com_rsvppro" => 62,
			"module_mod_jevents_cal" => 71,
			"module_mod_jevents_categories" => 76,
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

	public
			function renderVersionsForClipboard()
	{
		if (!JEVHelper::isAdminUser())
		{
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

// components (including JEvents)
		$xmlfiles3 = array_merge(JFolder::files(JPATH_ADMINISTRATOR . "/components", "manifest\.xml", true, true), JFolder::files(JPATH_ADMINISTRATOR . "/components", "sh404sef\.xml", true, true), JFolder::files(JPATH_ADMINISTRATOR . "/components", "virtuemart\.xml", true, true), JFolder::files(JPATH_ADMINISTRATOR . "/components", "jce\.xml", true, true), JFolder::files(JPATH_ADMINISTRATOR . "/components", "jmailalerts\.xml", true, true), JFolder::files(JPATH_ADMINISTRATOR . "/components", "hikashop\.xml", true, true), JFolder::files(JPATH_ADMINISTRATOR . "/components", "jev_latestevents\.xml", true, true)
		);
		foreach ($xmlfiles3 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			// is sh404sef disabled ?
			if (basename(dirname($manifest)) == "com_sh404sef")
			{
				if (is_callable("Sh404sefFactory::getConfig"))
				{
					$sefConfig = Sh404sefFactory::getConfig();
					if (!$sefConfig->Enabled)
					{
						$app->version = $manifestdata["version"] . " (Disabled in SH404 settings)";
					}
				}
				else
				{
					$app->version = $manifestdata["version"] . " (sh404sef system plugins not enabled)";
				}
			}
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
			if (strpos($manifest, "mod_") === false)
				continue;

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

		$xmlfiles1 = JFolder::files(JPATH_ADMINISTRATOR . "/manifests/files", "\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (realpath($manifest) != $manifest)
				continue;
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$apps[str_replace(".xml", "", "layout_" . basename($manifest))] = $app;
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
			if (strpos($manifest, "Zend") > 0)
				continue;

			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app = new stdClass();
			$app->name = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name = str_replace(".xml", "", basename($manifest));
			$group = basename(dirname(dirname($manifest)));
			$plugin = JPluginHelper::getPlugin($group, $name);
			if (!$plugin)
			{
				$app->version .= " (not enabled)";
			}

			$name = "plugin_" . $group . "_" . $name;
			$apps[$name] = $app;
		}

		$output = "<textarea rows='40' cols='80' class='versionsinfo'>[code]\n";
		$output .= "PHP Version : " . phpversion() . "\n";
		$output .= "MySQL Version : " .JFactory::getDbo()->getVersion(). "\n";
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

	function getTranslatorLink()
	{
		$translatorUrl = JText::_("JEV_TRANSLATION_AUTHOR");
		//$translatorUrl = JText::_("JEV_TRANSLATION_AUTHOR_URL");
		//$translatorUrl = "<a href=\"$translatorUrl\">$translatorName</a>";

		return $translatorUrl;

	}

	function support()
	{
		jimport('joomla.html.pane');

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'));

		JToolBarHelper::title(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'), 'jevents');

		JEventsHelper::addSubmenu();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

                if (ini_get("max_input_vars")>0 && ini_get("max_input_vars")<=1000){
                    JError::raiseNotice(234,JText::sprintf("MAX_INPUT_VARS_LOW_WARNING",ini_get("max_input_vars")));
                }
                

		if (JevJoomlaVersion::isCompatible("3.0"))
		{
			$this->sidebar = JHtmlSidebar::render();
		}

	}

	function custom_css()
	{
		jimport('joomla.html.pane');

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'));

		JToolBarHelper::title(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'), 'jevents');

		JEventsHelper::addSubmenu();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		if (JevJoomlaVersion::isCompatible("3.0"))
		{
			$this->sidebar = JHtmlSidebar::render();
		}

	}

	function setUpdateUrls()
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		$updates = array(
			array("element"=>"pkg_jevents","name"=>"com_jevents", "type"=>"package"),
			array("element"=>"pkg_jevlocations","name"=>"com_jevlocations", "type"=>"package"),
			array("element"=>"pkg_jevpeople","name"=>"com_jevpeople", "type"=>"package"),
			array("element"=>"pkg_rsvppro","name"=>"com_rsvppro", "type"=>"package"),
			array("element"=>"pkg_jeventstags","name"=>"com_jeventstags", "type"=>"package"),

			// Silver - AnonUsers
			array("element"=>"jevanonuser","name"=>"jevanonuser","folder"=>"jevents", "type"=>"plugin"),
			// Silver - AutoTweet
			array("element"=>"jevsendfb","name"=>"jevsendfb","folder"=>"jevents", "type"=>"plugin"),
			array("element"=>"autotweetjevents","name"=>"autotweetjevents","folder"=>"system", "type"=>"plugin"),
			// Silver - MatchingEvents
			array("element"=>"jevmatchingevents","name"=>"jevmatchingevents","folder"=>"jevents", "type"=>"plugin"),
			// Silver - StandardImage
			array("element"=>"jevfiles","name"=>"jevfiles","folder"=>"jevents", "type"=>"plugin"),
			// Silver - agendaminutes
			array("element"=>"agendaminutes","name"=>"agendaminutes","folder"=>"jevents", "type"=>"plugin"),
			array("element"=>"jevent_embed","name"=>"jevent_embed","folder"=>"content", "type"=>"plugin"),
			// Silver - authorisedusers
			array("element"=>"jevuser","name"=>"jevuser","folder"=>"user", "type"=>"plugin"),
			// Silver - calendar
			array("element"=>"jevcalendar","name"=>"jevcalendar","folder"=>"jevents", "type"=>"plugin"),
			// Silver - catcal
			array("element"=>"jevcatcal","name"=>"jevcatcal","folder"=>"jevents", "type"=>"plugin"),
			// Silver - cck
			array("element"=>"jevcck","name"=>"jevcck","folder"=>"jevents", "type"=>"plugin"),
			array("element"=>"k2embedded","name"=>"k2embedded","folder"=>"k2", "type"=>"plugin"),
			// Silver - creator
			array("element"=>"jevcreator","name"=>"jevcreator","folder"=>"content", "type"=>"plugin"),
			// Silver - customfields
			array("element"=>"jevcustomfields","name"=>"jevcustomfields","folder"=>"jevents", "type"=>"plugin"),
			// Silver - Dynamic legend
			array("element"=>"mod_jevents_dynamiclegend","name"=>"mod_jevents_dynamiclegend","type"=>"module"),
			// Silver - Calendar Plus
			array("element"=>"mod_jevents_calendarplus","name"=>"mod_jevents_calendarplus","type"=>"module"),
			// Silver - Slideshow Module
			array("element"=>"mod_jevents_slideshow","name"=>"mod_jevents_slideshow","type"=>"module"),
			// Silver - facebook
			array("element"=>"jevfacebook","name"=>"jevfacebook","folder"=>"jevents", "type"=>"plugin"),
			// Silver - featured
			array("element"=>"jevfeatured","name"=>"jevfeatured","folder"=>"jevents", "type"=>"plugin"),
			// Silver - hiddendetail
			array("element"=>"jevhiddendetail","name"=>"jevhiddendetail","folder"=>"jevents", "type"=>"plugin"),
			// Silver - jomsocial -  TODO
			array("element"=>"jevjsstream","name"=>"jevjsstream","folder"=>"jevents", "type"=>"plugin"),
			array("element"=>"jevents","name"=>"jevents","folder"=>"community", "type"=>"plugin"),
			// Silver - layouts
			array("element"=>"extplus","name"=>"extplus","type"=>"file"),
			array("element"=>"ruthin","name"=>"ruthin","type"=>"file"),
			array("element"=>"iconic","name"=>"iconic","type"=>"file"),
			array("element"=>"map","name"=>"map","type"=>"file"),
			array("element"=>"smartphone","name"=>"smartphone","type"=>"file"),
			array("element"=>"zim","name"=>"zim","type"=>"file"),
			// Silver - Jevents Categories
			array("element"=>"mod_jevents_categories","name"=>"mod_jevents_categories","type"=>"module"),
			// Silver - Newsletters - some TODO
			array("element"=>"tagjevents_jevents","name"=>"tagjevents_jevents","folder"=>"acymailing", "type"=>"plugin"),
			// Silver - Nnotifications
			array("element"=>"jevnotify","name"=>"jevnotify","folder"=>"jevents", "type"=>"plugin"),
			array("element"=>"mod_jevents_notify","name"=>"mod_jevents_notify","type"=>"module"),
			// Silver - simpleattend
			array("element"=>"jevrsvp","name"=>"jevrsvp","folder"=>"jevents", "type"=>"plugin"),
			// Silver - tabbed modules
			array("element"=>"mod_tabbedmodules","name"=>"mod_tabbedmodules","type"=>"module"),
			// Silver - time Limit
			array("element"=>"jevtimelimit","name"=>"jevtimelimit","folder"=>"jevents", "type"=>"plugin"),
			// Silver - User Events
			array("element"=>"jevusers","name"=>"jevusers","folder"=>"jevents", "type"=>"plugin"),
			// Silver - Week Days
			array("element"=>"jevweekdays","name"=>"jevweekdays","folder"=>"jevents", "type"=>"plugin"),
			
			// GOLD addons - PaidSubs - TODO check Virtuemart for Joomla 3.0 is available
			array("element"=>"jevpaidsubs","name"=>"jevpaidsubs","folder"=>"jevents", "type"=>"plugin"),
			array("element"=>"mod_jevents_paidsubs","name"=>"mod_jevents_paidsubs","type"=>"module"),

			// Translations - TODO club translations.  Normal JEvents translations handled below!

			// Bronze - editor button
			array("element"=>"jevents","name"=>"jevents","folder"=>"editors-xtd", "type"=>"plugin"),

			// Bronze - Meta tags
			array("element"=>"jevmetatags","name"=>"jevmetatags","folder"=>"jevents", "type"=>"plugin"),

			// Bronze - Missing Events
			array("element"=>"jevmissingevent","name"=>"jevmissingevent","folder"=>"jevents", "type"=>"plugin"),

			// Bronze - Popups
			array("element"=>"jevpopupdetail","name"=>"jevpopupdetail","folder"=>"jevents", "type"=>"plugin"),

			// Bronze - sh404sef - TODO

		);
		// Do the language files for Joomla
		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__extensions where type='file' AND element LIKE '%_JEvents'");
		$translations = $db->loadObjectList();
		foreach ($translations  as $translation){
			//	array("element"=>"ar-AA_JEvents","name"=>"Arabic translation for JEvents","type"=>"file"),
			$updates[]= array("element"=>$translation->element,"name"=>$translation->name,"type"=>"file");
		}


		foreach ($updates as $package)
		{
			$this->setUpdateUrlsByPackage($package) ;
		}
	}

	function setUpdateUrlsByPackage($package)
	{
		$pkg = $package["element"];
		$com= $package["name"];
		$folder= isset( $package["folder"])?  $package["folder"] : "";
		$type= $package["type"];

		$db = JFactory::getDbo();

		// Process the package
		$db = JFactory::getDbo();
		// Do we already have a record for the update URL for the component - we should remove this in JEvents 3.0!!
		if ($folder=="") {
			$this->removeComponentUpdate($com);
		}

		// Now check and setup the package update URL
		$db->setQuery("select *, exn.extension_id as extension_id , exn.type as extension_type from #__extensions as exn
LEFT JOIN #__update_sites_extensions as map on map.extension_id=exn.extension_id
LEFT JOIN #__update_sites as us on us.update_site_id=map.update_site_id
where exn.type='$type'
and exn.element='$pkg' and exn.folder='$folder'
");

		$pkgupdate = $db->loadObject();
		// we have a package and an update record
		if ($pkgupdate && $pkgupdate->update_site_id)
		{
			// Now update package update URL
			$this->setPackageUpdateUrl($pkgupdate);
		}
		// we have a package but not an update record
		else if ($pkgupdate && $pkgupdate->extension_id)
		{
			// Now set package update URL
			$this->setPackageUpdateUrl($pkgupdate);
		}
		else
		{
			// No package installed so fall back to component and set it to update using the package URL :)

			// Do we already have a record for the update URL for the component - we should remove this
			$db->setQuery("select *, exn.extension_id as extension_id  from #__extensions as exn
	LEFT JOIN #__update_sites_extensions as map on map.extension_id=exn.extension_id
	LEFT JOIN #__update_sites as us on us.update_site_id=map.update_site_id
	where exn.type='component'
	and exn.element='$com'
	");
			$cpupdate = $db->loadObject();
			if ($cpupdate && $cpupdate->update_site_id)
			{
				$db->setQuery("DELETE FROM #__update_sites where update_site_id=" . $cpupdate->update_site_id);
				$db->query();
				$db->setQuery("DELETE FROM #__update_sites_extensions where update_site_id=" . $cpupdate->update_site_id . " AND extension_id=" . $cpupdate->extension_id);
				$db->query();
			}

			// Now set package update URL for the component as opposed to the package ;)
			if ($cpupdate && $cpupdate->extension_id){
				$this->setPackageUpdateUrl($cpupdate);
			}
		}

	}

	private
			function removeComponentUpdate($com)
	{
		$db = JFactory::getDbo();
		$version = JEventsVersion::getInstance();
		$release = $version->get("RELEASE");

		// Do we already have a record for the update URL for the component - we should remove this in JEvents 3.0!!
		$db->setQuery("select * from #__extensions as exn
	LEFT JOIN #__update_sites_extensions as map on map.extension_id=exn.extension_id
	LEFT JOIN #__update_sites as us on us.update_site_id=map.update_site_id
	where exn.type='component'
	and exn.element='$com'
	");
		$cpupdate = $db->loadObject();
		if ($cpupdate && $cpupdate->update_site_id)
		{
			$db->setQuery("DELETE FROM #__update_sites where update_site_id=" . $cpupdate->update_site_id);
			$db->query();
			$db->setQuery("DELETE FROM #__update_sites_extensions where update_site_id=" . $cpupdate->update_site_id . " AND extension_id=" . $cpupdate->extension_id);
			$db->query();
		}

	}

	private
			function setPackageUpdateUrl($pkgupdate)
	{
		$db  = JFactory::getDbo();

		$sitedomain = rtrim(str_replace(array('https://','http://'),"",JURI::root()),'/');

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$clubcode = $params->get("clubcode","");
		$filter = new JFilterInput();
		$clubcode = $filter->clean($clubcode, "CMD");
		//$clubcode = $filter->clean($clubcode, "BASE64")."-".$sitedomain;
		//$clubcode = base64_encode($clubcode);
		$clubcode = $clubcode . "-".base64_encode($sitedomain);

		$version = new JEventsVersion();
		$version = $version->get('RELEASE');
		$version = str_replace(" ","",$version);
		//$domain = "ubu.jev20j16.com";
		$domain = "www.jevents.net";

		$extension  = JTable::getInstance("Extension");
		$extension->load($pkgupdate->extension_id);

		// Packages are installed with client_id = 0 which stops the update from taking place to we update the extension to client_id=1
		/*
		if ($pkgupdate->client_id==0 && $pkgupdate->extension_type=="package"){
			$db->setQuery("UPDATE #__extensions SET client_id=1 WHERE extension_id = $pkgupdate->extension_id");
			$db->query();
			echo $db->getErrorMsg();
		}
		 */

		// We already have an update site
		if ($pkgupdate->update_site_id){
			$extensionname = str_replace(" ","_",$extension->element);
			if ($extension->folder){
				$extensionname = "plg_".$extension->folder."_".$extensionname;
			}
			/*
			 // set the JEvents Version number in the update URL
			if (isset($extension->manifest_cache)){
				$extensionmanifest = json_decode($extension->manifest_cache);
				if (isset($extensionmanifest->version)) {
					$version = $extensionmanifest->version;
				}
			}
			*/
			$db->setQuery("UPDATE #__update_sites set name=".$db->quote(ucwords($extension->name)).", location=".$db->quote("http://$domain/updates/$clubcode/$extensionname-update-$version.xml").", enabled = 1 WHERE update_site_id=".$pkgupdate->update_site_id);
			$db->query();
			echo $db->getErrorMsg();
		}
		else {
			$extensionname = str_replace(" ","_",$extension->element);
			if ($extension->folder){
				$extensionname = "plg_".$extension->folder."_".$extensionname;
			}
			$db->setQuery("INSERT INTO #__update_sites (name, type, location, enabled, last_check_timestamp) VALUES (".$db->quote(ucwords($extension->name)).",'extension',".$db->quote("http://$domain/updates/$clubcode/$extensionname-update-$version.xml").",'1','0')");
			$db->query();
			echo $db->getErrorMsg();
			$id = $db->insertid();
			echo $db->getErrorMsg();

			$db->setQuery("REPLACE INTO #__update_sites_extensions (update_site_id, extension_id) VALUES ($id, $pkgupdate->extension_id)");
			$db->query();
			echo $db->getErrorMsg();
		}

	}

}

