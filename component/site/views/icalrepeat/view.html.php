<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3012 2011-11-16 10:29:35Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\String\StringHelper;

/**
 * HTML View class for the component frontend
 *
 * @static
 */
include_once(JEV_ADMINPATH."/views/icalrepeat/view.html.php");

class IcalrepeatViewIcalrepeat extends AdminIcalrepeatViewIcalrepeat
{
	var $jevlayout = null;
	
	function __construct($config = array()){
		include_once(JPATH_ADMINISTRATOR.'/'."includes".'/'."toolbar.php");
		parent::__construct($config);	
		
		// used only for helper functions
		$this->jevlayout="default";	
		$this->addHelperPath(realpath(dirname(__FILE__)."/../default/helpers"));		
		$this->addHelperPath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'."helpers");		
	}	
	
	function edit($tpl = null)
	{
		$document = JFactory::getDocument();
		// Set editstrings var just incase and to avoid IDE reporting not set.
		$editStrings = "";
		include(JEV_ADMINLIBS."/editStrings.php");		
		$document->addScriptDeclaration($editStrings);

		JEVHelper::script('editicalJQ.js',  'components/'.JEV_COM_COMPONENT.'/assets/js/');  
		JEVHelper::script('view_detailJQ.js', 'components/'.JEV_COM_COMPONENT.'/assets/js/');
                  JEVHelper::script('JevStdRequiredFieldsJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		
		$document->setTitle(JText::_( 'EDIT_ICAL_REPEAT' ));
		
		// Set toolbar items for the page
		JToolbarHelper::title( JText::_( 'EDIT_ICAL_REPEAT' ), 'jevents' );
	
		$bar =  JToolBar::getInstance('toolbar');
		if (JEVHelper::isEventEditor()) {
			JToolbarHelper::apply('icalrepeat.apply', "JEV_SAVE");
		}
		JToolbarHelper::apply('icalrepeat.save', "JEV_SAVE_CLOSE");

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup",0) && JEVHelper::isEventCreator())
		{
			$document->addStyleDeclaration("div#toolbar-box{margin:10px 10px 0px 10px;} div#jevents {margin:0px 10px 10px 10px;} ");
			$this->toolbarButton("icalevent.close", 'cancel', 'cancel', 'JEV_SUBMITCANCEL', false);
			JRequest::setVar('tmpl', 'component'); //force the component template
		}
		else
		{
			$this->toolbarButton("icalevent.detail", 'cancel', 'cancel', 'JEV_SUBMITCANCEL', false);
		}
		
		//JToolbarHelper::help( 'screen.icalrepeat.edit', true);
	
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		JHTML::_('behavior.tooltip');

		
		$this->_adminStart();		

		// load Joomla javascript classes
		JHTML::_('behavior.core');
		$this->setLayout("edit");

		$this->setupEditForm();

		JEVHelper::componentStylesheet($this, "editextra.css");
		jimport('joomla.filesystem.file');

		// Lets check if we have editted before! if not... rename the custom file.
		if (JFile::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
		{
			// It is definitely now created, lets load it!
			JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		}

		parent::displaytemplate($tpl);
		$this->_adminEnd();
	}	
	
	function _adminStart(){
		
		$dispatcher	= JEventDispatcher::getInstance();
		list($this->year,$this->month,$this->day) = JEVHelper::getYMD();
		$this->Itemid	= JEVHelper::getItemid();
		$this->datamodel =new JEventsDataModel();
		$dispatcher->trigger( 'onJEventsHeader', array($this));

?>
	<div style="clear:both"
				<?php
				$mainframe = JFactory::getApplication();
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				echo (!JFactory::getApplication()->isAdmin() && $params->get("darktemplate", 0)) ? "class='jeventsdark'" : "class='jeventslight'";
				?>>
		<div id="toolbar-box" >
<?php
		$bar =  JToolBar::getInstance('toolbar');
		$barhtml = $bar->render();
		//$barhtml = str_replace('href="#"','href="javascript void();"',$barhtml);
		//$barhtml = str_replace('submitbutton','return submitbutton',$barhtml);
		echo $barhtml;
		
		if (JevJoomlaVersion::isCompatible("3.0"))
		{
			// JFactory::getApplication()->JComponentTitle;
			$title ="";
		}
		else
		{
			$title = JFactory::getApplication()->get('JComponentTitle');
		}
		echo $title;
?>
		</div>
<?php		
		$dispatcher	= JEventDispatcher::getInstance();
		$dispatcher->trigger( 'onJEventsFooter', array($this));


	}

	function _adminEnd(){
?>
	</div>
<?php			
	}
	
	function _xadminStart(){
		
?>
	<div id="content-box" style="clear:both">
		<div class="border">
			<div class="padding">
				<div id="toolbar-box" >
   					<div class="t">
						<div class="t">
							<div class="t"></div>
						</div>
					</div>
					<div class="m">
<?php
		$bar =  JToolBar::getInstance('toolbar');
		$barhtml = $bar->render();
		//$barhtml = str_replace('href="#"','href="javascript void();"',$barhtml);
		//$barhtml = str_replace('submitbutton','return submitbutton',$barhtml);
		echo $barhtml;
		
		$title = JFactory::getApplication()->get('JComponentTitle');
		echo $title;
?>
					<div class="clr"></div>
					</div>
					<div class="b">
						<div class="b">
							<div class="b"></div>	
						</div>
					</div>
  				</div>
				<div id="toolbar-box2">
   					<div class="t">
						<div class="t">
							<div class="t"></div>
						</div>
					</div>
					<div class="m">
<?php			
	}

	function _xadminEnd(){
?>
					<div class="clr"></div>
					</div>
					<div class="b">
						<div class="b">
							<div class="b"></div>	
						</div>
					</div>
  				</div>
			</div>
		</div>
	</div>
<?php			
	}

	// This handles all methods where the view is passed as the first argument
	function __call($name, $arguments){
		if (strpos($name,"_")===0){
			$name="ViewHelper".ucfirst(JString::substr($name,1));
		}
		$helper = ucfirst($this->jevlayout).ucfirst($name);
		if (!$this->loadHelper($helper)){
			$helper = "Default".ucfirst($name);
			if (!$this->loadHelper($helper)){
				return;
			}
		}
		$args = array_unshift($arguments,$this);
		if (class_exists($helper)){
			if (class_exists("ReflectionClass") ){
				$reflectionObj = new ReflectionClass($helper);
				if (method_exists($reflectionObj,"newInstanceArgs")){
					$var = $reflectionObj->newInstanceArgs($arguments);	
				}
				else {
					$var = $this->CreateClass($helper,$arguments);
				}
			}
			else {
				$var = $this->CreateClass($helper,$arguments);
			}
			return;
		}
		else if (is_callable($helper)){
			return call_user_func_array($helper,$arguments);
		}
	}
	
	
	function toolbarButton($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		$bar =  JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jev', $icon, $alt, $task, $listSelect);

	}

	function toolbarLinkButton($task = '', $icon = '', $iconOver = '', $alt = '')
	{
		$bar =  JToolBar::getInstance('toolbar');

		// Add a standard button
		$bar->appendButton('Jevlink', $icon, $alt, $task, false);

	}
	
	protected function CreateClass($className, $params) {
		switch (count($params)) {
			case 0:
				return new $className();
				break;
			case 1:
				return new $className($params[0]);
				break;
			case 2:
				return new $className($params[0], $params[1]);
				break;
			case 3:
				return new $className($params[0], $params[1], $params[2]);
				break;
			case 4:
				return new $className($params[0], $params[1], $params[2], $params[3]);
				break;
			case 5:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4]);
				break;
			case 6:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
				break;
			case 7:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]);
				break;
			case 8:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7]);
				break;
			case 9:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8]);
				break;
			case 10:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9]);
				break;
			default:
				echo "Too many arguments";
				return null;
				break;
		}
	}

	function loadHelper( $file = null)
	{
		if (function_exists($file) || class_exists($file)) return true;
		
		// load the template script
		jimport('joomla.filesystem.path');
		$helper = JPath::find($this->_path['helper'], $this->_createFileName('helper', array('name' => $file)));

		if ($helper != false)
		{
			// include the requested template filename in the local scope
			include_once $helper;
		}
		return $helper;
	}

}
