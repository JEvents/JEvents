<?php
/**
 * copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * HTML View class for the module  frontend
 *
 * @static
 */
include_once(JPATH_SITE . "/modules/mod_jevents_latest/tmpl/default/latest.php");

#[\AllowDynamicProperties]
class FlatModLatestView extends DefaultModLatestView
{

	function displayLatestEvents()
	{

		$cfg      = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$datenow  = JEVHelper::getNow();

		$app      = Factory::getApplication();
		$this->getLatestEventsData();

		$content = "";

		if (isset($this->eventsByRelDay) && count($this->eventsByRelDay))
		{

			$content .= $this->getModuleHeader('<table class="mod_events_latest_table jevbootstrap" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">');

			// Now to display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up.

			$firstTime = true;

			// initialize name of com_jevents module and task defined to view
			// event detail.  Note that these could change in future com_event
			// component revisions!!  Note that the '$this->itemId' can be left out in
			// the link parameters for event details below since the event.php
			// component handler will fetch its own id from the db menu table
			// anyways as far as I understand it.

			$task_events = 'icalrepeat.detail';

			$this->processFormatString();

			foreach ($this->eventsByRelDay as $relDay => $daysEvents)
			{

				reset($daysEvents);

				// get all of the events for this day
				foreach ($daysEvents as $dayEvent)
				{

					if ($this->processTemplate($content, $dayEvent))
					{
						continue;
					}

					$eventcontent = "";

					// generate output according custom string
					foreach ($this->splitCustomFormat as $condtoken)
					{

                        if ($this->conditionNotMet($condtoken, $dayEvent))
                        {
                                continue;
                        }

						foreach ($condtoken['data'] as $token)
						{
							unset($match);
							unset($dateParm);
							$dateParm = "";
							$match    = '';
							if (is_array($token))
							{
								$match    = $token['keyword'];
								$dateParm = isset($token['dateParm']) ? trim($token['dateParm']) : "";
							}
							else if (strpos($token, '${') !== false)
							{
								$match = $token;
							}
							else
							{
								$eventcontent .= $token;
								continue;
							}

							$this->processMatch($eventcontent, $match, $dayEvent, $dateParm, $relDay);
						} // end of foreach
					} // end of foreach

					$dst = "border-color:" . $dayEvent->bgcolor();
					if ($firstTime) $eventrow = '<tr><td class="mod_events_latest_first" style="' . $dst . '">%s' . "</td></tr>\n";
					else $eventrow = '<tr><td class="mod_events_latest" style="' . $dst . '">%s' . "</td></tr>\n";

					$templaterow = $this->modparams->get("modlatest_templaterow") ? $this->modparams->get("modlatest_templaterow") : $eventrow;
					$content     .= str_replace("%s", $eventcontent, $templaterow);

					$firstTime = false;
				} // end of foreach
			} // end of foreach
			$content .= $this->getModuleFooter("</table>\n");
		}
		else if ($this->modparams->get("modlatest_NoEvents", 1))
		{
			$content     .= $this->modparams->get("modlatest_templatetop") ? $this->modparams->get("modlatest_templatetop") : '<table class="mod_events_latest_table jevbootstrap" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';
			$templaterow = $this->modparams->get("modlatest_templaterow") ? $this->modparams->get("modlatest_templaterow") : '<tr><td class="mod_events_latest_noevents">%s</td></tr>' . "\n";
			$content     .= str_replace("%s", Text::_('JEV_NO_EVENTS'), $templaterow);
			$content     .= $this->modparams->get("modlatest_templatebottom") ? $this->modparams->get("modlatest_templatebottom") : "</table>\n";
		}

		$callink_HTML = '<div class="mod_events_latest_callink">'
			. $this->getCalendarLink()
			. '</div>';

		if ($this->linkToCal == 1) $content = $callink_HTML . $content;
		if ($this->linkToCal == 2) $content .= $callink_HTML;

		if ($this->displayRSS)
		{
			$rssimg       = Uri::root() . "media/com_jevents/images/livemarks.png";
			$callink_HTML = '<div class="mod_events_latest_rsslink">'
				. '<a href="' . $this->rsslink . '" title="' . Text::_("RSS_FEED") . '" target="_blank">'
				. '<img src="' . $rssimg . '" alt="' . Text::_("RSS_FEED") . '" />'
				. Text::_("SUBSCRIBE_TO_RSS_FEED")
				. '</a>'
				. '</div>';
			$content      .= $callink_HTML;
		}
		if ($this->modparams->get("contentplugins", 0))
		{
			$eventdata  = new stdClass();
			//$eventdata->text = str_replace("{/toggle","{/toggle}",$content);
			$eventdata->text = $content;
			$app->triggerEvent('onContentPrepare', array('com_jevents', &$eventdata, &$this->modparams, 0));
			$content = $eventdata->text;
		}

		return $content;
	} // end of function
} // end of class
