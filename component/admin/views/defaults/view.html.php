<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1399 2009-03-30 08:31:52Z geraint $
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
class AdminDefaultsViewDefaults extends JEventsAbstractView
{

	function __construct($config = null)
	{
		parent::__construct($config);
		global $mainframe;

	}

	/**
	 * Defaults display function
	 *
	 * @param template $tpl
	 */
	function overview($tpl = null)
	{
		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEV_LAYOUT_DEFAULTS') );

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('JEV_LAYOUT_DEFAULTS'), 'jevents' );

		JToolBarHelper::custom( 'cpanel.cpanel', 'default.png', 'default.png', 'Control Panel', false );

		JSubMenuHelper::addEntry(JText::_('Defaults'), 'index.php?option='.JEV_COM_COMPONENT, true);

		JHTML::_('behavior.tooltip');

		$this->_hideSubmenu();

		global $mainframe;

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		// Get data from the model
		$model	=& $this->getModel();
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('items',		$items);

		parent::displaytemplate($tpl);


	}

	function edit($tpl = null){

		$this->_hideSubmenu();

		include_once(JPATH_ADMINISTRATOR.DS."includes".DS."toolbar.php");
		// TODO find the active admin template
		JHTML::stylesheet("system.css",JURI::root()."administrator/templates/system/css/");
		JHTML::stylesheet("template.css",JURI::root()."administrator/templates/khepri/css/");

		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );
		JHTML::script( 'editdefaults.js', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/js/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('JEV_LAYOUT_DEFAULT_EDIT'));

		// Set toolbar items for the page
		JToolBarHelper::title( JText::_('JEV_LAYOUT_DEFAULT_EDIT'), 'jevents' );

		JToolBarHelper::save("defaults.save");
		JToolBarHelper::cancel("defaults.cancel");

		JSubMenuHelper::addEntry(JText::_('Defaults'), 'index.php?option='.JEV_COM_COMPONENT, true);

		JHTML::_('behavior.tooltip');

		global $mainframe;

		$db		=& JFactory::getDBO();
		$uri	=& JFactory::getURI();

		// Get data from the model
		$model	=& $this->getModel();
		$item	= & $this->get( 'Data');

		$this->assignRef('item',		$item);

		parent::displaytemplate($tpl);

	}


	function showToolBar(){
		?>
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
	<?php		


	}

	protected function replaceLabels(&$value){
		// non greedy replacement - because of the ?
		$value = preg_replace_callback('|{{.*?:|', 'replaceLabelsCallback', $value);
	}


}

function replaceLabelsCallback($matches){
	if (count($matches)==1){		
		return "{{".JText::_(substr($matches[0],2, strlen($matches[0])-3)).":";
	}
	return "";
}