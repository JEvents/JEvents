<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Justmine.php 2657 2011-09-28 11:42:45Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Filters events to restrict events for administration - used for administration of events in the frontend
 * Only show events created by the user themselves
 */
class jevJustmineFilter extends jevFilter
{
	const filterType = "justmine";
	var $label = "";
	var $yesLabel = "";
	var $noLabel = "";
	var $isEventAdmin = false;

	function __construct($tablename, $filterfield, $isstring = true, $yesLabel = "Jev_Yes", $noLabel = "Jev_No")
	{

		$input = Factory::getApplication()->input;

		$this->filterType      = self::filterType;
		$this->filterNullValue = "0";
		$this->yesLabel        = Text::_($yesLabel);
		$this->noLabel         = Text::_($noLabel);
		$this->filterLabel     = Text::_("Show_Only_My_Events");
		$this->filterLabelEscaped      = Text::_("Show_Only_My_Events", true);

		// this is a special filter - we always want memory here since only used in frontend management

		$this->filter_value = Factory::getApplication()->getUserStateFromRequest($this->filterType . '_fv_ses', $this->filterType . '_fv', $this->filterNullValue);
		$input->set($this->filterType . '_fv', $this->filter_value);

		parent::__construct($tablename, "state", $isstring);

		// Should these be ignored?
		$reg       = Factory::getConfig();
		$modparams = $reg->get("jev.modparams", false);
		if ($modparams && $modparams->get("ignorefiltermodule", false))
		{
			$this->filter_value = $this->filterNullValue;
		}

	}

	function _createFilter($prefix = "")
	{

		if (!$this->filterField) return "";
		if ($this->filter_value == $this->filterNullValue) return "";
		// The default to show all events
		$user = Factory::getUser();

		return "ev.created_by=" . $user->id;
	}

	function _createfilterHTML()
	{

		$filterList          = array();
		$filterList["title"] = $this->filterLabel;
		$options             = array();
		$options[]           = HTMLHelper::_('select.option', "0", $this->noLabel, "value", "yesno");
		$options[]           = HTMLHelper::_('select.option', "1", $this->yesLabel, "value", "yesno");
		$filterList["html"]  = HTMLHelper::_('select.genericlist', $options, $this->filterType . '_fv', 'class="inputbox" aria-label="' . $this->filterLabelEscaped . '" size="1" onchange="form.submit();"', 'value', 'yesno', $this->filter_value);

		return $filterList;
	}

}
