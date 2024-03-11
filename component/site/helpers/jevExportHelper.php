<?php
/**
 * JEvents Component for Joomla
 *
 * @version     $Id: jevExportHelper.php
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * JEvents component helper.
 *
 * @package        Jevents
 * @since          1.6
 */
class JevExportHelper
{

	static function getAddToGCal($row)
	{

		$eventData = JevExportHelper::getEventStringArray($row);

		$urlString['title']       = "text=" . $eventData['title'];
		$urlString['dates']       = "dates=" . $eventData['dates'];
		$urlString['location']    = "location=" . $eventData['location'];
		$urlString['trp']         = "trp=false";
		$urlString['websiteName'] = "sprop=" . $eventData['sitename'];
		$urlString['websiteURL']  = "sprop=name:" . $eventData['siteurl'];
		$urlString['details']     = "details=" . $eventData['rawdetails'];
		$link                     = "http://www.google.com/calendar/event?action=TEMPLATE&" . implode("&", $urlString);

		return $link;
	}

	static function getEventStringArray($row)
	{

		$urlString['title'] = urlencode($row->title());
		$params             = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$tz                 = $params->get("icaltimezonelive", "");
		if ($tz)
		{
			$urlString['dates'] = date("Ymd\THis", $row->getUnixStartTime()) . "/" . date("Ymd\THis", $row->getUnixEndTime()) . "&ctz=" . $tz;
		}
		else
		{
			$urlString['dates'] = date("Ymd\THis", $row->getUnixStartTime()) . "/" . date("Ymd\THis", $row->getUnixEndTime());
		}
		$urlString['st']         = date("Ymd\THis", $row->getUnixStartTime());
		$urlString['et']         = date("Ymd\THis", $row->getUnixEndTime());
		$urlString['duration']   = (int) $row->getUnixEndTime() - (int) $row->getUnixStartTime();
		$urlString['duration']   = (int) $row->getUnixEndTime() - (int) $row->getUnixStartTime();
		$urlString['location']   = urlencode(isset($row->_locationaddress) ? $row->_locationaddress : $row->location());
		$urlString['sitename']   = urlencode(Factory::getApplication()->get('sitename'));
		$urlString['siteurl']    = urlencode(Uri::root());

        $htmlDesc = $row->get('description');
        // convert relative to absolute URLs
        $htmlDesc = preg_replace('#(href|src|action|background)[ ]*=[ ]*\"(?!(https?://|\#|mailto:|/))(?:\.\./|\./)?#', '$1="' . JURI::root(), $htmlDesc);
        $htmlDesc = preg_replace('#(href|src|action|background)[ ]*=[ ]*\"(?!(https?://|\#|mailto:))/#', '$1="' . JURI::root(), $htmlDesc);

        $htmlDesc = preg_replace("#(href|src|action|background)[ ]*=[ ]*\'(?!(https?://|\#|mailto:|/))(?:\.\./|\./)?#", "$1='" . JURI::root(), $htmlDesc);
        $htmlDesc = preg_replace("#(href|src|action|background)[ ]*=[ ]*\'(?!(https?://|\#|mailto:))/#", "$1='" . JURI::root(), $htmlDesc);

		$urlString['details']    = strip_tags($htmlDesc);
        $urlString['rawdetails'] = $htmlDesc;

		if (StringHelper::strlen($urlString['details']) > 500)
		{
			$urlString['details'] = StringHelper::substr($urlString['details'], 0, 500) . ' ...';

		}

        //Check if we should include the link to the event
        if ($params->get('source_url', 0) == 1)
        {
            $link                 = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), true, $params->get('default_itemid', 0));
            $uri                  = Uri::getInstance(Uri::base());
            $root                 = $uri->toString(array('scheme', 'host', 'port'));
            $urlString['rawdetails'] .= ' ' .  Text::_('JEV_EVENT_IMPORTED_FROM') . ' <a href="'. $root . Route::_($link, true, -1) . '">' . $root . Route::_($link, true, -1) . "</a>";
            $urlString['details'] .= ' ' . Text::_('JEV_EVENT_IMPORTED_FROM') . $root . Route::_($link, true, -1);
        }

        $urlString['rawdetails'] = urlencode($urlString['rawdetails']);
        $urlString['details']    = urlencode($urlString['details']);

		return $urlString;
	}

	static function getAddToYahooCal($row)
	{

		$eventData = JevExportHelper::getEventStringArray($row);

		$urlString['title']      = "title=" . $eventData['title'];
		$urlString['st']         = "st=" . $eventData['st'];
		$urlString['et']         = "et=" . $eventData['et'];
		$urlString['rawdetails'] = "desc=" . $eventData['details'];
		$urlString['location']   = "in_loc=" . $eventData['location'];
		$link                    = "https://calendar.yahoo.com/?v=60&view=d&type=20&" . implode("&", $urlString);

		return $link;
	}
    static function getOutlookPrep($row)
    {
	    $jevparams  = ComponentHelper::getParams(JEV_COM_COMPONENT);
	    $eventData = JevExportHelper::getEventStringArray($row);

	    $urlString['title']      = "subject=" . $eventData['title'];

	    $jtz        = $jevparams->get("icaltimezonelive", "");
	    $jtz        = isset($event->_tzid) && !empty($event->_tzid) ? $event->_tzid : $jtz;
	    if (!empty($jtz))
	    {
		    $jtz = new DateTimeZone($jtz);
	    }
	    else
	    {
		    $jtz = new DateTimeZone(@date_default_timezone_get());
	    }

	    $utctz = new DateTimeZone('UTC');
	    if ($row->alldayevent())
	    {
		    $startDateFormatted = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), "%Y-%m-%d") ;
	    }
	    else
	    {
		    $startDate = JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hup(), $row->minup());
		    $indate  = new DateTime($startDate, $jtz);
		    $indate->setTimezone($utctz);
		    $startDateFormatted = $indate->format('Y-m-d\TH:i:s') . "Z";

		    if ($row->noendtime())
		    {
			    $endDateFormatted = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), "%Y-%m-%d") ;
		    }
		    else
		    {
			    $endDate = JEventsHTML::getDateFormat($row->ydn(), $row->mdn(), $row->ddn(), "%Y-%m-%d") . "T" . sprintf('%02d:%02d:00', $row->hdn(), $row->mindn());
			    $outdate = new DateTime($endDate);
			    $outdate->setTimezone($utctz);
			    $endDateFormatted = $outdate->format('Y-m-d\TH:i:s') . "Z";
		    }

	    }

	    $urlString['st']         = "startdt=" . $startDateFormatted;
		if (isset($endDateFormatted))
		{
			$urlString['et'] = "enddt=" . $endDateFormatted;
		}
	    $urlString['rawdetails'] = "body=" . $eventData['rawdetails'];
	    $urlString['location']   = "location=" . $eventData['location'];

		return $urlString;
    }

	static function getAddToOutlookLive($row)
	{

		$urlString = self::getOutlookPrep($row);
		$link                    = "https://outlook.live.com/owa/?path=/calendar/action/compose&rru=addevent&" . implode("&", $urlString);

		return $link;
	}

	static function getAddToMsOutlook($row)
	{
		$urlString = self::getOutlookPrep($row);
		$link                    = "https://outlook.office.com/owa/?path=/calendar/action/compose&rru=addevent&" . implode("&", $urlString);

		return $link;
	}

}
