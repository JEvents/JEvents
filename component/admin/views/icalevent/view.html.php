<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3401 2012-03-22 15:35:38Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd
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

		// Get data from the model
		$model = $this->getModel();

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('ICAL_EVENTS'));

		// Set toolbar items for the page
		JToolbarHelper::title(JText::_('ICAL_EVENTS'), 'jevents');

		JToolbarHelper::addNew('icalevent.edit');
		JToolbarHelper::editList('icalevent.edit');
		JToolbarHelper::publishList('icalevent.publish');
		JToolbarHelper::unpublishList('icalevent.unpublish');
		JToolbarHelper::custom('icalevent.editcopy', 'copy.png', 'copy.png', 'JEV_ADMIN_COPYEDIT');
		$state = (int)JFactory::getApplication()->getUserStateFromRequest("stateIcalEvents", 'state', 0);
		if ($state==-1){
			JToolbarHelper::deleteList("JEV_EMPTY_TRASH_DELETE_EVENT_AND_ALL_REPEATS", 'icalevent.emptytrash',"JTOOLBAR_EMPTY_TRASH");
		}
		else {
			JToolbarHelper::trash('icalevent.delete');
		}
		JToolbarHelper::spacer();
		//JToolbarHelper::help( 'screen.ical', true);

		JEventsHelper::addSubmenu();

		$showUnpublishedICS = false;

		$db = JFactory::getDbo();

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
		$icsFile = (int) JFactory::getApplication()->getUserStateFromRequest("icsFile", "icsFile", 0);

		JHtmlSidebar::addFilter(
				JText::_('ALL_ICS_FILES'), 'icsFile', JHtml::_('select.options', $icsfiles, 'value', 'text', $icsFile)
		);

		$state = (int) JFactory::getApplication()->getUserStateFromRequest("stateIcalEvents", 'state', 3);
		$options = array();
		$options[] = JHTML::_('select.option', '3', JText::_('JOPTION_SELECT_PUBLISHED'));
		$options[] = JHTML::_('select.option', '1', JText::_('PUBLISHED'));
		$options[] = JHTML::_('select.option', '2', JText::_('UNPUBLISHED'));
		$options[] = JHTML::_('select.option', '-1', JText::_('JTRASH'));
		JHtmlSidebar::addFilter(
				JText::_('ALL_EVENTS'), 'state', JHtml::_('select.options', $options, 'value', 'text', $state)
		);

		// get list of creators
		$created_by = JFactory::getApplication()->getUserStateFromRequest("createdbyIcalEvents", 'created_by', "");
		$sql = "SELECT distinct u.id, u.name, u.username FROM #__jevents_vevent as jev LEFT JOIN #__users as u on u.id=jev.created_by ORDER BY u.name ";
		$db = JFactory::getDbo();
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

		JHTML::_('behavior.tooltip');

		// Only offer translations in latest version of Joomla
		if (JevJoomlaVersion::isCompatible("3.4")){
			$this->languages = $this->get('Languages');
		}
		else {
			$this->languages = null;
		}

	}

	public function edit($tpl = null)
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		$document = JFactory::getDocument();
		//Define to keep editor happy that it is defined.
		$editStrings = "";
		include(JEV_ADMINLIBS . "editStrings.php");
		$document->addScriptDeclaration($editStrings);

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		JEVHelper::script('editicalJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
        JEVHelper::script('JevStdRequiredFieldsJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');

		if ($this->row->title() === '')
		{
			$document->setTitle(JText::_('CREATE_ICAL_EVENT'));
			// Set toolbar items for the page
			JToolbarHelper::title(JText::_('CREATE_ICAL_EVENT'), 'jevents');

			// Set default noendtime
			$this->row->noendtime((int) $params->get('default_noendtime', '0'));
		}
		else
		{
			$document->setTitle(JText::_('EDIT_ICAL_EVENT'));

			// Set toolbar items for the page
			JToolbarHelper::title(JText::_('EDIT_ICAL_EVENT'), 'jevents');
		}

		$bar =  JToolBar::getInstance('toolbar');
		if ($this->id > 0)
		{
			if ($this->editCopy)
			{

				if (JEVHelper::isEventEditor() || JEVHelper::canEditEvent($this->row))
				{
					$this->toolbarConfirmButton("icalevent.apply", JText::_("JEV_SAVE_COPY_WARNING"), 'apply', 'apply', 'JEV_SAVE', false);
				}
				$this->toolbarConfirmButton("icalevent.save", JText::_("JEV_SAVE_COPY_WARNING"), 'save', 'save', 'JEV_SAVE_CLOSE', false);
				$this->toolbarConfirmButton("icalevent.savenew", JText::_("JEV_SAVE_COPY_WARNING"), 'save', 'save', 'JEV_SAVE_NEW', false);
			}
			else
			{
				if (JEVHelper::isEventEditor() || JEVHelper::canEditEvent($this->row))
				{
					$this->toolbarConfirmButton("icalevent.apply", JText::_("JEV_SAVE_ICALEVENT_WARNING"), 'apply', 'apply', 'JEV_SAVE', false);
				}
				$this->toolbarConfirmButton("icalevent.save", JText::_("JEV_SAVE_ICALEVENT_WARNING"), 'save', 'save', 'JEV_SAVE_CLOSE', false);
				$this->toolbarConfirmButton("icalevent.savenew", JText::_("JEV_SAVE_COPY_WARNING"), 'save', 'save', 'JEV_SAVE_NEW', false);

			}
		}
		else
		{
			$canEditOwn = false;
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if (!$params->get("authorisedonly", 0))
			{
				$juser = JFactory::getUser();
				$canEditOwn = $juser->authorise('core.edit.own', 'com_jevents');
			}
			if (JEVHelper::isEventEditor() || $canEditOwn)
			{
				$this->toolbarConfirmButton("icalevent.apply", JText::_("JEV_SAVE_ICALEVENT_WARNING"), 'apply', 'apply', 'JEV_SAVE', false);
			}
			$this->toolbarConfirmButton("icalevent.save", JText::_("JEV_SAVE_ICALEVENT_WARNING"), 'save', 'save', 'JEV_SAVE_CLOSE', false);
			$this->toolbarConfirmButton("icalevent.savenew", JText::_("JEV_SAVE_COPY_WARNING"), 'save', 'save', 'JEV_SAVE_NEW', false);
		}



		JToolbarHelper::cancel('icalevent.list');
		//JToolbarHelper::help( 'screen.icalevent.edit', true);

		// TODO move this into JForm field type!
		$this->setCreatorLookup();

		// load Joomla javascript classes
		JHTML::_('behavior.core');
		$this->setLayout("edit");

		$this->setupEditForm();

	}

	function translate($tpl = null)
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		$this->editor =  JFactory::getEditor();
		if ($this->editor->get("_name") == "codemirror")
		{
			$this->editor = JFactory::getEditor("none");
			JFactory::getApplication()->enqueueMessage(JText::_("JEV_CODEMIRROR_NOT_COMPATIBLE_EDITOR", "WARNING"));
		}

		// Get the form && data
		$this->form = $this->get('TranslateForm');
		$this->original = $this->get("Original");
		$this->translation = $this->get("Translation");
		$lang = JRequest::getString("lang", "");

		$this->form->bind($this->original);
		$this->form->bind($this->translation);

		$this->form->setValue("trans_language",null,  $lang);
		$this->form->setValue("language",null,  $lang);
		$this->form->setValue("trans_evdet_id", null, $this->original["evdet_id"]);
		$this->form->setValue("ev_id", null, JRequest::getInt("ev_id", 0));

		// Event editing buttons
		if ($params->get('com_show_editor_buttons'))
		{
			$this->form->setFieldAttribute("trans_description", "hide", $params->get('com_editor_button_exceptions'));
		}
		else
		{
			$this->form->setFieldAttribute("trans_description", "buttons", "false");
		}
		$this->form->setFieldAttribute("description", "buttons", "false");

                
                $dispatcher = JEventDispatcher::getInstance();
                $dispatcher->trigger('onTranslateEvent', array(&$this->row, $lang), true);
                                
		$this->addTranslationToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addTranslationToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolbarHelper::save('icalevent.savetranslation');
		JToolbarHelper::cancel('icalevent.close');

		$bar =  JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('confirm', JText::_("JEV_DELETE_TRANSLATION_WARNING"),  'trash',  'JEV_DELETE', "icalevent.deletetranslation", false);
		
	}
	
	function csvimport($tpl = null)
	{

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('CSV_IMPORT'));

		// Set toolbar items for the page
		JToolbarHelper::title(JText::_('CSV_IMPORT'), 'jevents');

		JToolbarHelper::cancel('icalevent.list');

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
		$access = $user->authorise('core.admin', 'com_jevents') || $user->authorise('core.deleteall', 'com_jevents');

		$db = JFactory::getDBO();
		if (($jevuser && $jevuser->candeleteall) || $access)
		{
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
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
                                if (count($users)>200){
                                    return null;
                                }
				$sql = "SELECT count(id) FROM #__users where id IN (" . implode(",", array_values($users)) . ") and block=0 ORDER BY name asc";
				$db->setQuery($sql);
				$userCount = $db->loadResult();

				if ($userCount<=200) {                                
                                    $sql = "SELECT * FROM #__users where id IN (" . implode(",", array_values($users)) . ") and block=0 ORDER BY name asc";
                                    $db->setQuery($sql);
                                    $users = $db->loadObjectList();
                                }
                                else {
                                    return null;
                                }
                                    
			}

			// get list of creators - if fewer than 200
			if (count($users)>200) {
				return null;
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

	function toolbarButton($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true) {
		$bar = JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jev', $icon, $alt, $task, $listSelect);
	}

	function toolbarConfirmButton($task = '', $msg = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		$bar =  JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jevconfirm', $msg, $icon, $alt, $task, $listSelect, false, "document.adminForm.updaterepeats.value");

	}

	protected function translationLinks ($row) {
		if ($this->languages)
		{
			$translations = array();
			JevHtmlBootstrap::modal();
			JEVHelper::script('editpopupJQ.js','components/'.JEV_COM_COMPONENT.'/assets/js/');

			// Any existing translations ?  Do NOT use isset here since there is a magic __get that will return false if its not defined
			if ($row->evdet_id) {
				$db = JFactory::getDbo();
				$db->setQuery("SELECT language FROM #__jevents_translation where evdet_id= " . $row->evdet_id);
				$translations = $db->loadColumn();
			}
			// test styling for existing translation
			//$translations[] = "cy-GB";
			?>
			<ul class="item-associations">
			<?php foreach ($this->languages as $id => $item) :

				$text = strtoupper($item->sef);
				$url = JRoute::_('index.php?option=com_jevents&task=icalevent.translate&evdet_id='.$row->evdet_id.'&ev_id='.$row->ev_id.'&pop=1&tmpl=component&lang=' . $item->lang_code);
				$img = JHtml::_('image', 'mod_languages/' . $item->image . '.gif',
						$item->title,
						array('title' => $item->title),
						true
					);
				$url  = "javascript:jevEditTranslation('".$url ."', '". JText::sprintf("JEV_TRANSLATE_EVENT_TO" ,  addslashes($item->title),  array('jsSafe'=>true) ) . "'); ";
				$tooltipParts = array( 	$img,  $item->title);
				$item->link = JHtml::_('tooltip', implode(' ', $tooltipParts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->sef .( in_array($item->lang_code, $translations)?" hastranslation":"" ));
				?>
				<li>
				<?php
				echo $item->link;
				?>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php
		}
	}

}
