<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 3401 2012-03-22 15:35:38Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminIcaleventViewIcalevent extends JEventsAbstractView
{

	function overview($tpl = null)
	{


		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('ICAL_EVENTS'));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('ICAL_EVENTS'), 'jevents');

		JToolBarHelper::addNew('icalevent.edit');
		JToolBarHelper::editList('icalevent.edit');
		JToolBarHelper::publishList('icalevent.publish');
		JToolBarHelper::unpublishList('icalevent.unpublish');
		JToolBarHelper::custom('icalevent.editcopy', 'copy.png', 'copy.png', 'JEV_ADMIN_COPYEDIT');
		JToolBarHelper::deleteList('Delete Event and all repeats?', 'icalevent.delete');
		JToolBarHelper::spacer();
		//JToolBarHelper::help( 'screen.ical', true);

		JEventsHelper::addSubmenu();

		$showUnpublishedICS = false;

		$db = JFactory::getDbo();

		if (JVersion::isCompatible("3.0"))
		{

			JHtmlSidebar::setAction('index.php?option=com_jevents&task=icalevent.list');

			// get list of ics Files
			$query = "SELECT ics.ics_id as value, ics.label as text FROM #__jevents_icsfile as ics ";
			if (!$showUnpublishedICS)
			{
				$query .= " WHERE ics.state=1";
			}
			$query .= " ORDER BY ics.isdefault DESC, ics.label ASC";

			$db->setQuery($query);
			$icsfiles = $db->loadObjectList();
			$icsFile = intval(JFactory::getApplication()->getUserStateFromRequest("icsFile", "icsFile", 0));

			JHtmlSidebar::addFilter(
					JText::_('ALL_ICS_FILES'), 'icsFile', JHtml::_('select.options', $icsfiles, 'value', 'text', $icsFile)
			);

			$state = intval(JFactory::getApplication()->getUserStateFromRequest("stateIcalEvents", 'state', 0));
			$options = array();
			$options[] = JHTML::_('select.option', '1', JText::_('PUBLISHED'));
			$options[] = JHTML::_('select.option', '2', JText::_('UNPUBLISHED'));
			JHtmlSidebar::addFilter(
					JText::_('ALL_EVENTS'), 'state', JHtml::_('select.options', $options, 'value', 'text', $state)
			);

			// get list of creators
			$created_by = JFactory::getApplication()->getUserStateFromRequest("createdbyIcalEvents", 'created_by', "");
			$sql = "SELECT distinct u.id, u.* FROM #__jevents_vevent as jev LEFT JOIN #__users as u on u.id=jev.created_by order by u.name ";
			$db = & JFactory::getDBO();
			$db->setQuery($sql);
			$users = $db->loadObjectList();
			$userOptions = array();
			foreach ($users as $user)
			{
				if (!$user->id)
				{
					$user->id = 0;
				}
				$userOptions[] = JHTML::_('select.option', $user->id, $user->name . " ($user->username)");
			}

			JHtmlSidebar::addFilter(
					JText::_('JEV_EVENT_CREATOR'), 'created_by', JHtml::_('select.options', $userOptions, 'value', 'text', $created_by)
			);

			$this->sidebar = JHtmlSidebar::render();
		}
		else
		{

			// get list of ics Files
			$query = "SELECT ics.ics_id as value, ics.label as text FROM #__jevents_icsfile as ics ";
			if (!$showUnpublishedICS)
			{
				$query .= " WHERE ics.state=1";
			}
			$query .= " ORDER BY ics.isdefault DESC, ics.label ASC";

			$db->setQuery($query);
			$result = $db->loadObjectList();

			$icsFile = intval(JFactory::getApplication()->getUserStateFromRequest("icsFile", "icsFile", 0));
			$icsfiles[] = JHTML::_('select.option', '-1', JText::_('ALL_ICS_FILES'));
			$icsfiles = array_merge($icsfiles, $result);
			$icslist = JHTML::_('select.genericlist', $icsfiles, 'icsFile', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $icsFile);
			$this->assign('icsList', $icslist);

			$state = intval(JFactory::getApplication()->getUserStateFromRequest("stateIcalEvents", 'state', 0));
			$options = array();
			$options[] = JHTML::_('select.option', '0', JText::_('ALL_EVENTS'));
			$options[] = JHTML::_('select.option', '1', JText::_('PUBLISHED'));
			$options[] = JHTML::_('select.option', '2', JText::_('UNPUBLISHED'));

			$statelist = JHTML::_('select.genericlist', $options, 'state', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $state);
			$this->assign('statelist', $statelist);

			// get list of creators
			$created_by = JFactory::getApplication()->getUserStateFromRequest("createdbyIcalEvents", 'created_by', "");
			$sql = "SELECT distinct u.id, u.* FROM #__jevents_vevent as jev LEFT JOIN #__users as u on u.id=jev.created_by order by u.name ";
			$db = & JFactory::getDBO();
			$db->setQuery($sql);
			$users = $db->loadObjectList();
			$userOptions = array();
			$userOptions[] = JHTML::_('select.option', "", JText::_("JEV_EVENT_CREATOR"));
			foreach ($users as $user)
			{
				if (!$user->id)
				{
					$user->id = 0;
				}
				$userOptions[] = JHTML::_('select.option', $user->id, $user->name . " ($user->username)");
			}
			$userlist = JHTML::_('select.genericlist', $userOptions, 'created_by', 'class="inputbox" size="1"  onchange="document.adminForm.submit();"', 'value', 'text', $created_by);
			$this->assign('userlist', $userlist);
		}

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->get("section",0);

		JHTML::_('behavior.tooltip');

	}

	function edit($tpl = null)
	{
		$document = & JFactory::getDocument();
		include(JEV_ADMINLIBS . "editStrings.php");
		$document->addScriptDeclaration($editStrings);

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		JEVHelper::script('editical.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');

		if ($this->row->title() <= "")
		{
			$document->setTitle(JText::_('CREATE_ICAL_EVENT'));

			// Set toolbar items for the page
			JToolBarHelper::title(JText::_('CREATE_ICAL_EVENT'), 'jevents');
		}
		else
		{
			$document->setTitle(JText::_('EDIT_ICAL_EVENT'));

			// Set toolbar items for the page
			JToolBarHelper::title(JText::_('EDIT_ICAL_EVENT'), 'jevents');
		}

		$bar = & JToolBar::getInstance('toolbar');
		if ($this->id > 0)
		{
			if ($this->editCopy)
			{
				$this->toolbarConfirmButton("icalevent.apply", JText::_("save_copy_warning"), 'apply', 'apply', 'Jev_Apply', false);
				$this->toolbarConfirmButton("icalevent.save", JText::_("save_copy_warning"), 'save', 'save', 'Save', false);
				$this->toolbarConfirmButton("icalevent.savenew", JText::_("save_copy_warning"), 'save', 'save', 'JEV_Save_New', false);
			}
			else
			{
				$this->toolbarConfirmButton("icalevent.apply", JText::_("save_icalevent_warning"), 'apply', 'apply', 'JEV_Apply', false);
				$this->toolbarConfirmButton("icalevent.save", JText::_("save_icalevent_warning"), 'save', 'save', 'Save', false);
				$this->toolbarConfirmButton("icalevent.savenew", JText::_("save_icalevent_warning"), 'save', 'save', 'JEV_Save_New', false);
			}
		}
		else
		{
			if (JEVHelper::isEventEditor())
				JToolBarHelper::apply('icalevent.apply', "JEV_Apply");
			JToolBarHelper::save('icalevent.save');
			JToolBarHelper::save2new('icalevent.savenew', "JEV_Save_New");
		}

		JToolBarHelper::cancel('icalevent.list');
		//JToolBarHelper::help( 'screen.icalevent.edit', true);

		JHTML::_('behavior.tooltip');

		// TODO move this into JForm field type!
		$this->setCreatorLookup();

		if (JVersion::isCompatible("3.0"))
		{
			$this->setLayout("edit");
		}
		else
		{
			$this->setLayout("edit16");
		}

		$this->setupEditForm();

	}

	protected function setupEditForm () {
		
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		
		$this->editor = & JFactory::getEditor();
		if ($this->editor->get("_name") == "codemirror")
		{
			$this->editor = JFactory::getEditor("none");
			JFactory::getApplication()->enqueueMessage(JText::_("JEV_CODEMIRROR_NOT_COMPATIBLE_EDITOR", "WARNING"));
		}

		// clean any existing cache files
		$cache = & JFactory::getCache(JEV_COM_COMPONENT);
		$cache->clean(JEV_COM_COMPONENT);

		// Prepare the data
		// Experiment in the use of JForm
		JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/");
		$xpath = false;
		// leave form control blank since we want the fields as ev_id and not jform[ev_id]
		$this->form = JForm::getInstance("jevents.edit.icalevent", 'icalevent', array('control' => '', 'load_data' => false), false, $xpath);

		$rowdata = array();
		foreach ($this->row as $k => $v)
		{
			if (strpos($k, "_") === 0)
			{
				$newk = substr($k, 1);
				//$this->row->$newk = $v;
			}
			else
			{
				$newk = $k;
			}
			$rowdata[$newk] = $v;
		}
		$this->form->bind($rowdata);

		$this->catid = $this->row->catid();
		if ($this->catid == 0 && $this->defaultCat > 0)
		{
			$this->catid = $this->defaultCat;
		}
		if ($this->row->catids)
		{
			$this->catid = $this->row->catids;
		}

		if (!isset($this->ev_id))
		{
			$this->ev_id = $this->row->ev_id();
		}

		if ($this->editCopy)
		{
			$this->old_ev_id = $this->ev_id;
			$this->ev_id = 0;
			$this->repeatId = 0;
			$this->rp_id = 0;
			unset($this->row->_uid);
			$this->row->id(0);
		}

		$native = true;
		$thisCal = null;
		if ($this->row->icsid() > 0)
		{
			$thisCal = $this->dataModel->queryModel->getIcalByIcsid($this->row->icsid());
			if (isset($thisCal) && $thisCal->icaltype == 0)
			{
				// note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
				$native = false;
			}
			else if (isset($thisCal) && $thisCal->icaltype == 1)
			{
				// note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
				$native = false;
			}
		}

		// Event editing buttons
		$this->form->setValue("jevcontent", null, $this->row->content());
		if ($params->get('com_show_editor_buttons'))
		{
			$this->form->setFieldAttribute("jevcontent", "buttons", $params->get('com_editor_button_exceptions'));
		}
		else
		{
			$this->form->setFieldAttribute("jevcontent", "buttons", "false");
		}

		// Make data available to the form
		$this->form->jevdata["catid"]["dataModel"] = $this->dataModel;
		$this->form->jevdata["catid"]["excats"] = $this->excats;
		$this->form->jevdata["catid"]["with_unpublished_cat"] = $this->with_unpublished_cat;
		$this->form->jevdata["catid"]["repeatId"] = $this->repeatId;
		$this->form->setValue("catid", null, $this->catid);

		$this->form->jevdata["creator"]["users"] = $this->users;

		$this->form->jevdata["ics_id"]["clist"] = $this->clist;
		$this->form->jevdata["ics_id"]["clistChoice"] = $this->clistChoice;
		$this->form->jevdata["ics_id"]["thisCal"] = $thisCal;
		$this->form->jevdata["ics_id"]["native"] = $native;
		$this->form->jevdata["ics_id"]["nativeCals"] = $this->nativeCals;

		$this->form->jevdata["lockevent"]["offerlock"] = isset($this->offerlock) ? 1 : 0;

		$this->form->jevdata["access"]["glist"] = isset($this->glist) ? $this->glist : false;

		$this->form->jevdata["state"]["ev_id"] = $this->ev_id;

		$this->form->jevdata["location"]["event"] = $this->row;

		// replacement values
		$this->searchtags = array();
		$this->replacetags = array();
		$this->blanktags = array();

		$fields = $this->form->getFieldSet();
		foreach ($fields as $key => $field)
		{
			if ($this->form->getFieldAttribute($key, "layoutfield"))
			{
				$this->searchtags[] = '{{' . $this->form->getFieldAttribute($key, "layoutfield") . "_LBL}}";
				$this->replacetags[] = $field->label;
				$this->blanktags[] = "";

				$this->searchtags[] = '{{' . $this->form->getFieldAttribute($key, "layoutfield") . "}}";
				$this->replacetags[] = $field->input;
				$this->blanktags[] = "";
				
			}
		}

		// Plugins CAN BE LAYERED IN HERE - In Joomla 3.0 we need to call it earlier to get the tab titles			
		// append array to extratabs keys content, title, paneid
		$this->extraTabs = array();
		$dispatcher = & JDispatcher::getInstance();
		$dispatcher->trigger('onEventEdit', array(&$this->extraTabs, &$this->row, &$params), true);

		foreach ($this->extraTabs as $extraTab)
		{
			$this->searchtags[] = "{{" . str_replace(" ", "_", strtoupper($extraTab['title'])) . "}}";
			$this->replacetags[] = $extraTab['content'];
			$this->blanktags[] = "";
		}


		// load any custom fields
		$this->customfields = array();
		$res = $dispatcher->trigger('onEditCustom', array(&$this->row, &$this->customfields));

		ob_start();
		foreach ($this->customfields as $key => $val)
		{
			$this->searchtags[] = '{{' . $key . '}}';
			$this->replacetags[] = $this->customfields[$key]["input"];
			$this->blanktags[] = "";
			$this->searchtags[] = '{{' . $key . '_lbl}}';
			$this->replacetags[] = $this->customfields[$key]["label"];
			$this->blanktags[] = "";

			if (JVersion::isCompatible("3.0"))
			{
				?>
				<div class="control-group jevplugin_<?php echo $key; ?>">
					<label class="control-label "><?php echo $this->customfields[$key]["label"]; ?></label>
					<div class="controls" >
				<?php echo $this->customfields[$key]["input"]; ?>
					</div>
				</div>
				<?php
			}
			else
			{
				?>		
				<tr class="jevplugin_<?php echo $key; ?>">
					<td valign="top"  width="130" align="left">
						<?php
						echo $this->customfields[$key]["label"];
						?>
					</td>
					<td colspan="3">
						<?php
						echo $this->customfields[$key]["input"];
						?>
					</td>
				</tr>
				<?php
			}
		}
		$this->searchtags[] = "{{CUSTOMFIELDS}}";
		$output = ob_get_clean();
		$this->replacetags[] = $output;
		$this->blanktags[] = "";

	}
	
	function csvimport($tpl = null)
	{

		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('CSV_IMPORT'));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('CSV_IMPORT'), 'jevents');

		JToolBarHelper::cancel('icalevent.list');

		JEventsHelper::addSubmenu();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->get("section",0);

		JHTML::_('behavior.tooltip');

	}

	protected function setCreatorLookup()
	{
		// If user is jevents can deleteall or has backend access then allow them to specify the creator
		$jevuser = JEVHelper::getAuthorisedUser();
		$user = JFactory::getUser();
		//$access = JAccess::check($user->id, "core.deleteall", "com_jevents");
		$access = $user->authorise('core.admin', 'com_jevents');

		$db = JFactory::getDBO();
		if (($jevuser && $jevuser->candeleteall) || $access)
		{
			$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 0);
			// if authorised only then load from database
			if ($authorisedonly)
			{
				$sql = "SELECT tl.*, ju.*  FROM #__jev_users AS tl ";
				$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
				$sql .= " WHERE tl.cancreate=1";
				$sql .= " ORDER BY ju.name ASC";
				$db->setQuery($sql);
				$users = $db->loadObjectList();
			}
			else
			{
				$rules = JAccess::getAssetRules("com_jevents", true);
				$creatorgroups = $rules->getData();
				// need to merge the arrays because of stupid way Joomla checks super user permissions
				//$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups["core.create"]->getData());
				// use union orf arrays sincee getData no longer has string keys in the resultant array
				//$creatorgroups = $creatorgroups["core.admin"]->getData()+ $creatorgroups["core.create"]->getData();
				// use union orf arrays sincee getData no longer has string keys in the resultant array
				$creatorgroupsdata = $creatorgroups["core.admin"]->getData();
				// take the higher permission setting
				foreach ($creatorgroups["core.create"]->getData() as $creatorgroup => $permission)
				{
					if ($permission)
					{
						$creatorgroupsdata[$creatorgroup] = $permission;
					}
				}

				$users = array(0);
				foreach ($creatorgroupsdata as $creatorgroup => $permission)
				{
					if ($permission == 1)
					{
						$users = array_merge(JAccess::getUsersByGroup($creatorgroup, true), $users);
					}
				}
				$sql = "SELECT * FROM #__users where id IN (" . implode(",", array_values($users)) . ") ORDER BY name asc";
				$db->setQuery($sql);
				$users = $db->loadObjectList();
			}

			$userOptions[] = JHTML::_('select.option', '-1', JText::_('SELECT_USER'));
			foreach ($users as $user)
			{
				$userOptions[] = JHTML::_('select.option', $user->id, $user->name . " ( " . $user->username . " )");
			}
			$creator = $this->row->created_by() > 0 ? $this->row->created_by() : (isset($jevuser) ? $jevuser->user_id : 0);
			$userlist = JHTML::_('select.genericlist', $userOptions, 'jev_creatorid', 'class="inputbox" size="1" ', 'value', 'text', $creator);

			$this->assignRef("users", $userlist);
		}

	}

	function toolbarConfirmButton($task = '', $msg = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		$bar = & JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jevconfirm', $msg, $icon, $alt, $task, $listSelect, false, "document.adminForm.updaterepeats.value");

	}

}
