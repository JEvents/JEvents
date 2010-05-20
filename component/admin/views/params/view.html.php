<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1512 2009-07-15 10:51:30Z geraint $
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
class AdminParamsViewParams extends JEventsAbstractView
{

	function edit()
	{
		JHTML::stylesheet( 'eventsadmin.css', 'administrator/components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Configuration'));
		
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Configuration' ), 'jevents' );

		JToolBarHelper::save('params.save');
		JToolBarHelper::cancel('cpanel.cpanel');
		
		$model		= &$this->getModel();		
		$this->params		= &$model->getParams();
		
		$component	= JComponentHelper::getComponent(JRequest::getCmd( 'component' ));

		JHTML::_('behavior.tooltip');
	}
	
	
}