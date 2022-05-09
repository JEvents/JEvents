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

class JEventsModelicalevent extends ListModel
{
	public $queryModel;
	private $total = 0;
	private $_debug = false;

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
				'title', 'a.' . $db->quoteName('title'),'a.title',
				'catid', 'a.' . $db->quoteName('catid'), 'a.catid',
				'showpast', 'a.' . $db->quoteName('showpast'), 'a.showpast',
				'state', 'a.' . $db->quoteName('state'), 'a.state',
				'ordering', 'a.' . $db->quoteName('ordering'), ' a.ordering',
				'created_by', 'a.' . $db->quoteName('created_by'), 'a.created_by',
				'modified_by', 'a.' . $db->quoteName('modified_by'), 'a.modified_by',
				'starttime', 'a.' . $db->quoteName('starttime'), 'a.starttime',
				'access', 'a.' . $db->quoteName('access'), 'a.access',
				'tag'
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

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);

		// Set the model state set flag to true.  Needed to stop recursion
		$this->__state_set = true;

		// Special sanitising of values
		if ($this->getState('filter.icsFile', '£$%$£') !== '£$%$£')
		{
			$this->setState('filter.icsFile', (int) $this->getState('filter.icsFile', '£$%$£'));
		}
	}
		/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		// get the state data into the model
		$this->getStoreId();

		$db = Factory::getDbo();
		$app   = Factory::getApplication();
		$input = $app->input;

		$showUnpublishedICS = JEVHelper::isAdminUser();
		JEV_CommonFunctions::getCategoryData();

		$icsFile = intval($this->getState('filter.icsFile', 0));
		$state = $this->getState('filter.state', 3);
		$created_by = $this->getState('filter.created_by', "");
		if ($created_by !== '')
		{
			$created_by = (int) $created_by;
		}
		$catid    = intval($this->getState('filter.catid', 0));
		$showpast = $this->getState("filter.showpast", '');
		if ($showpast === "")
		{
			$showpast = 0;
		}

		$search     = $this->getState('filter.search', '');
		$search     = $db->escape(trim(strtolower($search)));
		// sanitised later
		$access = $this->getState("filter.access", '');

		$limit = $this->getState('list.limit',  $app->getCfg('list_limit', 10));
		$limitstart = $this->getState('list.start',  0);

		// Club Plugin checks
		$tags = 0;
		$tagsPlugin = PluginHelper::isEnabled('jevents', 'jevtags');

		if ($tagsPlugin) {
			$tagsArray  = $app->getUserStateFromRequest("taglkup_fvs", "taglkup_fvs", array());
			$tags       = implode(',', $tagsArray);
			$this->tagsFiltering = true;
		}

		// Is this a large dataset ?
		$query = "SELECT count(rpt.rp_id) from #__jevents_repetition as rpt ";
		$db->setQuery($query);
		$totalrepeats        = $db->loadResult();
		$this->_largeDataSet = 0;
		$cfg                 = JEVConfig::getInstance();
		if ($totalrepeats > $cfg->get('largeDataSetLimit', 100000))
		{
			$this->_largeDataSet = 1;
		}
		$cfg = JEVConfig::getInstance();
		$cfg->set('largeDataSet', $this->_largeDataSet);

		$where = array();
		$join  = array();

		if ($search)
		{
			$searchwhere   = array();
			$searchwhere[] = "LOWER(detail.summary) LIKE '%$search%'";
			$searchwhere[] = "LOWER(detail.location) LIKE '%$search%'";
			jimport("joomla.filesystem.folder");
			if (Folder::exists(JPATH_ADMINISTRATOR . "/components/com_jevlocations") && ComponentHelper::getComponent("com_jevlocations", true)->enabled)
			{
				$join[]        = "\n #__jev_locations as loc ON loc.loc_id=detail.location";
				$searchwhere[] = "LOWER(loc.title) LIKE '%$search%'";
				$searchwhere[] = "LOWER(loc.city) LIKE '%$search%'";
				$searchwhere[] = "LOWER(loc.postcode) LIKE '%$search%'";
			}
			$where[] = "(" . implode(" OR ", $searchwhere) . " )";
		}

		$user = Factory::getUser();

		// keep this incase we use filters in category lists
		$catwhere = "\n ev.catid IN(" . $this->queryModel->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams($input->getCmd("option"));
		if ($params->get("multicategory", 0))
		{
			$join[]     = "\n #__jevents_catmap as catmap ON catmap.evid = ev.ev_id";
			$join[]     = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$where[]    = " catmapcat.access " . ' IN (' . JEVHelper::getAid($user) . ')';
			$where[]    = " catmap.catid IN(" . $this->queryModel->accessibleCategoryList() . ")";
			$needsgroup = true;
			$catwhere   = " 1";
		}
		$where[] = $catwhere;

		// category filter!
		$db->setQuery("SELECT * FROM #__categories where id = " . intval($catid));
		$filtercat = $db->loadObject();

		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		$cats           = $user->getAuthorisedCategories('com_jevents', 'core.create');
		if (isset($user->id) && !$user->authorise('core.create', 'com_jevents') && !$authorisedonly)
		{
			if (count($cats) > 0 && $catid < 1)
			{
				for ($i = 0; $i < count($cats); $i++)
				{
					if ($params->get("multicategory", 0))
					{
						$whereCats[$i] = "catmap.catid='$cats[$i]'";
					}
					else
					{
						$whereCats[$i] = "ev.catid='$cats[$i]'";
					}
				}
				$where[] = '(' . implode(" OR ", $whereCats) . ')';
			}
			else if (count($cats) > 0 && $catid > 0 && in_array($catid, $cats))
			{
				if ($params->get("multicategory", 0))
				{
					if ($filtercat)
					{
						$where[] = "catmapcat.lft BETWEEN $filtercat->lft AND $filtercat->rgt";
					}
					else
					{
						$where[] = "catmap.catid='$catid'";
					}
				}
				else
				{
					$where[] = "ev.catid='$catid'";
				}
			}
			else
			{
				if ($params->get("multicategory", 0))
				{
					$where[] = "catmap.catid=''";
				}
				else
				{
					$where[] = "ev.catid=''";
				}
			}
		}
		else
		{
			if ($catid > 0)
			{
				if ($params->get("multicategory", 0))
				{
					if ($filtercat)
					{
						$where[] = "catmapcat.lft BETWEEN $filtercat->lft AND $filtercat->rgt";
					}
					else
					{
						$where[] = "catmap.catid='$catid'";
					}
				}
				else
				{
					$where[] = "ev.catid='$catid'";
				}
			}
		}

		if ($created_by !== "" && intval($created_by) >= 0)
		{
			$cby = intval($created_by);
			if ($cby >= 0 && $created_by !== "")
				$where[] = "ev.created_by=" . $db->Quote($cby);
		}

		if ($icsFile > 0)
		{
			$join[]  = " #__jevents_icsfile as icsf ON icsf.ics_id = ev.icsid";
			$where[] = "\n icsf.ics_id = $icsFile";
			if (!$showUnpublishedICS)
			{
				$where[] = "\n icsf.state=1";
			}
		}
		else
		{
			if (!$showUnpublishedICS)
			{
				$join[]  = " #__jevents_icsfile as icsf ON icsf.ics_id = ev.icsid";
				$where[] = "\n icsf.state=1";
			}
			else
			{
				$icsFrom = "";
			}
		}

		if ($access !== "")
		{
			$access = (int) $access;
			$where[]    = " ev.access = " . $access;
		}

		if (!$showpast)
		{
			$datenow = JevDate::getDate("-1 day");
			if (!$this->_largeDataSet)
			{
				$where[] = "\n rpt.endrepeat>'" . $datenow->toSql() . "'";
			}
		}
		if ($state !== '*' && $state !== '')
		{
			$state = intval($state);
			if ($state == 0)
			{
				$where[] = "\n ev.state=0";
			}
			else if ($state == 1)
			{
				$where[] = "\n ev.state=1";
			}
			else if ($state == 2)
			{
				$where[] = "\n ev.state=0";
			}
			else if ($state == -2)
			{
				$where[] = "\n ev.state=-1";
			}
			else if ($state == 3)
			{
				$where[] = "\n (ev.state=1 OR ev.state=0)";
			}
		}
		else
		{
			// only published and unpublished unless specifically looking for other types of state
			$where[] = "\n (ev.state=1 OR ev.state=0)";
		}

		// if anon user plugin enabled then include this information
		$anonfields = "";
		$anonjoin = "";
		if (PluginHelper::importPlugin("jevents", "jevanonuser"))
		{

			$anonfields = ", ac.name as anonname, ac.email as anonemail";
			$anonjoin = "\n LEFT JOIN #__jev_anoncreator as ac on ac.ev_id = ev.ev_id";
		}

		// Allow tag filtering
		$tagsFields = '';
		$tagsJoin   = '';
		$this->tagsFilter = false;

		if ($tagsPlugin && $tags) {
			$tagsFields = ", tg.tag_id, tg.title ";
			$tagsJoin   = "\n LEFT JOIN #__jev_tageventsmap as tagmap ON tagmap.ev_id = ev.ev_id ";
			$tagsJoin  .= "\n LEFT JOIN  #__jev_tags as tg ON tagmap.ev_id = ev.ev_id ";
			if ($tags)
			{
				$where[] = "\n tagmap.tag_id IN (" . $tags . ")";
				$this->tagsFilter = $tags;

			} else {
				$this->tagsFilter = false;
			}
		}


		// get the total number of records
		if ($this->_largeDataSet)
		{
			$query = "SELECT count(distinct ev.ev_id)"
				. "\n FROM #__jevents_vevent AS ev "
				. "\n LEFT JOIN #__jevents_vevdetail as detail ON ev.detail_id=detail.evdet_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				//. "\n LEFT JOIN #__groups AS g ON g.id = ev.access"
				. $tagsJoin
				. (count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '')
				. (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');
		}
		else
		{
			$query = "SELECT count(distinct rpt.eventid)"
				. "\n FROM #__jevents_vevent AS ev "
				. "\n LEFT JOIN #__jevents_vevdetail as detail ON ev.detail_id=detail.evdet_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				//. "\n LEFT JOIN #__groups AS g ON g.id = ev.access"
				. $tagsJoin
				. (count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '')
				. (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');
		}
		$db->setQuery($query);

		$total = 0;

		try
		{
			$total = $db->loadResult();
		} catch (Exception $e) {
			echo $e;
		}

		$this->total = $total;

		if ($limit > $total)
		{
			$limitstart = 0;
		}

		$query .= "\n GROUP BY  ev.ev_id";

		$orderdir = $app->getUserStateFromRequest("eventsorderdir", "filter_order_Dir", 'asc');
		$order    = $app->getUserStateFromRequest("eventsorder", "filter_order", 'start');

		if ($params->get("multicategory", 0))
		{
			$anonfields .= ", GROUP_CONCAT(DISTINCT catmap.catid SEPARATOR ',') as catids";
		}

		$dir = $orderdir == "asc" ? "asc" : "desc";

		if ($order == 'start' || $order == 'starttime')
		{
			$order = ($this->_largeDataSet ? "\n GROUP BY  ev.ev_id ORDER BY detail.dtstart $dir" : "\n GROUP BY  ev.ev_id ORDER BY rpt.startrepeat $dir");
		}
		else if ($order == 'created')
		{
			$order = ($this->_largeDataSet ? "\n GROUP BY  ev.ev_id ORDER BY ev.created $dir" : "\n GROUP BY  ev.ev_id ORDER BY ev.created $dir");
		}
		else if ($order == 'modified')
		{
			$order = ($this->_largeDataSet ? "\n GROUP BY  ev.ev_id ORDER BY modified $dir" : "\n GROUP BY  ev.ev_id ORDER BY modified $dir");
		}
		else
		{
			$order = ($this->_largeDataSet ? "\n GROUP BY  ev.ev_id ORDER BY detail.summary $dir" : "\n GROUP BY  ev.ev_id ORDER BY detail.summary $dir");
		}

		// only include repeat id since we need it if we call plugins on the resultant data
		$query = "SELECT ev.* " .  ($this->_largeDataSet ? "" :", rpt.rp_id") . ", ev.state as evstate, detail.*, ev.created as created, max(detail.modified) as modified,  a.title as _groupname " . $anonfields . $tagsFields
			. "\n , rr.rr_id, rr.freq,rr.rinterval"//,rr.until,rr.untilraw,rr.count,rr.bysecond,rr.byminute,rr.byhour,rr.byday,rr.bymonthday"
			. ($this->_largeDataSet ? "" : "\n ,MAX(rpt.endrepeat) as endrepeat ,MIN(rpt.startrepeat) as startrepeat"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn")
			. "\n FROM #__jevents_vevent as ev "
			. ($this->_largeDataSet ? "" : "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id")
			. $anonjoin
			. $tagsJoin
			. "\n LEFT JOIN #__jevents_vevdetail as detail ON ev.detail_id=detail.evdet_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n LEFT JOIN #__viewlevels AS a ON a.id = ev.access"
			. ( count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '' )
			. ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
			. $order;

		if ($limit > 0)
		{
			$query .= "\n LIMIT $limitstart, $limit";
		}
		$db->setQuery($query);

		//echo $db->explain();
		$rows = $db->loadObjectList();
		//echo $db->getQuery()."<br/>";
		foreach ($rows as $key => $val)
		{
			// set state variable to the event value not the event detail value
			$rows[$key]->state      = $rows[$key]->evstate;
			$groupname              = $rows[$key]->_groupname;
			$rows[$key]             = new jIcalEventRepeat($rows[$key]);
			$rows[$key]->_groupname = $groupname;
		}
		$cfg          = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		if ($this->_debug)
		{
			echo '[DEBUG]<br />';
			echo 'query:';
			echo '<pre>';
			echo $query;
			echo '-----------<br />';
			echo 'option "' . JEV_COM_COMPONENT . '"<br />';
			echo '</pre>';
			//die( 'userbreak - mic ' );
		}
		return $rows;

	}

	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param    array   $data     Data for the form.
	 * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return    mixed    A Form object on success, false on failure
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{

		// Prepare the data
		// Experiment in the use of Form and template override for forms and fields
		Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/");
		$template = Factory::getApplication()->getTemplate();
		Form::addFormPath(JPATH_THEMES . "/$template/html/com_jevents/forms");
		//Form::addFieldPath(JPATH_THEMES."/$template/html/com_jevents/fields");

		$xpath = false;
		// leave form control blank since we want the fields as ev_id and not Form[ev_id]
		$form = $this->loadForm("jevents.edit.icalevent", 'icalevent', array('control' => '', 'load_data' => false), false, $xpath);
		Form::addFieldPath(JPATH_THEMES . "/$template/html/com_jevents/fields");

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function getTranslateForm($data = array(), $loadData = true)
	{

		// Prepare the data
		// Experiment in the use of Form and template override for forms and fields
		Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/");
		$template = Factory::getApplication()->getTemplate();
		Form::addFormPath(JPATH_THEMES . "/$template/html/com_jevents/forms");

		$xpath = false;
		// leave form control blank since we want the fields as ev_id and not Form[ev_id]
		$form = $this->loadForm("jevents.translate.icalevent", 'translate', array('control' => '', 'load_data' => false), false, $xpath);
		Form::addFieldPath(JPATH_THEMES . "/$template/html/com_jevents/fields");

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function getOriginal()
	{

		$db = Factory::getDbo();
		$input  = Factory::getApplication()->input;

		$evdet_id = $input->getInt("evdet_id", 0);
		$db->setQuery("SELECT * FROM #__jevents_vevdetail where evdet_id = " . $evdet_id);
		$data = $db->loadAssoc();

		return $data;
	}

	public function getTranslation()
	{

		$db = Factory::getDbo();

		$input  = Factory::getApplication()->input;

		$evdet_id = $input->getInt("evdet_id", 0);
		$lang     = $input->getString("lang", "");
		$db->setQuery("SELECT * FROM #__jevents_translation where evdet_id = " . $evdet_id . " AND language = " . $db->quote($lang));
		$tempdata = $db->loadAssoc();
		$data     = array();
		if ($tempdata)
		{
			foreach ($tempdata as $key => $val)
			{
				$data["trans_" . $key] = $val;
			}
		}

		return $data;
	}

	public function saveTranslation()
	{

		$input  = Factory::getApplication()->input;

		$array = JEVHelper::arrayFiltered($input->getArray(array(), null, 'RAW'));

		// Should we allow raw content through unfiltered
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("allowraw", 0))
		{

			$array['trans_description'] = $input->post->get("trans_description", "", 'RAW');
			$array['trans_extra_info']  = $input->post->get("trans_extra_info", "", 'RAW');
		}

		include_once JPATH_COMPONENT . "/tables/translate.php";
		$translation = new TableTranslate();
		$success     = $translation->save($array);

		if ($success)
		{
			Factory::getApplication()->triggerEvent('onSaveTranslation', array($array, true));
		}

		return $success;
	}

	public function deleteTranslation()
	{

		$input  = Factory::getApplication()->input;

		include_once JPATH_COMPONENT . "/tables/translate.php";
		$translation = new TableTranslate();
		$translation->delete($input->getInt("trans_translation_id"));
	}

	function getLanguages()
	{

		static $languages;
		if (!isset($languages))
		{
			$db = Factory::getDbo();

			// get the list of languages first
			$query = $db->getQuery(true);
			$query->select("l.*");
			$query->from("#__languages as l");
			$query->where('l.lang_code <> "xx-XX"');
			$query->order("l.lang_code asc");

			$db->setQuery($query);
			$languages = $db->loadObjectList('lang_code');
		}

		return $languages;
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 * @since    3.0
	 */
	// MUST use JForm here otherwise won't run in Joomla 3.9
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{

		// Association content items
		$app   = Factory::getApplication();
		$assoc = false && Associations::isEnabled() && Factory::getApplication()->isClient('administrator');
		if ($assoc)
		{
			$languages = LanguageHelper::getLanguages('lang_code');
			$addform   = new SimpleXMLElement('<form />');
			$fields    = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_JEVENTS_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add   = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_article');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

}
