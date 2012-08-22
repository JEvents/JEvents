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

jimport('joomla.application.component.controller');

class AdminDefaultsController extends JController {
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
		$this->registerDefaultTask("overview");

		// Make sure DB is up to date
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__jev_defaults");
		$defaults =$db->loadObjectList("name");
		if (!isset($defaults['icalevent.detail_body'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.detail_body',
						title=".$db->Quote(JText::_("JEV_EVENT_DETAIL_PAGE")).",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote(JText::_("JEV_EVENT_DETAIL_PAGE"))." WHERE name='icalevent.detail_body'");
			$db->query();
		}
		
		if (!isset($defaults['icalevent.list_row'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.list_row',
						title=".$db->Quote(JText::_("JEV_EVENT_LIST_ROW")).",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote(JText::_("JEV_EVENT_LIST_ROW"))." WHERE name='icalevent.list_row'");
			$db->query();
		}
		
		if (!isset($defaults['month.calendar_cell'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='month.calendar_cell',
						title=".$db->Quote(JText::_("JEV_EVENT_MONTH_CALENDAR_CELL")).",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote(JText::_("JEV_EVENT_MONTH_CALENDAR_CELL"))." WHERE name='month.calendar_cell'");
			$db->query();
		}
		
		if (!isset($defaults['month.calendar_tip'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='month.calendar_tip',
						title=".$db->Quote(JText::_("JEV_EVENT_MONTH_CALENDAR_TIP")).",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		else {
			$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote(JText::_("JEV_EVENT_MONTH_CALENDAR_TIP"))." WHERE name='month.calendar_tip'");
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
		// get the view
		$this->view = & $this->getView("defaults","html");

		// Set the layout
		$this->view->setLayout('overview');

		// Get/Create the model
		if ($model = & $this->getModel("defaults", "defaultsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->overview();
	}

	function edit(){
		// get the view
		$this->view = & $this->getView("defaults","html");

		// Set the layout
		$this->view->setLayout('edit');

		// Get/Create the model
		if ($model = & $this->getModel("default", "defaultsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->edit();

	} // editdefaults()

	function cancel(){
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
		$sql = "UPDATE #__jev_defaults SET state=0 where name=".$db->Quote($name);
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
		$sql = "UPDATE #__jev_defaults SET state=1 where name=".$db->Quote($name);
		$db->setQuery($sql);
		$db->query();

		$this->setRedirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=defaults.overview",false) );
	}


	/**
	* Saves the Session Record
	*/
	function save() {


		$name = JRequest::getString('name', "");
		if ($name !=""){

			// Get/Create the model
			if ($model = & $this->getModel("default", "defaultsModel")) {
				if ($model->store(JRequest::get("post",JREQUEST_ALLOWRAW))){
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

}
