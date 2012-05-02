<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 2749 2011-10-13 08:54:34Z geraintedwards $
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

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		JEVHelper::stylesheet('eventsadmin.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('ICAL_EVENT_REPEATS'));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('ICAL_EVENT_REPEATS'), 'jevents');

		JToolBarHelper::editList('icalrepeat.edit');
		JToolBarHelper::deleteList('Delete this repeat?', 'icalrepeat.delete');
		JToolBarHelper::cancel('icalevent.list');
		JToolBarHelper::custom('cpanel.cpanel', 'default.png', 'default.png', JText::_('JEV_ADMIN_CPANEL'), false);

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');

	}

	function edit($tpl = null)
	{
		$document = & JFactory::getDocument();
		include(JEV_LIBS . "editStrings.php");
		$document->addScriptDeclaration($editStrings);

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0"))
			JEVHelper::stylesheet('eventsadmin.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');
		else
			JEVHelper::stylesheet('eventsadmin.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');
		JEVHelper::script('editical.js', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/js/');
		JEVHelper::script('view_detail.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');

		$document->setTitle(JText::_('EDIT_ICAL_REPEAT'));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('EDIT_ICAL_REPEAT'), 'jevents');

		$this->addSaveToolBarButton();
		JToolBarHelper::apply('icalrepeat.apply', "Apply");
		//$this->addCancelToolBarButton();

		JToolBarHelper::cancel('icalrepeat.list');
		//JToolBarHelper::help( 'screen.icalrepeat.edit', true);

		$this->_hideSubmenu();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->getValue("section",0);

		JHTML::_('behavior.tooltip');

	}

	function addSaveToolBarButton()
	{

		$toolbar = & JToolBar::getInstance('toolbar');

		// Add a standard button
		//$bar->appendButton( 'Standard', $icon, $alt, $task, $listSelect, $x );

		$buttontext = JText::_('SAVE');
		$buttonhtml = '<a href="#" onclick="javascript:return clickIcalSaveButton();" class="toolbar">
		<span class="icon-32-save" title="' . $buttontext . '"></span>' . $buttontext . '</a><div style="position:relative;clear:both;">';
		$submitbutton = JVersion::isCompatible("1.6.0") ? "Joomla.submitbutton" : "submitbutton";

		ob_start();
		?>
		<div id="action_dialog"  style="position:absolute;right:0px;background-color:#eeeeee;border:solid 1px #000000;padding:16px;visibility:hidden">
			<div style="width:16px!important;float:right;font-size:10px;background-color:#ffffff;border:solid #000000;border-width:0 0 1px 1px;text-align:center;margin:-16px;">
				<a href="javascript:void(0)" onclick="closedialog()" style="font-weight:bold;text-decoration:none;color:#000000;border-width:0;">x</a>
			</div>

			<?php
			$buttontext = JText::_('SAVE_THIS');
			$buttontask = "icalrepeat.save";
			?>
			<div><a href="#" onclick="javascript:<?php echo $submitbutton; ?>('<?php echo $buttontask; ?>');return false;" class="toolbar"><span class="icon-32-save" style='margin:0px' title="<?php echo strip_tags($buttontext); ?>"></span><?php echo $buttontext; ?></a></div>

			<?php
			/*
			  $buttontext = JText::_( 'SAVE_FUTURE' );
			  $buttontask = "icalrepeat.savefuture";
			  <div><a href="#" onclick="javascript:return submitbutton('<?php echo $buttontask;?>')" class="toolbar"><span class="icon-32-save" style='margin:0px' title="<?php echo strip_tags($buttontext);?>"></span><?php echo $buttontext;?></a></div>
			 */
			?>

			<?php
			$buttontext = JText::_('SAVE_ALL');
			$buttontask = "icalevent.save";
			?>
			<div><a href="#" onclick="javascript:<?php echo $submitbutton; ?>('<?php echo $buttontask; ?>');return false;" class="toolbar"><span class="icon-32-save" style='margin:0px' title="<?php echo strip_tags($buttontext); ?>"></span><?php echo $buttontext; ?></a></div>

		</div>

		</div>
		<?php
		$html = $buttonhtml . ob_get_clean();
		$toolbar->appendButton('Custom', $html, "customsave");

	}

	function addCancelToolBarButton()
	{

		$toolbar = & JToolBar::getInstance('toolbar');
		$submitbutton = JVersion::isCompatible("1.6.0") ? "Joomla.submitbutton" : "submitbutton";
		$html = '<a class="toolbar" onclick="javascript: <?php echo $submitbutton;?>(\'icalrepeat.detail\');return false;" href="#"><span class="icon-32-cancel" title="Cancel"> </span>' . JText::_('CANCEL') . '</a>';
		$toolbar->appendButton('Custom', $html, "customcancel");

	}

}
