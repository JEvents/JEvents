<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3543 2012-04-20 08:17:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2020 GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Feed\FeedFactory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\String\StringHelper;

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

		$document = Factory::getDocument();
		$document->setTitle(Text::_('JEVENTS_DASHBOARD'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('JEVENTS_DASHBOARD'), 'jevents');

		JEventsHelper::addSubmenu();

		if (GSLMSIE10)
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		$this->checkForAddons();

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::setUpdateUrls();

		$this->dataModel  = new JEventsDataModel("JEventsAdminDBModel");

		$counts = $this->dataModel->queryModel->getEventCounts();
		$this->totalEvents = $counts[0];
		$this->pastEvents = $counts[2];
		$this->futureEvents = $counts[1];

		$count = $this->dataModel->queryModel->getUnpublishedEventCounts();
		$this->unpublishedEvents = $count;

		$counts = $this->dataModel->queryModel->getNewEventCounts();
		$this->newEvents = $counts[1];
		$this->newThisMonth = $counts[2];

		$counts = $this->dataModel->queryModel->getUpcomingEventAttendeesCounts();
		$this->upcomingAttendees = $counts[1];
		$this->upcomingAttendeesThisMonth = $counts[2];

		$data = $this->dataModel->queryModel->getEventCountsByCategory(8);
		$this->eventsByCat = array();
		$this->eventsByCatCounts = array();

		foreach ($data as $datapoint)
		{
			$this->eventsByCat[] = $datapoint->title;
			$this->eventsByCatCounts[] = $datapoint->count;
			$params = @json_decode($datapoint->params);
			// catch for old typo!
			if (isset($params->catcolor) && !isset($params->catcolour))
			{
				$params->catcolour = $params->catcolor;
			}
			if (isset($params->catcolor) && !empty($params->catcolor) && strtolower($params->catcolor) !== '#ffffff')
			{
				$this->eventsByCatColours[] = $params->catcolour;
			}
			else
			{
				// otherwise a random colour
				$this->eventsByCatColours[] = '#'.str_pad(dechex(mt_rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
			}
		}

		$data = $this->dataModel->queryModel->getEventCountsByDay();

		$this->eventCountsByDay = array(0,0,0,0,0,0,0);

		foreach ($data as $datapoint)
		{
			$this->eventCountsByDay[$datapoint->weekday - 1] = $datapoint->count;
		}

		$data = $this->dataModel->queryModel->getEventCountsByDay();

		$this->eventCountsByDay = array(0,0,0,0,0,0,0);

		foreach ($data as $datapoint)
		{
			$this->eventCountsByDay[$datapoint->weekday - 1] = $datapoint->count;
		}


		$this->attendeeCountsByEvent = $this->dataModel->queryModel->getUpcomingEventAttendees();

		$data = $this->dataModel->queryModel->getEventCountsByWeek();

		$this->eventCountByWeek = array();
		$this->eventCountByWeekLabels = array();
		foreach ($data as $datapoint)
		{
			$this->eventCountByWeek[] = $datapoint->count;
			$this->eventCountByWeekLabels[] = $datapoint->weekstart;
		}

	}

	protected function checkForAddons()
	{

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("clubcode", "") && Joomla\String\StringHelper::strlen($params->get("clubcode", "")) > 10)
		{
			return;
		}

		$db = Factory::getDbo();
		// find list of installed addons
		$installed = 'element="com_jevlocations"  OR element="com_jeventstags"  OR element="com_jevpeople"  OR element="com_rsvppro" ';
		$installed .= ' OR element="extplus"  OR element="ruthin"  OR element="iconic"  OR element="flatplus"   OR element="smartphone" OR element="float"';
		// extend this list !!!
		$installed .= " OR element in ('agendaminutes','jevcustomfields','jevfiles','jevhiddendetail','jevlocations','jevmetatags','jevnotify','jevpeople','jevrsvppro','jevrsvp','jevtags','jevtimelimit','jevusers','jevvalidgroups') ";
		$sql       = 'SELECT element,extension_id FROM #__extensions  where  (
		' . $installed . '
		)';
		$db->setQuery($sql);
		$installed = $db->loadObjectList();

		if (count($installed))
		{
			Factory::getApplication()->enqueueMessage(Text::_("JEV_SET_UPDATER_CODE") . "<br/><br/>" . Text::_("JEV_JOOMLA_UPDATE_CLUBCODE_INFO"), "warning");

			return;
		}
	}

	/**
	 * render News feed from JEvents portal
	 */
	function renderJEventsNews()
	{

		$cache = Factory::getCache(JEV_COM_COMPONENT, 'view');
		$cache->setLifeTime(86400);
		// In Joomla 1.7 caching of feeds doesn't work!
		$cache->setCaching(true);

		$app = Factory::getApplication();
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

		try
		{
			$feed   = new FeedFactory;
			$rssDoc = $feed->getFeed('https://www.jevents.net/jevnews?format=feed&type=rss');
		}
		catch (InvalidArgumentException $e)
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}
		catch (RunTimeException $e)
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}
		catch (LogicException $e)
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}

		if (empty($rssDoc))
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}
		else
		{
// channel header and link
			$title = str_replace(" ", "_", $rssDoc->title);
			$link  = $rssDoc->uri;

			$output = '<table class="adminlist   table table-striped">';
			$output .= '<tr><th><a href="' . $link . '" target="_blank">' . Text::_($title) . '</th></tr>';

			$items    = $rssDoc;
			$numItems = 3;
			if ($numItems == 0)
			{
				$output .= '<tr><th>' . Text::_('JEV_No_news') . '</th></tr>';
			}
			else
			{
				$k = 0;
				for ($j = 0; $j < $numItems; $j++)
				{
					if (!isset($items[$j]))
					{
						break;
					}
					$item   = @$items[$j];
					$output .= '<tr><td class="row' . $k . '">';
					$output .= '<a href="' . $item->uri . '" target="_blank">' . $item->title . '</a>';
					if ($item->content)
					{
						$description = $this->limitText($item->content, 50);
						$output      .= '<br />' . $description;
					}
					$output .= '</td></tr>';
					$k      = 1 - $k;
				}
			}

			$output .= '</table>';
		}
		// do not return the output because of differences between J15 and J17
		echo $output;

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

	function renderJEventsNewsCached25()
	{

		$output = '';

		//  get RSS parsed object
		$options               = array();
		$options['rssUrl']     = 'https://www.jevents.net/jevnews?format=feed&type=rss';
		$options['cache_time'] = 0;

		error_reporting(0);
		ini_set('display_errors', 0);

		$rssDoc = Factory::getFeedParser($options['rssUrl'], $options['cache_time']);

		//$rssDoc = Factory::getXMLparser('RSS', $options);

		if ($rssDoc == false)
		{
			$output = Text::_('Error: Feed not retrieved');
		}
		else
		{
// channel header and link
			$title = str_replace(" ", "_", $rssDoc->get_title());
			$link  = $rssDoc->get_link();

			$output = '<table class="adminlist   table table-striped">';
			$output .= '<tr><th><a href="' . $link . '" target="_blank">' . Text::_($title) . '</th></tr>';

			$items    = array_slice($rssDoc->get_items(), 0, 3);
			$numItems = count($items);
			if ($numItems == 0)
			{
				$output .= '<tr><th>' . Text::_('JEV_No_news') . '</th></tr>';
			}
			else
			{
				$k = 0;
				for ($j = 0; $j < $numItems; $j++)
				{
					$item   = $items[$j];
					$output .= '<tr><td class="row' . $k . '">';
					$output .= '<a href="' . $item->get_link() . '" target="_blank">' . $item->get_title() . '</a>';
					if ($item->get_description())
					{
						$description = $this->limitText($item->get_description(), 50);
						$output      .= '<br />' . $description;
					}
					$output .= '</td></tr>';
					$k      = 1 - $k;
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
			$options    = array();
			$rssUrl     = 'https://www.jevents.net/versions30.xml';
			$cache_time = 86400;

			error_reporting(0);
			ini_set('display_errors', 0);

			jimport('simplepie.simplepie');

			// this caching doesn't work!!!
			//$cache = Factory::getCache('feed_parser', 'callback');
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

				$rows  = array();
				$items = $rssDoc->get_items();

				foreach ($items as $item)
				{
					$apps = array();
					if (strpos($item->get_title(), "layout_") === 0)
					{
						$layout = str_replace("layout_", "", $item->get_title());
						if (Folder::exists(JEV_PATH . "views/$layout"))
						{
// club layouts
							$xmlfiles1 = Folder::files(JEV_PATH . "views/$layout", "manifest\.xml", true, true);
							if ($xmlfiles1 && count($xmlfiles1) > 0)
							{
								foreach ($xmlfiles1 as $manifest)
								{
									if (realpath($manifest) != $manifest)
										continue;
									if (!$manifestdata = $this->getValidManifestFile($manifest))
										continue;

									$app                                            = new stdClass();
									$app->name                                      = $manifestdata["name"];
									$app->version                                   = $manifestdata["version"];
									$apps["layout_" . basename(dirname($manifest))] = $app;
								}
							}
						}
						// package version
						if (Folder::exists(JPATH_ADMINISTRATOR . "/manifests/files"))
						{
// club layouts
							$xmlfiles1 = Folder::files(JPATH_ADMINISTRATOR . "/manifests/files", "$layout\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (realpath($manifest) != $manifest)
									continue;
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app                                            = new stdClass();
								$app->name                                      = $manifestdata["name"];
								$app->version                                   = $manifestdata["version"];
								$apps["layout_" . basename(dirname($manifest))] = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "module_") === 0)
					{
						$module = str_replace("module_", "", $item->get_title());
// modules
						if (Folder::exists(JPATH_SITE . "/modules/$module"))
						{

							$xmlfiles1 = Folder::files(JPATH_SITE . "/modules/$module", "\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (realpath($manifest) != $manifest)
									continue;
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app          = new stdClass();
								$app->name    = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name         = "module_" . str_replace(".xml", "", basename($manifest));
								$apps[$name]  = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "plugin_") === 0)
					{
						$plugin = explode("_", str_replace("plugin_", "", $item->get_title()), 2);
						if (count($plugin) < 2)
							continue;
// plugins
						if ((Folder::exists(JPATH_SITE . "/plugins/" . $plugin[0] . "/" . $plugin[1])))
						{
// plugins
							$xmlfiles1 = Folder::files(JPATH_SITE . "/plugins/" . $plugin[0] . "/" . $plugin[1], "\.xml", true, true);

							foreach ($xmlfiles1 as $manifest)
							{
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app          = new stdClass();
								$app->name    = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name         = str_replace(".xml", "", basename($manifest));
								$name         = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;
								$apps[$name]  = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "component_") === 0)
					{
						$component = str_replace("component_", "", $item->get_title());

						if (Folder::exists(JPATH_ADMINISTRATOR . "/components/" . $component))
						{

// modules
							$xmlfiles1 = Folder::files(JPATH_ADMINISTRATOR . "/components/" . $component, "\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app          = new stdClass();
								$app->name    = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name         = "component_" . basename(dirname($manifest));
								$apps[$name]  = $app;
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
					$output .= '<th>' . Text::_("JEV_APPNAME") . '</th>';
					$output .= '<th>' . Text::_("JEV_APPCODE") . '</th>';
					$output .= '<th>' . Text::_("JEV_CURRENTVERSION") . '</th>';
					$output .= '<th>' . Text::_("JEV_LATESTVERSION") . '</th>';
					$output .= '<th>' . Text::_("JEV_CRITICALVERSION") . '</th>';
					$output .= '</tr>';
					$k      = 0;
					foreach ($rows as $row)
					{
						$output .= '<tr class="row' . $k . '"><td>';
						$output .= implode("</td><td>", $row);
						$output .= '</td></tr>';
						$k      = ($k + 1) % 2;
					}

					$output      .= '</table>';
					$needsupdate = true;

					return $output;
				}
			}
		}

		return false;

	}

	private
	function generateVersionsFile($rssDoc)
	{

		$input    = Factory::getApplication()->input;


		if ($input->getInt("versions", 0) == 0)
		{
			return;
		}
		jimport("joomla.filesystem.folder");

		$apps = array();

		// Club layouts
		$xmlfiles1 = Folder::files(JEV_PATH . "views", "manifest\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                                            = new stdClass();
			$app->name                                      = $manifestdata["name"];
			$app->version                                   = $manifestdata["version"];
			$apps["layout_" . basename(dirname($manifest))] = $app;
		}

		$xmlfiles1 = Folder::files(JPATH_MANIFESTS . "/files", "\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                                                            = new stdClass();
			$app->name                                                      = $manifestdata["name"];
			$app->version                                                   = $manifestdata["version"];
			$apps["layout_" . str_replace(".xml", "", basename($manifest))] = $app;
		}

// plugins
		if (Folder::exists(JPATH_SITE . "/plugins"))
		{
			$xmlfiles2 = Folder::files(JPATH_SITE . "/plugins", "\.xml", true, true);
		}
		else
		{
			$xmlfiles2 = array();
		}

		foreach ($xmlfiles2 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app          = new stdClass();
			$app->name    = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name         = str_replace(".xml", "", basename($manifest));

			$name = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;

			$apps[$name] = $app;
		}

// components (including JEvents
		$xmlfiles3 = Folder::files(JPATH_ADMINISTRATOR . "/components", "manifest\.xml", true, true);
		foreach ($xmlfiles3 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app          = new stdClass();
			$app->name    = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name         = "component_" . basename(dirname($manifest));
			$apps[$name]  = $app;
		}


// modules
		if (Folder::exists(JPATH_SITE . "/modules"))
		{
			$xmlfiles4 = Folder::files(JPATH_SITE . "/modules", "\.xml", true, true);
		}
		else
		{
			$xmlfiles4 = array();
		}
		foreach ($xmlfiles4 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                  = new stdClass();
			$app->name            = $manifestdata["name"];
			$app->version         = $manifestdata["version"];
			$app->criticalversion = "";
			$name                 = "module_" . str_replace(".xml", "", basename($manifest));
			$apps[$name]          = $app;
		}

// setup the XML file for server
		/*
		  $output = '$catmapping = array(' . "\n";
		  foreach ($apps as $appname => $app)
		  {
		  $output .='"' . $appname . '"=> 0,' . "\n";
		  }
		  $output = StringHelper::substr($output, 0, StringHelper::strlen($output) - 2) . ");\n\n";
		 */
		$criticaldata = file_get_contents('http://ubu.jev20j16.com/importantversions.txt');
		$criticaldata = explode("\n", $criticaldata);
		$criticals    = array();
		foreach ($criticaldata as $critical)
		{
			$critical = explode("|", $critical);
			if (count($critical) > 1)
			{
				$criticals[$critical[0]] = $critical[1];
			}
		}
		$catmapping = array(
			"layout_extplus"                   => 3,
			"layout_iconic"                    => 3,
			"layout_ruthin"                    => 3,
			"layout_flatplus"                  => 3,
			"layout_smartphone"                => 3,
			"layout_map"                       => 3,
			"layout_float"                     => 3,
			"plugin_acymailing_tagjevents"     => 41,
			"plugin_community_jevents"         => 7,
			"plugin_content_jevcreator"        => 34,
			"plugin_content_jevent_embed"      => 113,
			"plugin_jevents_agendaminutes"     => 12,
			"plugin_jevents_jevanonuser"       => 25,
			"plugin_jevents_jevcalendar"       => 15,
			"plugin_jevents_jevcatcal"         => 15,
			"plugin_jevents_jevcb"             => 18,
			"plugin_jevents_jevcck"            => 64,
			"plugin_jevents_jevcustomfields"   => 10,
			"plugin_jevents_jevfacebook"       => 46,
			"plugin_jevents_jevfeatured"       => 16,
			"plugin_jevents_jevfiles"          => 24,
			"plugin_jevents_jevhiddendetail"   => 51,
			"plugin_jevents_jevjsstream"       => 7,
			"plugin_jevents_jevjxcoments"      => 19,
			"plugin_jevents_jevlocations"      => 4,
			"plugin_jevents_jevmatchingevents" => 47,
			"plugin_jevents_jevmetatags"       => 58,
			"plugin_jevents_jevmissingevent"   => 56,
			"plugin_jevents_jevnotify"         => 61,
			"plugin_jevents_jevpaidsubs"       => 48,
			"plugin_jevents_jevpeople"         => 13,
			"plugin_jevents_jevpopupdetail"    => 50,
			"plugin_jevents_jevrsvp"           => 14,
			"plugin_jevents_jevrsvppro"        => 62,
			"plugin_jevents_jevsendfb"         => 45,
			"plugin_jevents_jevsessions"       => 21,
			"plugin_jevents_jevtags"           => 9,
			"plugin_jevents_jevtimelimit"      => 17,
			"plugin_jevents_jevusers"          => 8,
			"plugin_jevents_jevweekdays"       => 59,
			"plugin_jnews_jnewsjevents"        => 24,
			"plugin_rsvppro_manual"            => 62,
			"plugin_rsvppro_paypalipn"         => 62,
			"plugin_rsvppro_virtuemart"        => 62,
			//"plugin_search_eventsearch" => 52, // JEvents 2.2
			"plugin_search_eventsearch"        => 71,
			"plugin_search_jevlocsearch"       => 4,
			"plugin_search_jevtagsearch"       => 9,
			"plugin_system_autotweetjevents"   => 45,
			"plugin_user_juser"                => 24,
			"component_com_attend_jevents"     => 21,
			//"component_com_jevents" => 52, // JEvents 2.0
			//"component_com_jevents" => 65, // JEvents 2.1
			"component_com_jevents"            => 71, // JEvents 3.0
			"component_com_jeventstags"        => 9,
			"component_com_jevlocations-old"   => 4,
			"component_com_jevlocations"       => 4,
			"component_com_jevpeople"          => 13,
			"component_com_rsvppro"            => 62,
			"module_mod_jevents_cal"           => 71,
			"module_mod_jevents_categories"    => 76,
			//"module_mod_jevents_filter" => 52,
			//"module_mod_jevents_latest" => 52,
			//"module_mod_jevents_legend" => 52,
			"module_mod_jevents_filter"        => 71,
			"module_mod_jevents_latest"        => 71,
			"module_mod_jevents_legend"        => 71,
			"module_mod_jevents_notify"        => 61,
			"module_mod_jevents_paidsubs"      => 48,
			"module_mod_jevents_switchview"    => 52
		);
		$output     = "";
		foreach ($apps as $appname => $app)
		{
			$row                  = new stdClass();
			$row->version         = $app->version;
			$row->criticalversion = "";
			if (array_key_exists($appname, $criticals))
			{
				$row->criticalversion = $criticals[$appname];
			}
			$row->link = array_key_exists($appname, $catmapping) ? "https://www.jevents.net/downloads/category/" . $catmapping[$appname] : "";
			if ($row->link == "")
				continue;
			$output .= "<item>\n<title>$appname</title>\n<description><![CDATA[" . json_encode($row) . "]]></description>\n</item>\n";
		}
		$output      .= "";
		$needsupdate = true;
		ob_end_clean();
		echo $output;
		die();

	}

	private
	function getValidManifestFile($manifest)
	{

		$filecontent = file_get_contents($manifest);
		if (stripos($filecontent, "jevents.net") === false
			&& stripos($filecontent, "gwesystems.com") === false
			&& stripos($filecontent, "joomlacontenteditor") === false
			&& stripos($filecontent, "virtuemart") === false
			&& stripos($filecontent, "sh404sef") === false
			&& stripos($filecontent, "comprofiler") === false
			&& stripos($filecontent, "community") === false
			&& stripos($filecontent, "TechJoomla") === false
			&& stripos($filecontent, "hikashop") === false
			&& stripos($filecontent, "acymailing") === false)
		{
			return false;
		}
		// for JCE and Virtuemart only check component version number
		if (stripos($filecontent, "joomlacontenteditor") !== false
			|| stripos($filecontent, "virtuemart") !== false
			|| stripos($filecontent, "sh404sef") !== false
			|| strpos($filecontent, "JCE") !== false
			|| strpos($filecontent, "Community") !== false
			|| strpos($filecontent, "Comprofiler") !== false
			|| strpos($filecontent, "TechJoomla") !== false
			|| strpos($filecontent, "hikashop") !== false)
		{
			if (strpos($filecontent, "type='component'") === false && strpos($filecontent, 'type="component"') === false)
			{
				return false;
			}
		}

		$manifestdata = Installer::parseXMLInstallFile($manifest);
		if (!$manifestdata)
			return false;
		if (strpos($manifestdata["authorUrl"], "jevents") === false
			&& strpos($manifestdata["authorUrl"], "gwesystems") === false
			&& strpos($manifestdata["authorUrl"], "joomlacontenteditor") === false
			&& strpos($manifestdata["authorUrl"], "virtuemart") === false
			&& strpos($manifestdata['name'], "sh404SEF") === false
			&& strpos($manifestdata['name'], "Community") === false
			&& strpos($manifestdata['name'], "comprofiler") === false
			&& strpos($manifestdata['author'], "TechJoomla") === false
			&& strpos($manifestdata['name'], "HikaShop") === false
			&& strpos($manifestdata['name'], "AcyMailing") === false
		)
		{
			return false;
		}

		return $manifestdata;

	}

	// remove stray entries!

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
		$app              = new stdClass();
		$app->name        = "Joomla";
		$version          = new Version();
		$app->version     = $version->getShortVersion();
		$apps[$app->name] = $app;

		// TODO :  Can we do this from the database???
		// components (including JEvents)
		$xmlfiles3 = array_merge(
			Folder::files(JPATH_ADMINISTRATOR . "/components", "manifest\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "sh404sef\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "virtuemart\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "jce\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "comprofiler\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "community\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "jmailalerts\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "hikashop\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "hikashop_j3\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "jev_latestevents\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "acymailing\.xml", true, true));
		foreach ($xmlfiles3 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app          = new stdClass();
			$app->name    = $manifestdata["name"];
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
			$name        = "component_" . basename(dirname($manifest));
			$apps[$name] = $app;
		}

// modules
		if (Folder::exists(JPATH_SITE . "/modules"))
		{
			$xmlfiles4 = Folder::files(JPATH_SITE . "/modules", "\.xml", true, true);
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

			$app                  = new stdClass();
			$app->name            = $manifestdata["name"];
			$app->version         = $manifestdata["version"];
			$app->criticalversion = "";
			$name                 = "module_" . str_replace(".xml", "", basename($manifest));
			$apps[$name]          = $app;
		}

// club layouts
		$xmlfiles1 = Folder::files(JEV_PATH . "views", "manifest\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (realpath($manifest) != $manifest)
				continue;
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                                            = new stdClass();
			$app->name                                      = $manifestdata["name"];
			$app->version                                   = $manifestdata["version"];
			$apps["layout_" . basename(dirname($manifest))] = $app;
		}

		$xmlfiles1 = Folder::files(JPATH_ADMINISTRATOR . "/manifests/files", "\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (realpath($manifest) != $manifest)
				continue;
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                                                            = new stdClass();
			$app->name                                                      = $manifestdata["name"];
			$app->version                                                   = $manifestdata["version"];
			$apps[str_replace(".xml", "", "layout_" . basename($manifest))] = $app;
		}

// plugins
		if (Folder::exists(JPATH_SITE . "/plugins"))
		{
			$xmlfiles2 = Folder::files(JPATH_SITE . "/plugins", "\.xml", true, true);
		}
		else
		{
			$xmlfiles2 = array();
		}

		foreach ($xmlfiles2 as $manifest)
		{
			if (strpos($manifest, "Zend") > 0 || strpos($manifest, "invalid") === 0 || strpos($manifest, "invalid") > 0)
				continue;

			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app          = new stdClass();
			$app->name    = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name         = str_replace(".xml", "", basename($manifest));
			$group        = basename(dirname(dirname($manifest)));
			$plugin       = PluginHelper::getPlugin($group, $name);
			if (!$plugin)
			{
				$app->version .= " (not enabled)";
			}

			$name        = "plugin_" . $group . "_" . $name;
			$apps[$name] = $app;
		}

		$output = "<textarea rows='40' cols='80' class='versionsinfo'>[code]\n";
		$output .= "PHP Version : " . phpversion() . "\n";
		$output .= "MySQL Version : " . Factory::getDbo()->getVersion() . "\n";
		$output .= "Server Information : " . php_uname() . "\n";

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("fixjquery", -1) == -1)
		{
			$output .= "*** CONFIG NOT SAVED*** \n";
		}
		$output .= "Fix jQuery? : " . ($params->get("fixjquery", 1) ? "Yes" : "No") . "\n";
		$output .= "Load JEvents Bootstrap CSS? : " . ($params->get("bootstrapcss", 1) ? "Yes" : "No") . "\n";
		//$output .= "Load JEvents Bootstrap JS? : " . ($params->get("bootstrapjs", 1)?"Yes":"No"). "\n";
		if (ini_get("max_input_vars") > 0 && ini_get("max_input_vars") <= 10000)
		{
			$output .= "Max Input Vars ? : " . ini_get("max_input_vars") . "\n";
		}
		$output    .= "Club code set? : " . ($params->get("clubcode", false) ? "Yes" : "No") . "  \n";
		$server    = new \Joomla\Input\Input($_SERVER);
		$useragent = $server->get('HTTP_USER_AGENT', false, "string");
		$output    .= $useragent ? "User Agent : " . $useragent . "  \n" : "";
		foreach ($apps as $appname => $app)
		{
			$output .= "$appname : $app->version\n";
		}
		$output .= "[/code]</textarea>";

		return $output;

	}

	function getTranslatorLink()
	{

		$translatorUrl = Text::_("JEV_TRANSLATION_AUTHOR");
		//$translatorUrl = Text::_("JEV_TRANSLATION_AUTHOR_URL");
		//$translatorUrl = "<a href=\"$translatorUrl\">$translatorName</a>";

		return $translatorUrl;

	}

	function support()
	{

		jimport('joomla.html.pane');

		$document = Factory::getDocument();
		$document->setTitle(Text::_('JEVENTS') . ' :: ' . Text::_('JEVENTS'));

		JToolbarHelper::title(Text::_('JEVENTS') . ' :: ' . Text::_('JEVENTS'), 'jevents');



		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		if (ini_get("max_input_vars") > 0 && ini_get("max_input_vars") <= 1000)
		{

			Factory::getApplication()->enqueueMessage('234 - ' . Text::sprintf("MAX_INPUT_VARS_LOW_WARNING", ini_get("max_input_vars")), 'warning');

		}


	}

}

