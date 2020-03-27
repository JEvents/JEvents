<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3155 2012-01-05 12:01:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;

/**
 * HTML View class for the component frontend
 *
 * @static
 */
class AlternativeViewRange extends JEventsAlternativeView
{

	function listevents($tpl = null)
	{

		JEVHelper::componentStylesheet($this);

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		list($startdate, $enddate) = $this->getStartEndDates();

		list($startyear, $startmonth, $startday) = explode("-", $startdate);
		list($endyear, $endmonth, $endday) = explode("-", $enddate);

		$this->startdate    = $startdate;
		$this->startyear    = $startyear;
		$this->startmonth   = $startmonth;
		$this->startday     = $startday;
		$this->enddate      = $enddate;
		$this->endyear      = $endyear;
		$this->endmonth     = $endmonth;
		$this->endday       = $endday;

		$order = $params->get("dataorder", "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC");

		// Note that using a $limit value of -1 the limit is ignored in the query
		$this->data = $this->datamodel->getRangeData($startdate, $enddate, $this->limit, $this->limitstart, $order);

	}
}
