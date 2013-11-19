<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: defaults.php 3308 2012-02-28 10:13:19Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controllerform');

class AdminDefaultsController extends JControllerForm {
	/**
	 * Controler for the Control Panel
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		
		if (!JEVHelper::isAdminUser())
		{
			JFactory::getApplication()->redirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel", "Not Authorised - must be admin");
			return;
		}
		
		$this->registerTask( 'list',  'overview' );
		$this->registerTask( 'new',  'edit' );
		$this->registerTask( 'save',  'apply' );
		$this->registerDefaultTask("overview");

		// Make sure DB is up to date
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__jev_defaults");
		$defaults =$db->loadObjectList("name");
		if (!isset($defaults['icalevent.detail_body'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.detail_body',
						title=".$db->Quote("JEV_EVENT_DETAIL_PAGE").",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote("JEV_EVENT_DETAIL_PAGE")." WHERE name='icalevent.detail_body'");
			$db->query();
		}

		if (!isset($defaults['icalevent.edit_page'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.edit_page',
						title=".$db->Quote("JEV_EVENT_EDIT_PAGE").",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote("JEV_EVENT_EDIT_PAGE")." WHERE name='icalevent.edit_page'");
			$db->query();
		}
		
		if (!isset($defaults['icalevent.list_row'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.list_row',
						title=".$db->Quote("JEV_EVENT_LIST_ROW").",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote("JEV_EVENT_LIST_ROW")." WHERE name='icalevent.list_row'");
			$db->query();
		}
		
		if (!isset($defaults['month.calendar_cell'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='month.calendar_cell',
						title=".$db->Quote("JEV_EVENT_MONTH_CALENDAR_CELL").",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote("JEV_EVENT_MONTH_CALENDAR_CELL")." WHERE name='month.calendar_cell'");
			$db->query();
		}
		
		if (!isset($defaults['month.calendar_tip'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='month.calendar_tip',
						title=".$db->Quote("JEV_EVENT_MONTH_CALENDAR_TIP").",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote("JEV_EVENT_MONTH_CALENDAR_TIP")." WHERE name='month.calendar_tip'");
			$db->query();
		}
		
/*
 * Edit Page config must wait for plugins to be updated!
		if (!isset($defaults['icalevent.edit_page'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.edit_page',
						title=".$db->Quote(JText::_("JEV_EVENT_EDIT_PAGE")).",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote(JText::_("JEV_EVENT_EDIT_PAGE"))." WHERE name='icalevent.edit_page'");
			$db->query();
		}
*/

	}

	/**
	 * List Ical Events
	 *
	 */
	function overview( )
	{
		$this->populateLanguages();
		
		// get the view
		$this->view = $this->getView("defaults","html");

		// Set the layout
		$this->view->setLayout('overview');

		// Get/Create the model
		if ($model =  $this->getModel("defaults", "defaultsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->overview();
	}

	function edit($key = NULL, $urlVar = NULL){
		// get the view
		$this->view = $this->getView("defaults","html");

		// Set the layout
		$this->view->setLayout('edit');

		// Get/Create the model
		if ($model =  $this->getModel("default", "defaultsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->edit();

	} // editdefaults()

	function cancel($key = NULL){
		$this->setRedirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=defaults.overview",false) );
	}

	function unpublish(){
		$db= JFactory::getDBO();
		$cid = JRequest::getVar("cid",array());
		if (count($cid)!=1) {
			$this->setRedirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=defaults.overview",false) );
			return;
		}
		$name = $cid[0];
		$sql = "UPDATE #__jev_defaults SET state=0 where id=".$db->Quote($name);
		$db->setQuery($sql);
		$db->query();

		$this->setRedirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=defaults.overview",false) );
	}

	function publish(){
		$db= JFactory::getDBO();
		$cid = JRequest::getVar("cid",array());
		if (count($cid)!=1) {
			$this->setRedirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=defaults.overview",false) );
			return;
		}
		$name = $cid[0];
		$sql = "UPDATE #__jev_defaults SET state=1 where id=".$db->Quote($name);
		$db->setQuery($sql);
		$db->query();

		$this->setRedirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=defaults.overview",false) );
	}


	/**
	* Saves the Session Record
	*/
	function save($key = NULL, $urlVar = NULL) {


		$id = JRequest::getInt("id",0);
		if ($id >0 ){

			// Get/Create the model
			if ($model =  $this->getModel("default", "defaultsModel")) {
				if ($model->store(JRequest::get("post",JREQUEST_ALLOWRAW))){
					if (JRequest::getCmd("task")=="defaults.apply"){
						$this->setRedirect("index.php?option=".JEV_COM_COMPONENT."&task=defaults.edit&id=$id",JText::_("JEV_TEMPLATE_SAVED"));
						return;
					}					
					$this->setRedirect("index.php?option=".JEV_COM_COMPONENT."&task=defaults.overview",JText::_("JEV_TEMPLATE_SAVED"));
					return;
				}
				else {
					echo "<script> alert('".$model->getErrorMessage()."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}


	}

	private function populateLanguages() {
		$db = JFactory::getDBO();
			
		// get the list of languages first 
		$query	= $db->getQuery(true);
		$query->select("l.*");
		$query->from("#__languages as l");
		$query->where('l.lang_code <> "xx-XX"');
		$query->order("l.lang_code asc");
		
		$db->setQuery($query);
		$languages  = $db->loadObjectList('lang_code');
		
		// remove ones where the language is no longer installed
		$query	= $db->getQuery(true);
		$langcodes = array();
		$langcodes[] = $db->quote("*");
		foreach ($languages as $lang){
			$langcodes[] = $db->quote($lang->lang_code);
		}
		$langcodes =  implode(",",$langcodes);
		$query->delete('#__jev_defaults')->where("language NOT IN ($langcodes)");
		$db->setQuery($query);
		$db->query();
		
		// not needed if only one language
		if (count($languages )==1){
			return;
		}
		$query	= $db->getQuery(true);
		$query->select("def.*");
		$query->from("#__jev_defaults as def");
			
		$query->where('def.language = "*"');
		
		$query->order("def.title asc");
		$db->setQuery($query);
		$allLanguageTitles = $db->loadObjectList();
		
		$query	= $db->getQuery(true);
		$query->select("def.*");
		$query->from("#__jev_defaults as def");
			
		$query->where('def.language <> "*"');
		
		$query->order("def.title, language asc");
		
		$db->setQuery($query);
		$specificLanguageTitles = $db->loadObjectList();

		$missingDefaults = array();
		foreach ($allLanguageTitles as $title){
			foreach ($languages  as $lang_code=>$lang){
				$matched = false;
				foreach ($specificLanguageTitles as $title){
					if ($title == $title ){
						$matched = true;
						break;
					}
				}
				if (!$matched){
					$missingDefaults[] = array("lang_code"=>$lang_code, "title"=>$title);
				}
			}
		}
		
		if (count ($missingDefaults)>0){
			$query	= $db->getQuery(true);
			$query->insert("#__jev_defaults")->columns("title, name, subject,value,state,params,language");
			foreach ($missingDefaults as $md){
				$values = array($db->quote($md["title"]->title), $db->quote($md["title"]->name), $db->quote($md["title"]->subject), $db->quote($md["title"]->value), 0, $db->quote($md["title"]->params), $db->quote($md["lang_code"]));
				$query->values(implode(",",$values));
			}
			$db->setQuery($query);
			$db->query();
		}
		
		//echo $db->getgetErrorMsg();
	
	}
}
