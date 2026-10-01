<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JEvents\Administrator;

defined('JPATH_BASE') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

\JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
\JLoader::register('JEventsHelper', JPATH_ADMINISTRATOR . "/components/com_jevents/helpers/jevents.php");

/**
 * Provides the JEvents sub-links for the admin left-nav menu used by addon components.
 * Legacy class name: GslJEventsMenuHelper
 */
class GslJEventsMenuHelper
{

	static public function getLeftIconSubLinks($leftmenutrigger)
	{

		$params = ComponentHelper::getParams("com_jevents");

		$iconLinks = array();

		$iconLink                 = new \stdClass();
		$iconLink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
		$iconLink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
		$iconLink->active         = false;
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=icalevent.list");
		$iconLink->onclick        = "(function(e) { document.location='" . $iconLink->link . " ';return false;})(event);";
		$iconLink->icon           = "calendar";
		$iconLink->label          = Text::_('JEV_ADMIN_ICAL_EVENTS');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_("JEV_INSTAL_MANAGE", true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new \stdClass();
		$iconLink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
		$iconLink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
		$iconLink->active         = false;
		$iconLink->link           = Route::_("index.php?option=com_categories&view=categories&extension=com_jevents");
		$iconLink->onclick        = "(function(e) { document.location='" . $iconLink->link . " ';return false;})(event);";
		$iconLink->icon           = "album";
		$iconLink->label          = Text::_('JEV_INSTAL_CATS');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_("JEV_INSTAL_CATS", true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		if (\JEVHelper::isAdminUser())
		{
			$iconLink                 = new \stdClass();
			$iconLink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
			$iconLink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
			$iconLink->active         = false;
			$iconLink->link           = Route::_("index.php?option=com_jevents&task=icals.list");
			$iconLink->onclick        = "(function(e) { document.location='" . $iconLink->link . " ';return false;})(event);";
			$iconLink->icon           = "calendars";
			$iconLink->label          = Text::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS');
			$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS', true);
			$iconLink->tooltip_detail = "";
			$iconLinks[]              = $iconLink;

			if ($params->get("authorisedonly", 0))
			{
				$iconLink                 = new \stdClass();
				$iconLink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
				$iconLink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
				$iconLink->active         = false;
				$iconLink->link           = Route::_("index.php?option=com_jevents&task=user.list");
				$iconLink->onclick        = "(function(e) { document.location='" . $iconLink->link . " ';return false;})(event);";
				$iconLink->icon           = "users";
				$iconLink->label          = Text::_('JEV_MANAGE_USERS');
				$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('JEV_MANAGE_USERS', true);
				$iconLink->tooltip_detail = "";
				$iconLinks[]              = $iconLink;
			}
		}

		$iconLink                 = new \stdClass();
		$iconLink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
		$iconLink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
		$iconLink->active         = false;
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=defaults.list");
		$iconLink->onclick        = "(function(e) { document.location='" . $iconLink->link . " ';return false;})(event);";
		$iconLink->icon           = "file-edit";
		$iconLink->label          = Text::_('JEV_LAYOUT_DEFAULTS');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('JEV_LAYOUT_DEFAULTS', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new \stdClass();
		$iconLink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
		$iconLink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
		$iconLink->active         = false;
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=cpanel.support");
		$iconLink->onclick        = "(function(e) { document.location='" . $iconLink->link . " ';return false;})(event);";
		$iconLink->icon           = "file-text";
		$iconLink->label          = Text::_('SUPPORT_INFO');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('SUPPORT_INFO', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		$iconLink                 = new \stdClass();
		$iconLink->class          = "gsl-button gsl-small gsl-button-secondary gsl-padding-remove gsl-text-left ";
		$iconLink->iconclass      = "gsl-margin-small-right gsl-display-inline-block";
		$iconLink->active         = false;
		$iconLink->link           = Route::_("index.php?option=com_jevents&task=params.edit");
		$iconLink->onclick        = "(function(e) { document.location='" . $iconLink->link . " ';return false;})(event);";
		$iconLink->icon           = "settings";
		$iconLink->label          = Text::_('COM_JEVENTS_CONFIGURATION');
		$iconLink->tooltip        = $leftmenutrigger !== 2 ? "" : Text::_('COM_JEVENTS_CONFIGURATION_TOOLTIP', true);
		$iconLink->tooltip_detail = "";
		$iconLinks[]              = $iconLink;

		return $iconLinks;
	}

}