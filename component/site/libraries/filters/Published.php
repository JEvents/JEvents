<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Published.php 3549 2012-04-20 09:26:21Z geraintedwards $
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
 */
class jevPublishedFilter extends jevFilter
{
	const filterType = "published";
	var $label = "";
	var $yesLabel = "";
	var $noLabel = "";
	var $isEventAdmin = false;

	function __construct($tablename, $filterfield, $isstring = true, $yesLabel = "Jev_Yes", $noLabel = "Jev_No")
	{

		$input = Factory::getApplication()->input;

		$this->filterType = self::filterType;
		$task             = $input->get('view', '') . '.' . $input->get('layout', '');
		if ($task == "admin.listevents")
		{
			$default_filter = "-1";
		}
		else
		{
			$default_filter = "0";
		}

		$this->filterNullValue = $default_filter;
		$this->allLabel        = Text::_('ALL');
		$this->yesLabel        = Text::_($yesLabel);
		$this->noLabel         = Text::_($noLabel);
		$this->filterLabel     = Text::_("Show_Unpublished_Events");
		$this->filterLabelEscaped     = Text::_("Show_Unpublished_Events", true);

		// this is a special filter - we always want memory here since only used in frontend management

		$this->filter_value = Factory::getApplication()->getUserStateFromRequest($this->filterType . '_fv_ses', $this->filterType . '_fv', $this->filterNullValue);
		$input->set($this->filterType . '_fv', $this->filter_value);

		parent::__construct($tablename, "state", $isstring);

		// event creators can look at their own unpublished events
		if (!JEVHelper::isEventCreator())
		{
			$this->filter_value = $this->filterNullValue;
		}
	}

	function _createFilter($prefix="")
	{
		// The default is only to show published events
		if (!$this->filterField || $this->filter_value==0) return "ev.state=1";

		// Always only show published events to non-logged in users
		$user = Factory::getUser();
		if ($user->get('id')==0){
			return "ev.state=1";
		}

		// Can delete own events?
		if (JEVHelper::isEventDeletor(false))
		{
			if ($this->filter_value==-1)
			{
				// Do not show other user's trashed events show all of your own
				return "(ev.state <> -1 OR ev.created_by = " . $user->id . ")";
			}
			else
			{
				// Show Any unpublished events
				return "ev.state = 0";
			}
		}
		// Can delete all events
		else if (JEVHelper::isEventDeletor(true))
		{
			if ($this->filter_value==-1)
			{
				// show everything
				return "";
			}
			else
			{
				// Show Any unpublished events
				return "ev.state = 0";
			}
		}
		else if (JEVHelper::isEventPublisher(true) || JEVHelper::isEventEditor()){
			// Show all should not include trashed events
			if ($this->filter_value==-1) return "ev.state<>-1";
			return "ev.state=0";
		}
		// publish own checks
		else if  (JEVHelper::isEventCreator()){
			if ($this->filter_value==-1)
			{
				// Only show unpublished (ex trashed) events if created by this user
				return "(ev.state=1 OR (ev.created_by=" . $user->id . " AND ev.state <> -1))";
			}
			else
			{
				return "(ev.created_by=" . $user->id . " AND ev.state = 0)";
			}
		}

		return "ev.state=1";
	}

	function _createfilterHTML()
	{

		$filterList          = array();
		$filterList["title"] = $this->filterLabel;
		$options             = array();
		$options[]           = HTMLHelper::_('select.option', "-1", $this->allLabel, "value", "yesno");
		$options[]           = HTMLHelper::_('select.option', "0", $this->noLabel, "value", "yesno");
		$options[]           = HTMLHelper::_('select.option', "1", $this->yesLabel, "value", "yesno");
		$filterList["html"]  = HTMLHelper::_('select.genericlist', $options, $this->filterType . '_fv', 'class="inputbox" aria-label="' . $this->filterLabelEscaped . '" size="1" onchange="form.submit();"', 'value', 'yesno', $this->filter_value);

		return $filterList;
	}

	function _createfilterHtmlUIkit()
	{

		$filterList          = array();
		$filterList["title"] = $this->filterLabel;
		$options             = array();
		$options[]           = HTMLHelper::_('select.option', "-1", $this->allLabel, "value", "yesno");
		$options[]           = HTMLHelper::_('select.option', "0", $this->noLabel, "value", "yesno");
		$options[]           = HTMLHelper::_('select.option', "1", $this->yesLabel, "value", "yesno");
		$filterList["html"]  = HTMLHelper::_('select.genericlist', $options, $this->filterType . '_fv', 'class="uk-select uk-form-width-medium" aria-label="' . $this->filterLabelEscaped . '"  onchange="form.submit();"', 'value', 'yesno', $this->filter_value);

		return $filterList;
	}

}
