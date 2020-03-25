<?php
/**
 * @version    CVS: 3.5.0dev
 * @package    com_yoursites
 * @author     Geraint Edwards <yoursites@gwesystems.com>
 * @copyright  2016-2019 GWE Systems Ltd
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

class GslHelper
{
	public static function loadAssets()
	{
		$document = Factory::getDocument();
		// set container scope for code
		$document->addScriptDeclaration("gslUIkit.container = '.gsl-scope';");

		HTMLHelper::stylesheet('media/com_jevents/css/uikit.gsl.css', array('version' => '3.5.0dev', 'relative' => false));
		HTMLHelper::stylesheet('administrator/components/com_jevents/assets/css/jevents.css', array('version' => '3.5.0dev', 'relative' => false));
		$jversion = new Version;
		if ($jversion->isCompatible('4.0'))
		{
			HTMLHelper::stylesheet('administrator/components/com_jevents/assets/css/j4.css', array('version' => '3.5.0dev', 'relative' => false));
		}
		else
		{
			HTMLHelper::stylesheet('administrator/components/com_jevents/assets/css/j3.css', array('version' => '3.5.0dev1', 'relative' => false));
		}

		HTMLHelper::script('media/com_jevents/js/uikit.js', array('version' => '3.5.0dev', 'relative' => false));
		HTMLHelper::script('media/com_jevents/js/uikit-icons.js', array('version' => '3.5.0dev', 'relative' => false));
		HTMLHelper::script('administrator/components/com_jevents/assets/js/gslframework.js', array('version' => '3.5.0dev', 'relative' => false));
		HTMLHelper::script('administrator/components/com_jevents/assets/js/jevents.js', array('version' => '3.5.0dev', 'relative' => false));
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
		return Uri::base() . 'index.php?option=com_jevents&task=params.edit';
	}

	static public function cpanelIconLink()
	{
		?>
        <a href="<?php echo Route::_("index.php?option=com_jevents&view=cpanel"); ?>" class="">
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

		$iconLinks = array();

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $view == "icalevent";
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=icalevent.list");
		$iconLink->icon           = "calendar";
		$iconLink->label          = Text::_('JEV_ADMIN_ICAL_EVENTS');
		$iconLink->tooltip        = Text::_("JEV_INSTAL_MANAGE", true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $option == "com_categories";
		$iconLink->link           = Route::_("index.php?option=com_categories&view=categories&extension=com_jevents");
		$iconLink->icon           = "album";
		$iconLink->label          = Text::_('JEV_INSTAL_CATS');
		$iconLink->tooltip        = Text::_("JEV_INSTAL_CATS", true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		if (JEVHelper::isAdminUser())
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "";
			$iconLink->active         = $view == "icals";
			$iconLink->link           = Route::_("index.php?option=com_jevents&task=icals.list");
			$iconLink->icon           = "thumbnails";
			$iconLink->label          = Text::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS');
			$iconLink->tooltip        = Text::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS', true);
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
				$iconLink->tooltip        = Text::_('JEV_MANAGE_USERS', true);
				$iconLink->tooltip_detail = "";
				$iconLinks[]              = $iconLink;
			}
		}

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $view == "defaults";
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=defaults.list");
		$iconLink->icon           = "file-edit";
		$iconLink->label          = Text::_('JEV_LAYOUT_DEFAULTS');
		$iconLink->tooltip        = Text::_('JEV_LAYOUT_DEFAULTS', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $task == "cpanel.support";
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=cpanel.support");
		$iconLink->icon           = "file-text";
		$iconLink->label          = Text::_('SUPPORT_INFO');
		$iconLink->tooltip        = Text::_('SUPPORT_INFO', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new stdClass();
		$iconLink->class          = "";
		$iconLink->active         = $view == "customcss";
		$iconLink->link           = Route::_("index.php?option=com_jevents&view=customcss");
		$iconLink->icon           = "paint-bucket";
		$iconLink->label          = Text::_('JEV_CUSTOM_CSS');
		$iconLink->tooltip        = Text::_('JEV_CUSTOM_CSS', true);
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
			$iconLink->tooltip        = Text::_('COM_JEVLOCATIONS', true);
			$iconLink->tooltip_detail = "";
			$iconLinks[]              = $iconLink;
		}
		else
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
			$iconLink->tooltip        = Text::_('COM_JEVENTSTAGS', true);
			$iconLink->tooltip_detail = "";
			$iconLinks[]              = $iconLink;
		}
		else
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "notinstalled";
			$iconLink->active         = $view == "jeventstags";
			$iconLink->link           = "https://www.jevents.net/join-club-jevents";
			$iconLink->icon           = "location";
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
			$iconLink->tooltip        = Text::_('COM_JEVPEOPLE', true);
			$iconLink->tooltip_detail = "";
			$iconLinks[]              = $iconLink;
		}
		else
		{
			$iconLink                 = new stdClass();
			$iconLink->class          = "notinstalled";
			$iconLink->active         = $view == "jevpeople";
			$iconLink->link           = "https://www.jevents.net/join-club-jevents";
			$iconLink->icon           = "location";
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
			$iconLink->tooltip        = Text::_('COM_RSVPPRO', true);
			$iconLink->tooltip_detail = "";
			$iconLinks[]              = $iconLink;
		}
		else
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
		if ($extension && $extension->enabled && JEVHelper::isAdminUser())
		{

			Factory::getLanguage()->load("plg_jevents_jevcustomfields", JPATH_ADMINISTRATOR);

			$iconLink                 = new stdClass();
			$iconLink->class          = "";
			$iconLink->active         = strpos($task, "plugin.jev_customfields") === 0;
			$iconLink->link           = Route::_("index.php?option=com_jevents&task=plugin.jev_customfields.overview");
			$iconLink->icon           = "code";
			$iconLink->label          = Text::_('JEV_CUSTOM_FIELDS');
			$iconLink->tooltip        = Text::_('JEV_CUSTOM_FIELDS', true);
			$iconLink->tooltip_detail = "";
			$iconLinks[]              = $iconLink;
		}
		else
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

		return $iconLinks;

	}

	static public function returnToMainComponent()
	{
		return;
	}

}
