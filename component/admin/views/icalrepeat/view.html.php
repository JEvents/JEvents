<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1573 2009-09-23 08:34:42Z geraint $
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
class AdminIcalrepeatViewIcalrepeat extends JEventsAbstractView
{
	function overview($tpl = null)
	{

		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('ICal Event Repeats'));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'ICal Event Repeats' ), 'jevents' );

		JToolBarHelper::editList('icalrepeat.edit');
		JToolBarHelper::deleteList('Delete this repeat?','icalrepeat.delete');
		JToolBarHelper::cancel('icalevent.list');
		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', JText::_('JEV_ADMIN_CPANEL'), false );

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');
	}

	function edit($tpl = null)
	{
		$document =& JFactory::getDocument();		
		include(JEV_LIBS."editStrings.php");		
		$document->addScriptDeclaration($editStrings);
		
		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );
		JHTML::script('editical.js?v=1.5.2','administrator/components/'.JEV_COM_COMPONENT.'/assets/js/');
		JHTML::script('view_detail.js','components/'.JEV_COM_COMPONENT.'/assets/js/');

		$document->setTitle(JText::_('Edit ICal Repeat'));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Edit ICal Repeat' ), 'jevents' );

		$this->addSaveToolBarButton();
		JToolBarHelper::apply('icalrepeat.apply');
		//$this->addCancelToolBarButton();
		
		JToolBarHelper::cancel('icalrepeat.list');
		//JToolBarHelper::help( 'screen.icalrepeat.edit', true);

		$this->_hideSubmenu();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');
	}



	function addSaveToolBarButton(){

		$toolbar = & JToolBar::getInstance('toolbar');

		// Add a standard button
		//$bar->appendButton( 'Standard', $icon, $alt, $task, $listSelect, $x );

		$buttontext = JText::_("Save");
		$buttonhtml = '<a href="#" onclick="javascript:return clickIcalSaveButton();" class="toolbar">
		<span class="icon-32-save" title="'.$buttontext.'"></span>'.$buttontext.'</a><div style="position:relative;clear:both;">';

		ob_start();
		?>
            <div id="action_dialog"  style="position:absolute;right:0px;background-color:#eeeeee;border:solid 1px #000000;padding:16px;visibility:hidden">
            	<div style="width:16px!important;float:right;font-size:10px;background-color:#ffffff;border:solid #000000;border-width:0 0 1px 1px;text-align:center;margin:-16px;">
            		<a href="javascript:void(0)" onclick="closedialog()" style="font-weight:bold;text-decoration:none;color:#000000;border-width:0;">x</a>
            	</div>
            	
            	<?php 
            	$buttontext = JText::_("Save this");
            	$buttontask = "icalrepeat.save";
            	?>
				<div><a href="#" onclick="javascript:submitbutton('<?php echo $buttontask;?>');return false;" class="toolbar"><span class="icon-32-save" style='margin:0px' title="<?php echo strip_tags($buttontext);?>"></span><?php echo $buttontext;?></a></div>

	           	<?php 
	           	/*
            	$buttontext = JText::_("Save future");
            	$buttontask = "icalrepeat.savefuture";
				<div><a href="#" onclick="javascript:return submitbutton('<?php echo $buttontask;?>')" class="toolbar"><span class="icon-32-save" style='margin:0px' title="<?php echo strip_tags($buttontext);?>"></span><?php echo $buttontext;?></a></div>
				*/
            	?>

				<?php 
            	$buttontext = JText::_("Save all");
            	$buttontask = "icalevent.save";
            	?>
				<div><a href="#" onclick="javascript:submitbutton('<?php echo $buttontask;?>');return false;" class="toolbar"><span class="icon-32-save" style='margin:0px' title="<?php echo strip_tags($buttontext);?>"></span><?php echo $buttontext;?></a></div>

			</div>

         </div>
         <?php
         $html = $buttonhtml . ob_get_clean();
         $toolbar->appendButton( 'Custom',$html, "customsave");

	}

	function addCancelToolBarButton(){

		$toolbar = & JToolBar::getInstance('toolbar');

         $html = '<a class="toolbar" onclick="javascript: submitbutton(\'icalrepeat.detail\');return false;" href="#"><span class="icon-32-cancel" title="Cancel"> </span>'.JText::_("Cancel").'</a>';
         $toolbar->appendButton( 'Custom',$html, "customcancel");

	}

}
