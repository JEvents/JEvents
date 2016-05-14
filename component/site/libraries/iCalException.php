<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: iCalException.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Class to handle event exceptions - used to RSS and iCal exports
 *
 */

class iCalException extends JTable  {

	/** @var int Primary key */
	var $ex_id		= null;
	var $rp_id		= null;
	var $eventid = null;
	var $eventdetail_id = null;
	// exception_type 0=delete, 1=other exception 
	var $exception_type = null;
	var $startrepeat = '0000-00-00 00:00:00';
	var $oldstartrepeat = '0000-00-00 00:00:00';


	function iCalException( &$db ) {
		parent::__construct( '#__jevents_exception', 'ex_id', $db );
	}

	public static function loadByRepeatId($rp_id){
		
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__jevents_exception WHERE rp_id=".intval($rp_id);
		$db->setQuery($sql);
		$data = $db->loadObject();
		if (!$data || is_null($data)){
			return false;
		}
		else {
			$exception = new iCalException($db);
			$exception->bind(get_object_vars($data));
			return $exception;
		}
	}
}
