<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');


use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;

class JEventsModelicalrepeat extends ListModel
{

	public $total = 0;

	/**
	 * Constructor
	 *
	 * @param array $config Configuration array for model. Optional.
	 *
	 * @since 1.5
	 */
	function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$db = Factory::getDbo();

			$config['filter_fields'] = array(
				'id', 'a.' . $db->quoteName('id'), 'a.id',
				'showpast', 'a.' . $db->quoteName('showpast'), 'a.showpast',
				'ordering', 'a.' . $db->quoteName('ordering'), ' a.ordering'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');
		$id .= ':' . $this->getState('filter.showpast');

		return md5($this->context . ':' . $id);
	}

	public function getTotal()
	{
		// get the state data into the model
		$this->getStoreId();

		$input = Factory::getApplication()->input;

		$db            = Factory::getDbo();
		$publishedOnly = false;
		$cid           = $input->get('cid', array(0), "array");
		$cid           = ArrayHelper::toInteger($cid);

		if (is_array($cid) && count($cid) > 0)
			$id = $cid[0];
		else
			$id = $cid;

		// if cancelling a repeat edit then I get the event id a different way
		$evid = $input->getInt("evid", 0);
		if ($evid > 0)
		{
			$id = $evid;
		}

		$searchText = $this->getState('filter.search', '');
        $searchText	= $db->escape(trim(strtolower($searchText)));

		$searchTextQuery =  '';

		if (!empty(($searchText))) {
			$searchTextQuery = "\n AND LOWER(det.summary) LIKE '%$searchText%'";
		}

		$app    = Factory::getApplication();
		$showpast = intval($this->getState('filter.showpast', 0));
		if (!$showpast)
		{
			$datenow = JevDate::getDate("-1 day");
			$searchTextQuery .= "\n AND rpt.endrepeat>'" . $datenow->toSql() . "'";
		}

		$query = "SELECT count( DISTINCT rpt.rp_id)"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n WHERE ev.ev_id=" . $id
			. $searchTextQuery
			. "\n AND icsf.state=1"
			. ($publishedOnly ? "\n AND ev.state=1" : "");
		$db->setQuery($query);
		$total = $db->loadResult();

		$this->total = $total;
		return $total;
	}

	public function getItems()
	{
		// get the state data into the model
		$this->getStoreId();

		$input = Factory::getApplication()->input;

		$db            = Factory::getDbo();
		$publishedOnly = false;
		$cid           = $input->get('cid', array(0), "array");
		$cid           = ArrayHelper::toInteger($cid);

		if (is_array($cid) && count($cid) > 0)
			$id = $cid[0];
		else
			$id = $cid;

		$searchText = $this->getState('filter.search', '');
        $searchText	= $db->escape(trim(strtolower($searchText)));

		$searchTextQuery =  '';

		if (!empty(($searchText))) {
			$searchTextQuery = "\n AND LOWER(det.summary) LIKE '%$searchText%'";
		}

		$app    = Factory::getApplication();
		$showpast = intval($this->getState('filter.showpast', 0));
		if (!$showpast)
		{
			$datenow = JevDate::getDate("-1 day");
			$searchTextQuery .= "\n AND rpt.endrepeat >'" . $datenow->toSql() . "'";
		}

		// if cancelling a repeat edit then I get the event id a different way
		$evid = $input->getInt("evid", 0);
		if ($evid > 0)
		{
			$id = $evid;
		}

		$limit = $this->getState('list.limit',  $app->getCfg('list_limit', 10));
		$limitstart = $this->getState('list.start',  0);

		if ($limit > $this->total)
		{
			$limitstart = 0;
		}

		$query = "SELECT ev.*, rpt.*, rr.*, det.*, exc.exception_type"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_exception as exc ON rpt.rp_id = exc.rp_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n WHERE ev.ev_id=" . $id
			. $searchTextQuery
			. "\n AND icsf.state=1"
			. ($publishedOnly ? "\n AND ev.state=1" : "")
			. "\n GROUP BY rpt.rp_id"
			. "\n ORDER BY rpt.startrepeat";
		if ($limit > 0)
		{
			$query .= "\n LIMIT $limitstart, $limit";
		}

		$db->setQuery($query);
		$icalrows  = $db->loadObjectList();
		$icalcount = count($icalrows);
		for ($i = 0; $i < $icalcount; $i++)
		{
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}

		return $icalrows;

	}
}
