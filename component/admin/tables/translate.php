<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevuser.php 3178 2012-01-13 09:44:58Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


class TableTranslate extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $translation_id = null;

	var $evdet_id = null;
	var $description = null;

	var $location = null;
	var $summary = null;

	var $contact= null;
	var $extra_info= null;

	var $language = null;
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct() {
		$db = JFactory::getDbo();
		parent::__construct('#__jevents_translation', 'translation_id', $db);
	}

	public static function checkTable(){
		$db = JFactory::getDbo();
	}

	function bind($array, $ignore = '') {
		$data = array();
		foreach($array as $k => $v){
			if (strpos($k, "trans_")===0){
				$data[str_replace("trans_", "", $k)] = $v;
			}
		}

		// convert nl2br if there is no HTML
		if (strip_tags($data['description']) == $data['description'])
		{
			$data['description'] = nl2br($data['description']);
		}
		if (strip_tags($data['extra_info']) == $data['extra_info'])
		{
			$data['extra_info'] = nl2br($data['extra_info']);
		}

		$success = parent::bind($data, $ignore);

		return $success;
	}

}

