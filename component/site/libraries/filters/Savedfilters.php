<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Reset.php 1976 2011-04-27 15:54:31Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class jevSavedfiltersFilter extends jevFilter
{
	function __construct($contentElement)
	{

		$this->filterNullValue = -1;
		$this->filterType      = "savedfilters";
		$this->filterField     = "";
		parent::__construct($contentElement, "");
	}

	function _createFilter($prefix = "")
	{

		return "";
	}

	/**
	 * Creates facility to save filter values
	 *
	 */
	function _createfilterHTML()
	{

		// Only save filters for non-guests
		if (Factory::getUser()->id == 0)
		{
			return false;
		}
		$app          = Factory::getApplication();
		$activeModule = isset($app->activeModule) ? $app->activeModule : false;
		$activemodid  = (isset($activeModule) ? $activeModule->id : 0);

		$filter          = array();
		$filter["title"] = Text::_("JEV_SAVED_FILTERS");
		$db              = Factory::getDbo();
		$db->setQuery("SELECT * FROM #__jevents_filtermap where userid = " . $db->quote(Factory::getUser()->id . " and modid=" . $activemodid));
		$filters        = $db->loadObjectList();
		$filter["html"] = '<input type="hidden" name="deletefilter" id="deletefilter" value="0"  />';
		if ($filters)
		{
			foreach ($filters as $fltr)
			{
				$base = Uri::current();
				$base .= (strpos($base, "?") > 0 ? "&" : "?") . "jfilter=" . $fltr->fid;
				// OR USE this
				/*
				$router = Router::getInstance("site");
				$vars = $router->getVars();
				$vars["jfilter"]=$fltr->fid;
				$base = "index.php?".http_build_query($vars);
				$base = Route::_($base);
				*/

				$filter["html"] .= '<div class="saved_filter_buttons uk-button-group"><a href="' . $base . '" class="uk-button uk-button-small uk-button-primary" >' . $fltr->name . ' </a>';
				$filter["html"] .= '<button id="saved_filter_buttons_img" class="uk-button uk-button-small uk-button-danger" type="button" onclick="jQuery(\'#deletefilter\').val(' . $fltr->fid . ');form.submit();" ><span class="uk-icon" data-uk-icon="icon:trash"></span></button>';
				$filter["html"] .= '</div>';
			}
			$filter["html"] .= "</br/>";
		}

		return $filter;

	}

}
