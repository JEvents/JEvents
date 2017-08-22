<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: abstract.php 1085 2010-07-26 17:07:27Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML Abstract view class for the component frontend
 *
 * @static
 */
JLoader::register('JEventsDefaultView',JEV_VIEWS."/default/abstract/abstract.php");

class JEventsExtView extends JEventsDefaultView 
{
	var $jevlayout = null;

	function __construct($config = null)
	{
		parent::__construct($config);

		$this->jevlayout="ext";	

		$this->addHelperPath(dirname(__FILE__)."/../helpers/");
		
		$this->addHelperPath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'."helpers");

	}

	function viewNavTableBarIconic( $today_date, $this_date, $dates, $alts, $option, $task, $Itemid ){
		$this->loadHelper("ExtViewNavTableBarIconic");
		$var = new ExtViewNavTableBarIconic($this, $today_date, $this_date, $dates, $alts, $option, $task, $Itemid );
	}

}
