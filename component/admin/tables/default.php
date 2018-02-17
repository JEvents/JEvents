<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: default.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* User Table class
*
* @subpackage	Users
* @since 1.0
*/
class TableDefault extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var string
	 */
	var $name = null;

	var $title = null;
	var $subject = null;
	var $value = null;
	var $state = null;
	var $language = null;
	var $params = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct() {
		$db = JFactory::getDbo();
		parent::__construct('#__jev_defaults', 'id', $db);
	}


}
