<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
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

		$output = '';

		//  get RSS parsed object
		$options = array();
		$options['rssUrl'] = 'http://www.jevents.net/jevnews?format=feed&type=rss';
		$options['cache_time'] = 86400;

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
		return $output;

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
				$cache = JFactory::getCache('feed_parser', 'callback');
				$cache->setLifeTime($cache_time);

				$rssDoc = new SimplePie(null, null, 0);

				$rssDoc->enable_cache(false);
				$rssDoc->set_feed_url($rssUrl);
				$rssDoc->force_feed(true);
				$rssDoc->set_item_limit(999);

				$results = $cache->get(array($rssDoc, 'init'), null, false, false);
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

				jimport("joomla.filesystem.folder");

				$apps = array();

				// club layouts			 
				$xmlfiles1 = JFolder::files(JEV_PATH . "views/", "manifest\.xml", true, true);
				foreach ($xmlfiles1 as $manifest)
				{
					if (realpath($manifest) != $manifest) continue;					
					$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);

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
					$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
					if (!$manifestdata)
						continue;
					if (strpos($manifestdata["authorUrl"], "jevents") === false && strpos($manifestdata["authorUrl"], "gwesystems") === false)
					{
						continue;
					}
					$app = new stdClass();
					$app->name = $manifestdata["name"];
					$app->version = $manifestdata["version"];
					$name = str_replace(".xml", "", basename($manifest));
					if (JVersion::isCompatible("1.6")){
						$name = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;
					}
					else {
						// simulate Joomla 1.7 directory structure
						$name = "plugin_" . basename(dirname($manifest)). "_" . $name;
					}
					$apps[$name] = $app;
				}

				// components (including JEvents
				$xmlfiles3 = JFolder::files(JPATH_ADMINISTRATOR . "/components", "manifest\.xml", true, true);
				foreach ($xmlfiles3 as $manifest)
				{
					$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
					if (strpos($manifestdata["authorUrl"], "jevents") === false && strpos($manifestdata["authorUrl"], "gwesystems") === false)
					{
						continue;
					}

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
					$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
					if (!$manifestdata)
						continue;
					if (strpos($manifestdata["authorUrl"], "jevents") === false && strpos($manifestdata["authorUrl"], "gwesystems") === false)
					{
						continue;
					}
					$app = new stdClass();
					$app->name = $manifestdata["name"];
					$app->version = $manifestdata["version"];
					$app->criticalversion = "";
					$name = "module_" . str_replace(".xml", "", basename($manifest));
					$apps[$name] = $app;
				}

				// setup the XML file for server	
				if (false)
				{
					/*
					  $output = '$catmapping = array(' . "\n";
					  foreach ($apps as $appname => $app)
					  {
					  $output .='"' . $appname . '"=> 0,' . "\n";
					  }
					  $output = substr($output, 0, strlen($output) - 2) . ");\n\n";
					 */
					$catmapping = array(
						"layout_extplus" => 3,
						"layout_iconic" => 3,
						"layout_ruthin" => 3,
						"layout_smartphone" => 3,
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
						"plugin_jevents_jevnotify" => 61,
						"plugin_jevents_jevpaidsubs" => 48,
						"plugin_jevents_jevpeople" => 13,
						"plugin_jevents_jevpopupdetail" => 50,
						"plugin_jevents_jevrsvp" => 14,
						"plugin_jevents_jevrsvppro" => 12,
						"plugin_jevents_jevsessions" => 21,
						"plugin_jevents_jevtags" => 9,
						"plugin_jevents_jevtimelimit" => 17,
						"plugin_jevents_jevusers" => 8,
						"plugin_jevents_jevweekdays" => 59,
						"plugin_jnews_jnewsjevents" => 24,
						"plugin_rsvppro_manual" => 12,
						"plugin_rsvppro_paypalipn" => 12,
						"plugin_rsvppro_virtuemart" => 12,
						"plugin_search_eventsearch" => 52,
						"plugin_search_jevlocsearch" => 4,
						"plugin_search_jevtagsearch" => 9,
						"plugin_system_autotweetjevents" => 45,
						"plugin_user_juser" => 24,
						"component_com_attend_jevents" => 21,
						"component_com_jevents" => 52,
						"component_com_jeventstags" => 9,
						"component_com_jevlocations-old" => 4,
						"component_com_jevlocations" => 4,
						"component_com_jevpeople" => 13,
						"component_com_rsvppro" => 12,
						"module_mod_jevents_cal" => 52,
						"module_mod_jevents_categories" => 52,
						"module_mod_jevents_filter" => 52,
						"module_mod_jevents_latest" => 52,
						"module_mod_jevents_legend" => 52,
						"module_mod_jevents_notify" => 61,
						"module_mod_jevents_paidsubs" => 48,
						"module_mod_jevents_switchview" => 52);
					foreach ($apps as $appname => $app)
					{
						$row = new stdClass();
						$row->version = $app->version;
						$row->criticalversion = "";
						$row->link = array_key_exists($appname, $catmapping) ? "http://www.jevents.net/downloads/category/" . $catmapping[$appname] : "";
						$output .= "<item>\n<title>$appname</title>\n<description><![CDATA[" . json_encode($row) . "]]></description>\n</item>\n";
					}
					$output .= "";
					$needsupdate = true;
					ob_end_clean();
					echo $output;
					die();
				}

				$rows = array();
				$items = $rssDoc->get_items();

				foreach ($apps as $appname => $app)
				{
					$setup[] = $app;
					$app->done = false;
					foreach ($items as $item)
					{
						if ($item->get_title() == $appname)
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
							$app->done = true;
						}
					}
					if (!$app->done)
					{
						$rows[] = array($app->name, $appname, $app->version, "", "");
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
					$output .= '</table';
					$needsupdate = true;
					return $output;
				}
			}
		}
		return false;

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