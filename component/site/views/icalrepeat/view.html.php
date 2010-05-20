<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1647 2009-12-31 03:55:43Z geraint $
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
	
	function __construct($config = array()){
		include_once(JPATH_ADMINISTRATOR.DS."includes".DS."toolbar.php");
		parent::__construct($config);	
		
		// TODO find the active admin template
		JHTML::stylesheet("system.css",JURI::root()."administrator/templates/system/css/");
		JHTML::stylesheet("template.css",JURI::root()."administrator/templates/khepri/css/");

		//JHTML::script('toolbarfix.js','components/'.JEV_COM_COMPONENT.'/assets/js/');
	}	
	
	function edit($tpl = null)
	{
		$document =& JFactory::getDocument();		
		include(JEV_ADMINLIBS."/editStrings.php");		
		$document->addScriptDeclaration($editStrings);

		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );
		JHTML::script('editical.js?v=1.5.4','administrator/components/'.JEV_COM_COMPONENT.'/assets/js/');
		JHTML::script('view_detail.js','components/'.JEV_COM_COMPONENT.'/assets/js/');
		
		$document->setTitle(JText::_('Edit ICal Repeat'));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Edit ICal Repeat' ), 'jevents' );
	
		//JToolBarHelper::save('icalrepeat.save');

		//$this->addSaveToolBarButton();
		$toolbar = & JToolBar::getInstance('toolbar');
        $html = '<a class="toolbar" onclick="javascript: submitbutton(\'icalrepeat.save\');return false;" href="#"><span class="icon-32-save" title="Save"> </span>'.JText::_("Save").'</a>';
        $toolbar->appendButton( 'Custom',$html, "customsave");
		
        $html = '<a class="toolbar" onclick="javascript: submitbutton(\'icalrepeat.apply\');return false;" href="#"><span class="icon-32-apply" title="Save"> </span>'.JText::_("Apply").'</a>';
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
		
?>
	<div style="clear:both">
		<div id="toolbar-box" >
<?php
		$bar = & JToolBar::getInstance('toolbar');
		$barhtml = $bar->render();
		//$barhtml = str_replace('href="#"','href="javascript void();"',$barhtml);
		//$barhtml = str_replace('submitbutton','return submitbutton',$barhtml);
		echo $barhtml;
		global $mainframe;
		$title = $mainframe->get('JComponentTitle');
		echo $title;
?>
		</div>
<?php			
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
		global $mainframe;
		$title = $mainframe->get('JComponentTitle');
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
	
}
