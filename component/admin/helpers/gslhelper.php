<?php
/**
 * @version    CVS: JEVENTS_VERSION
 * @package    com_jevents
 * @author     Geraint Edwards <yoursites@gwesystems.com>
 * @copyright  2016-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

JLoader::register('JEventsHelper', JPATH_ADMINISTRATOR . "/components/com_jevents/helpers/jevents.php");

class GslHelper
{
	public static function loadAssets()
	{
		$document = Factory::getDocument();

		HTMLHelper::stylesheet('media/com_jevents/css/uikit.gsl.css', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false));
		HTMLHelper::stylesheet('components/com_jevents/assets/css/jevents.css', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false));
		$jversion = new Version;
		if ($jversion->isCompatible('4.0'))
		{
			HTMLHelper::stylesheet('components/com_jevents/assets/css/j4.css', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false));
		}
		else
		{
			HTMLHelper::stylesheet('components/com_jevents/assets/css/j3.css', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false));
		}

		//HTMLHelper::script('media/com_jevents/js/requireWorkaround1.js', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false), array('defer' => true));
		HTMLHelper::script('media/com_jevents/js/uikit.js', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false), array('defer' => true));
		HTMLHelper::script('media/com_jevents/js/uikit-icons.js', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false), array('defer' => true));
		//HTMLHelper::script('media/com_jevents/js/requireWorkaround2.js', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false), array('defer' => true));
		HTMLHelper::script('components/com_jevents/assets/js/gslframework.js', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false), array('defer' => true));
		HTMLHelper::script('components/com_jevents/assets/js/jevents.js', array('version' => JEventsHelper::JEvents_Version(false), 'relative' => false), array('defer' => true));
	}

	public static function renderModal()
	{
		return;

		// Progress Modal
		$whendonemessage   = Text::_("COM_YOURSITES_CLOSE_PROGRESS_POPUP", true);
		$progresstitle     = Text::_("COM_YOURSITES_PROGRESS_POPUP_TITLE", true);
		$progressModalData = array(
			'selector' => 'progressModal',
			'params'   => array(
				'title'    => $progresstitle,
				'footer'   => "<strong>$whendonemessage </strong>",
				'backdrop' => 'static'
			),
			'body'     => '<div class="gsl-grid gsl-padding-remove" style="padding:10px;" ><div id="pmcol1" class="gsl-width-1-2"></div><div  id="pmcol2" class="gsl-width-1-2"></div></div>',
		);

		echo LayoutHelper::render('jevents.modal.main', $progressModalData);
	}

	static public function renderVersion()
	{
		echo LayoutHelper::render('jevents.version');
	}

	static public function translate($string, $jssafe = true)
	{
		$string = "COM_JEVENTS_" . $string;

		return Text::_($string, $jssafe);
	}

	static public
	function isAdminUser($user = null)
	{

		if (is_null($user))
		{
			$user = Factory::getUser();
		}
		//$access = Access::check($user->id, "core.admin","com_jevents");
		// Add a second check incase the getuser failed.
		if (!$user)
		{
			return false;
		}
		$access = $user->authorise('core.admin', 'com_jevents');

		return $access;
	}

	static public function supportLink()
	{
		return "https://www.jevents.net/discussions";
	}

	static public function documentationLink()
	{
		return "https://www.jevents.net/documentation";
	}

	static public function configLink()
	{
		return Uri::base() . 'index.php?option=com_jevents&task=params.edit&component=com_jevents&view=component';
	}

	static public function cpanelIconLink()
	{
		$option = Factory::getApplication()->input->getCmd('option', 'com_jevents');
		$componentParams = ComponentHelper::getParams($option);
		$leftmenutrigger = $componentParams->get("leftmenutrigger", 0);
		$onclick = $leftmenutrigger == 2 ? "" : 'onclick="if((window.getComputedStyle(this.querySelector(\'.nav-label\')).getPropertyValue(\'display\')==\'none\' && window.innerWidth <= 960) || window.getComputedStyle(this.querySelector(\'.nav-label\')).getPropertyValue(\'display\')!==\'none\') {document.location=this.href;}return false;"';
		?>
        <a href="<?php echo Route::_("index.php?option=com_jevents&view=cpanel"); ?>" class="" <?php echo $onclick;?> >
            <img src="<?php echo Uri::base(); ?>components/com_jevents/assets/images/logo.png"
                 alt="JEvents Logo">
            <span class="nav-label"><?php echo Text::_('JEVENTS_DASHBOARD'); ?></span>
        </a>
		<?php
	}

	static public function getLeftIconLinks()
	{
		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");

		$app   = Factory::getApplication();
		$input = $app->input;
		$task  = $input->getCmd('jevtask', 'icalevent.list');
		$option  = $input->getCmd('option', 'com_jevents');
		if ($option == "com_jevents")
		{
			list($view, $layout) = array_pad(explode(".", $task), 2, "");
		}
		else
        {
	        $view =  $layout = "";
        }


		$params = ComponentHelper::getParams("com_jevents");
		$clubcode = $params->get("clubcode", "");
		$hideunusedmenuitems = (int) $params->get("hideunusedmenuitems", 0);

		$leftmenutrigger = (int) $params->get("leftmenutrigger", 0);

		$iconLinks = array();

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $view == "icalevent";
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=icalevent.list");
		$iconLink->icon           = "calendar";
		$iconLink->label          = Text::_('JEV_ADMIN_ICAL_EVENTS');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_("JEV_INSTAL_MANAGE", true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $option == "com_categories";
		$iconLink->link           = Route::_("index.php?option=com_categories&view=categories&extension=com_jevents");
		$iconLink->icon           = "album";
		$iconLink->label          = Text::_('JEV_INSTAL_CATS');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_("JEV_INSTAL_CATS", true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		if (JEVHelper::isAdminUser())
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "";
			$iconLink->active         = $view == "icals";
			$iconLink->link           = Route::_("index.php?option=com_jevents&task=icals.list");
			$iconLink->icon           = "calendars";
			$iconLink->label          = Text::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS');
			$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS', true);
			$iconLink->tooltip_detail = "";
			$iconLinks[]              = $iconLink;


			if ($params->get("authorisedonly", 0))
			{
				$iconLink                 = new stdClass();
				$iconLink->class          = "";
				$iconLink->active         = $view == "user";
				$iconLink->link           = Route::_("index.php?option=com_jevents&task=user.list");
				$iconLink->icon           = "users";
				$iconLink->label          = Text::_('JEV_MANAGE_USERS');
				$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('JEV_MANAGE_USERS', true);
				$iconLink->tooltip_detail = "";
				$iconLinks[]              = $iconLink;
			}
		}

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = 0;
		$iconLink->link           = Route::_("index.php?option=com_modules&filter[module]=mod_jevents_latest");
		$iconLink->icon           = "grid";
		$iconLink->label          = Text::_('COM_JEVENTS_MODULES');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('COM_JEVENTS_MODULES', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		try
		{
			$iconLink->sublinks = array();

			Factory::getLanguage()->load("mod_jevents_latest", JPATH_SITE);

			$sublink                 = new stdClass();
			$sublink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
			$sublink->onclick        = "(function(e) { window.open('" . Route::_("index.php?option=com_modules&filter[module]=mod_jevents_latest") . " ');return false;})(event);";
			$sublink->link           = "";
			$sublink->icon           = "list";
			$sublink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
			$sublink->label          = Text::_('MOD_JEV_LATEST_EVENTS_TITLE');
			$sublink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('MOD_JEV_LATEST_EVENTS_TITLE', true);
			$sublink->tooltip_detail = "";

			$iconLink->sublinks[] = $sublink;

			Factory::getLanguage()->load("mod_jevents_cal", JPATH_SITE);

			$sublink                 = new stdClass();
			$sublink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
			$sublink->onclick        = "(function(e) { window.open('" . Route::_("index.php?option=com_modules&filter[module]=mod_jevents_cal") . " ');return false;})(event);";
			$sublink->link           = "";
			$sublink->icon           = "calendar";
			$sublink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
			$sublink->label          = Text::_('MOD_JEV_CALENDAR_TITLE');
			$sublink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('MOD_JEV_CALENDAR_TITLE', true);
			$sublink->tooltip_detail = "";

			$iconLink->sublinks[] = $sublink;

			Factory::getLanguage()->load("mod_jevents_custom", JPATH_SITE);

			$sublink                 = new stdClass();
			$sublink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
			$sublink->onclick        = "(function(e) { window.open('" . Route::_("index.php?option=com_modules&filter[module]=mod_jevents_custom") . " ');return false;})(event);";
			$sublink->link           = "";
			$sublink->icon           = "paint-bucket";
			$sublink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
			$sublink->label          = Text::_('MOD_JEV_CUSTOM_MODULE_TITLE');
			$sublink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('MOD_JEV_CUSTOM_MODULE_TITLE', true);
			$sublink->tooltip_detail = "";

			$iconLink->sublinks[] = $sublink;

			Factory::getLanguage()->load("mod_jevents_filter", JPATH_SITE);

			$sublink                 = new stdClass();
			$sublink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
			$sublink->onclick        = "(function(e) { window.open('" . Route::_("index.php?option=com_modules&filter[module]=mod_jevents_filter") . " ');return false;})(event);";
			$sublink->link           = "";
			$sublink->icon           = "search";
			$sublink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
			$sublink->label          = Text::_('MOD_JEV_FILTER_MODULE_TITLE');
			$sublink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('MOD_JEV_FILTER_MODULE_TITLE', true);
			$sublink->tooltip_detail = "";

			$iconLink->sublinks[] = $sublink;

			Factory::getLanguage()->load("mod_jevents_legend", JPATH_SITE);

			$sublink                 = new stdClass();
			$sublink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
			$sublink->onclick        = "(function(e) { window.open('" . Route::_("index.php?option=com_modules&filter[module]=mod_jevents_legend") . " ');return false;})(event);";
			$sublink->link           = "";
			$sublink->icon           = "settings";
			$sublink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
			$sublink->label          = Text::_('MOD_JEV_LEGEND_TITLE');
			$sublink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('MOD_JEV_LEGEND_TITLE', true);
			$sublink->tooltip_detail = "";

			$iconLink->sublinks[] = $sublink;

		}
		catch (Exception $e)
		{

		}

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $view == "defaults";
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=defaults.list");
		$iconLink->icon           = "file-edit";
		$iconLink->label          = Text::_('JEV_LAYOUT_DEFAULTS');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('JEV_LAYOUT_DEFAULTS', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $task == "cpanel.support";
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=cpanel.support");
		$iconLink->icon           = "file-text";
		$iconLink->label          = Text::_('SUPPORT_INFO');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('SUPPORT_INFO', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $view == "customcss";
		$iconLink->link           = Route::_("index.php?option=com_jevents&view=customcss");
		$iconLink->icon           = "paint-bucket";
		$iconLink->label          = Text::_('JEV_CUSTOM_CSS');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('JEV_CUSTOM_CSS', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		// Links to addons

		// Managed Locations
		$db = Factory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevlocations' AND type='component' ");
		$is_enabled = $db->loadResult();
		if ($is_enabled)
		{
			Factory::getLanguage()->load("com_jevlocations", JPATH_ADMINISTRATOR);

			$iconLink                 = new stdClass();
			$iconLink->class          = "";
			$iconLink->active         = $view == "jevlocations";
			$iconLink->link           = Route::_("index.php?option=com_jevlocations");
			$iconLink->icon           = "location";
			$iconLink->label          = Text::_('COM_JEVLOCATIONS');
			$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('COM_JEVLOCATIONS', true);
			$iconLink->tooltip_detail = "";
			if (file_exists(JPATH_ADMINISTRATOR . "/components/com_jevlocations/helpers/gslmenuhelper.php"))
			{
				include_once JPATH_ADMINISTRATOR . "/components/com_jevlocations/helpers/gslmenuhelper.php";
				$iconLink->sublinks = GslLocationsMenuHelper::getLeftIconSubLinks($leftmenutrigger);
			}
			$iconLinks[]              = $iconLink;

		}
		else if (!$hideunusedmenuitems)
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "notinstalled";
			$iconLink->active         = $view == "jevlocations";
			$iconLink->link           = "https://www.jevents.net/join-club-jevents";
			$iconLink->icon           = "location";
			$iconLink->label          = Text::_('COM_JEVENTS_LOCATIONS');
			$iconLink->tooltip        = Text::_("COM_JEVENTS_DISABLED_OPTION", true);
			$iconLink->tooltip_detail = Text::_("COM_JEVENTS_DISABLED_OPTION_DESC", true);
			$iconLink->target         = "_blank";
			$iconLinks[]              = $iconLink;
		}

		// JEvents Tags
		$db = Factory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jeventstags' AND type='component' ");
		$is_enabled = $db->loadResult();
		if ($is_enabled)
		{
			Factory::getLanguage()->load("com_jeventstags", JPATH_ADMINISTRATOR);

			$iconLink                 = new stdClass();
			$iconLink->class          = "";
			$iconLink->active         = $view == "jeventstags";
			$iconLink->link           = Route::_("index.php?option=com_jeventstags");
			$iconLink->icon           = "hashtag";
			$iconLink->label          = Text::_('COM_JEVENTSTAGS');
			$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('COM_JEVENTSTAGS', true);
			$iconLink->tooltip_detail = "";
			if (file_exists(JPATH_ADMINISTRATOR . "/components/com_jeventstags/helpers/gslmenuhelper.php"))
			{
				include_once JPATH_ADMINISTRATOR . "/components/com_jeventstags/helpers/gslmenuhelper.php";
				$iconLink->sublinks = GslTagsMenuHelper::getLeftIconSubLinks($leftmenutrigger);
			}
			$iconLinks[]              = $iconLink;

		}
		else if (!$hideunusedmenuitems)
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "notinstalled";
			$iconLink->active         = $view == "jeventstags";
			$iconLink->link           = "https://www.jevents.net/join-club-jevents";
			$iconLink->icon           = "hashtag";
			$iconLink->label          = Text::_('COM_JEVENTS_TAGS');
			$iconLink->tooltip        = Text::_("COM_JEVENTS_DISABLED_OPTION", true);
			$iconLink->tooltip_detail = Text::_("COM_JEVENTS_DISABLED_OPTION_DESC", true);
			$iconLink->target         = "_blank";
			$iconLinks[]              = $iconLink;
		}

		// Managed People
		$db = Factory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevpeople' AND type='component' ");
		$is_enabled = $db->loadResult();
		if ($is_enabled)
		{
			Factory::getLanguage()->load("com_jevpeople", JPATH_ADMINISTRATOR);

			$iconLink                 = new stdClass();
			$iconLink->class          = "";
			$iconLink->active         = $view == "jevpeople";
			$iconLink->link           = Route::_("index.php?option=com_jevpeople");
			$iconLink->icon           = "user";
			$iconLink->label          = Text::_('COM_JEVPEOPLE');
			$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('COM_JEVPEOPLE', true);
			$iconLink->tooltip_detail = "";

			if (file_exists(JPATH_ADMINISTRATOR . "/components/com_jevpeople/helpers/gslmenuhelper.php"))
			{
				include_once JPATH_ADMINISTRATOR . "/components/com_jevpeople/helpers/gslmenuhelper.php";
				$iconLink->sublinks = GslPeopleMenuHelper::getLeftIconSubLinks($leftmenutrigger);
			}
			$iconLinks[]              = $iconLink;

		}
		else if (!$hideunusedmenuitems)
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "notinstalled";
			$iconLink->active         = $view == "jevpeople";
			$iconLink->link           = "https://www.jevents.net/join-club-jevents";
			$iconLink->icon           = "user";
			$iconLink->label          = Text::_('COM_JEVENTS_PEOPLE');
			$iconLink->tooltip        = Text::_("COM_JEVENTS_DISABLED_OPTION", true);
			$iconLink->tooltip_detail = Text::_("COM_JEVENTS_DISABLED_OPTION_DESC", true);
			$iconLink->target         = "_blank";
			$iconLinks[]              = $iconLink;
		}

		// RSVP Pro
		$db = Factory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_rsvppro' AND type='component' ");
		$is_enabled = $db->loadResult();
		if ($is_enabled)
		{
			Factory::getLanguage()->load("com_rsvppro", JPATH_ADMINISTRATOR);

			$iconLink                 = new stdClass();
			$iconLink->class          = "";
			$iconLink->active         = $view == "rsvppro";
			$iconLink->link           = Route::_("index.php?option=com_rsvppro");
			$iconLink->icon           = "cart";
			$iconLink->label          = Text::_('COM_RSVPPRO');
			$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('COM_RSVPPRO', true);
			$iconLink->tooltip_detail = "";
			if (file_exists(JPATH_ADMINISTRATOR . "/components/com_rsvppro/helpers/gslmenuhelper.php"))
			{
				include_once JPATH_ADMINISTRATOR . "/components/com_rsvppro/helpers/gslmenuhelper.php";
				$iconLink->sublinks = GslRsvpproMenuHelper::getLeftIconSubLinks($leftmenutrigger);
			}
			$iconLinks[]              = $iconLink;

		}
		else if (!$hideunusedmenuitems)
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "notinstalled";
			$iconLink->active         = $view == "rsvppro";
			$iconLink->link           = "https://www.jevents.net/join-club-jevents";
			$iconLink->icon           = "cart";
			$iconLink->label          = Text::_('COM_JEVENTS_RSVPPRO');
			$iconLink->tooltip        = Text::_("COM_JEVENTS_DISABLED_OPTION", true);
			$iconLink->tooltip_detail = Text::_("COM_JEVENTS_DISABLED_OPTION_DESC", true);
			$iconLink->target         = "_blank";
			$iconLinks[]              = $iconLink;
		}

		// Custom Fields
		$db = Factory::getDbo();
		$db->setQuery("SELECT * FROM #__extensions WHERE element = 'jevcustomfields' AND type='plugin' AND folder='jevents' ");
		$extension = $db->loadObject();
		// Stop if user is not authorised to manage JEvents
		if ($extension && $extension->enabled )
		{
			if (JEVHelper::isAdminUser())
			{
				Factory::getLanguage()->load("plg_jevents_jevcustomfields", JPATH_ADMINISTRATOR);

				$iconLink                 = new stdClass();
				$iconLink->class          = "";
				$iconLink->active         = strpos($task, "plugin.jev_customfields") === 0;
				$iconLink->link           = Route::_("index.php?option=com_jevents&task=plugin.jev_customfields.overview");
				$iconLink->icon           = "code";
				$iconLink->label          = Text::_('JEV_CUSTOM_FIELDS');
				$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('JEV_CUSTOM_FIELDS', true);
				$iconLink->tooltip_detail = "";
				$iconLinks[]              = $iconLink;

				try
				{
					$manifestCache = json_decode($extension->manifest_cache);
					if (version_compare($manifestCache->version, "3.7.0", "ge") || $manifestCache->version == "3.5.0RC2")
					{
						$iconLink->sublinks = array();

						$sublink              = new stdClass();
						$sublink->onclick     = "(function(e) { document.location='" . Route::_("index.php?option=com_fields&context=com_jevents.event") . " ';return false;})(event);";
						$sublink->link        = "";
						$sublink->class       = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
						$sublink->icon        = 'joomla';
						$sublink->iconclass   = "gsl-margin-small-right gsl-display-inline-block";
						$sublink->label       = JText::_('COM_JEVENTS_JOOMLA_CUSTOM_FIELDS');
						$iconLink->sublinks[] = $sublink;
					}
				}
				catch (Exception $e)
				{

				}
			}

		}
		else if (!$hideunusedmenuitems)
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "notinstalled";
			$iconLink->active         = strpos($task, "plugin.jev_customfields") === 0;
			$iconLink->link           = "https://www.jevents.net/join-club-jevents";
			$iconLink->icon           = "code";
			$iconLink->label          = Text::_('JEV_CUSTOM_FIELDS');
			$iconLink->tooltip        = Text::_("COM_JEVENTS_DISABLED_OPTION", true);
			$iconLink->tooltip_detail = Text::_("COM_JEVENTS_DISABLED_OPTION_DESC", true);
			$iconLink->target         = "_blank";
			$iconLinks[]              = $iconLink;
		}

		// YourSites
		$db = Factory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_yoursites' AND type='component' ");
		$is_enabled = $db->loadResult();
		// Availability and access check .
		if ($is_enabled && JFactory::getUser()->authorise('core.manage', 'com_yoursites'))
		{
			Factory::getLanguage()->load("com_yoursites", JPATH_ADMINISTRATOR);

			$iconLink                 = new stdClass();
			$iconLink->class          = "";
			$iconLink->active         = $view == "yoursites";
			$iconLink->link           = Route::_("index.php?option=com_yoursites");
			$iconLink->icon           = "";
			$iconLink->iconSrc        = "components/com_yoursites/assets/images/YourSitesIcon.png";
			$iconLink->label          = strip_tags(Text::_('COM_YOURSITES'));
			$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" :str_replace("'", '"',  Text::_('COM_YOURSITES', false));
			$iconLink->tooltip_detail = "";
			$iconLinks[]              = $iconLink;
		}

		$iconLink                 = new stdClass();
		$iconLink->class          = "returntojoomla";
		$iconLink->active         = false;
		$iconLink->link           = Route::_("index.php");
		$iconLink->icon           = "joomla";
		$iconLink->label          = Text::_('COM_JEVENTS_RETURN_TO_JOOMLA');
		$iconLink->tooltip        = Text::_("COM_JEVENTS_RETURN_TO_JOOMLA_TOOLTIP");
		$iconLink->tooltip_detail = "";
		$iconLink->events         = [];
		$iconLinks[]              = $iconLink;


		return $iconLinks;

	}

	static public function returnToMainComponent()
	{
		return;
	}

}
