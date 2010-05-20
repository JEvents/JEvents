<?php
/**
 * JEvents Component for Joomla 1.5.x
 * 
 * This file based on Joomla config component Copyright (C) 2005 - 2008 Open Source Matters.
 *
 * @version     $Id: params.php 1512 2009-07-15 10:51:30Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * @package		Joomla
 * @subpackage	Config
 */
class AdminParamsModelParams extends JModel
{
	/**
	 * Get the params for the configuration variables
	 */
	function &getParams()
	{
		static $instance;

		if ($instance == null)
		{
			$component	= JEV_COM_COMPONENT;

			$table =& JTable::getInstance('component');
			$table->loadByOption( $component );

			// work out file path
			if ($path = JRequest::getString( 'path' )) {
				$path = JPath::clean( JPATH_SITE.DS.$path );
				JPath::check( $path );
			} else {
				$option	= preg_replace( '#\W#', '', $table->option );
				$path	= JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'config.xml';
			}

			// Use our own class to add more functionality!
			include_once(JEV_ADMINLIBS."jevparams.php");			
			if (file_exists( $path )) {
				$instance = new JevParameter( $table->params, $path );
			} else {
				$instance = new JevParameter( $table->params );
			}
		}
		return $instance;
	}
}