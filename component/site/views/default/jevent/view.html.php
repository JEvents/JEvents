<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3155 2012-01-05 12:01:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// This view extends the icalevent view
include_once(dirname(__FILE__)."/../icalevent/view.html.php");

/**
 * HTML View class for the component frontend
 *
 * @static
 */
class DefaultViewJevent extends DefaultICalEvent 
{
	function __construct($config = null)
	{		
		parent::__construct($config);

		$this->addTemplatePath($this->_basePath.'/'."views".'/'.$this->jevlayout.'/'."icalevent".'/'.'tmpl');
	}
	
	function detail($tpl = null)
	{
		JEVHelper::componentStylesheet($this);

		$document = JFactory::getDocument();
		// TODO do this properly
		//$document->setTitle(JText::_( 'BROWSER_TITLE' ));
						
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$this->assign("introduction", $params->get("intro",""));
		

	}	
}
