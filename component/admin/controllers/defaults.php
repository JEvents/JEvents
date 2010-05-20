<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: cpanel.php 1429 2009-04-28 16:45:57Z geraint $
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
		$this->registerTask( 'list',  'overview' );
		$this->registerTask( 'new',  'edit' );
		$this->registerDefaultTask("overview");

		// Make sure DB is up to date
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__jev_defaults");
		$defaults =$db->loadObjectList("name");
		if (!isset($defaults['icalevent.detail_body'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.detail_body',
						title=".$db->Quote(JText::_("JEV EVENT DETAIL PAGE")).",
						subject='',
						value='',
						state=0");
			$db->query();
		}
		
		if (!isset($defaults['icalevent.list_row'])){
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.list_row',
						title=".$db->Quote(JText::_("JEV EVENT LIST ROW")).",
						subject='',
						value='',
						state=0");
			$db->query();
		}

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
				if ($model->store(JRequest::get("post",JREQUEST_ALLOWHTML))){
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
