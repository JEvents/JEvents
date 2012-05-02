<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 3012 2011-11-16 10:29:35Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */
include_once(JEV_ADMINPATH."/views/icalrepeat/view.html.php");

class ICalRepeatViewICalRepeat extends AdminICalRepeatViewICalRepeat 
{
	var $jevlayout = null;
	
	function __construct($config = array()){
		include_once(JPATH_ADMINISTRATOR.DS."includes".DS."toolbar.php");
		parent::__construct($config);	
		
		// TODO find the active admin template
		//JEVHelper::stylesheet("system.css",  "administrator/templates/system/css/");
		//JEVHelper::stylesheet("template.css",  "administrator/templates/khepri/css/");

		// used only for helper functions
		$this->jevlayout="default";	
		$this->addHelperPath(realpath(dirname(__FILE__)."/../default/helpers"));		
		$this->addHelperPath( JPATH_BASE.DS.'templates'.DS.JFactory::getApplication()->getTemplate().DS.'html'.DS.JEV_COM_COMPONENT.DS."helpers");		
	}	
	
	function edit($tpl = null)
	{
		$document =& JFactory::getDocument();		
		include(JEV_ADMINLIBS."/editStrings.php");		
		$document->addScriptDeclaration($editStrings);

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0")) JEVHelper::stylesheet( 'eventsadmin16.css','administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );
		else JEVHelper::stylesheet( 'eventsadmin.css','administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		JEVHelper::script('editical.js',  'administrator/components/'.JEV_COM_COMPONENT.'/assets/js/');  
		JEVHelper::script('view_detail.js', 'components/'.JEV_COM_COMPONENT.'/assets/js/');
		
		$document->setTitle(JText::_( 'EDIT_ICAL_REPEAT' ));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'EDIT_ICAL_REPEAT' ), 'jevents' );
	
		//JToolBarHelper::save('icalrepeat.save');

		//$this->addSaveToolBarButton();
		$submitbutton = JVersion::isCompatible("1.6.0") ? "Joomla.submitbutton" : "submitbutton";
		$toolbar = & JToolBar::getInstance('toolbar');
        $html = '<a class="toolbar" onclick="javascript: '.$submitbutton.'(\'icalrepeat.save\');return false;" href="#"><span class="icon-32-save" title="Save"> </span>'.JText::_( 'SAVE' ).'</a>';
        $toolbar->appendButton( 'Custom',$html, "customsave");
		
        $html = '<a class="toolbar" onclick="javascript: '.$submitbutton.'(\'icalrepeat.apply\');return false;" href="#"><span class="icon-32-apply" title="Save"> </span>'.JText::_( 'JEV_APPLY' ).'</a>';
        $toolbar->appendButton( 'Custom',$html, "customapply");
		
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup",0)) {
			$document->addStyleDeclaration("div#toolbar-box{margin:10px 10px 0px 10px;} div#jevents {margin:0px 10px 10px 10px;} ")	;
			JToolBarHelper::custom("icalevent.close",'cancel','cancel','Cancel',false);
			JRequest::setVar('tmpl', 'component'); //force the component template
		}
		else {	
			$this->addCancelToolBarButton();
		}

		//JToolBarHelper::help( 'screen.icalrepeat.edit', true);		
	
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		JHTML::_('behavior.tooltip');

		
		$this->_adminStart();			
		parent::displaytemplate($tpl);
		$this->_adminEnd();
	}	
	
	function _adminStart(){
		
		$dispatcher	=& JDispatcher::getInstance();
		list($this->year,$this->month,$this->day) = JEVHelper::getYMD();
		$this->Itemid	= JEVHelper::getItemid();
		$this->datamodel =new JEventsDataModel();
		$dispatcher->trigger( 'onJEventsHeader', array($this));

?>
	<div style="clear:both">
		<div id="toolbar-box" >
<?php
		$bar = & JToolBar::getInstance('toolbar');
		$barhtml = $bar->render();
		//$barhtml = str_replace('href="#"','href="javascript void();"',$barhtml);
		//$barhtml = str_replace('submitbutton','return submitbutton',$barhtml);
		echo $barhtml;
		
		$title = JFactory::getApplication()->get('JComponentTitle');
		echo $title;
?>
		</div>
<?php		
		$dispatcher	=& JDispatcher::getInstance();
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
		$bar = & JToolBar::getInstance('toolbar');
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
			$name="ViewHelper".ucfirst(substr($name,1));
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
