<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevcategory.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementJevcategory extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Category';

	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang =& JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$db = &JFactory::getDBO();

		$section	= $node->attributes('section');
		$class		= $node->attributes('class');
		if (!$class) {
			$class = "inputbox";
		}

		if (!isset ($section)) {
			// alias for section
			$section = $node->attributes('scope');
			if (!isset ($section)) {
				$section = 'content';
			}
		}

		$query = 'SELECT c.id, c.title as ctitle,p.title as ptitle, gp.title as gptitle, ggp.title as ggptitle, ' .
				' CASE WHEN CHAR_LENGTH(p.title) THEN CONCAT_WS(" => ", p.title, c.title) ELSE c.title END as title'.
				' FROM #__categories AS c' .
				' LEFT JOIN #__categories AS p ON p.id=c.parent_id' .
				' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id ' .
				' LEFT JOIN #__categories AS ggp ON ggp.id=gp.parent_id ' .
				//' LEFT JOIN #__categories AS gggp ON gggp.id=ggp.parent_id ' .
				' WHERE c.published = 1 ' .
				' AND c.section = '.$db->Quote($section);

		$db->setQuery($query);
		$options = $db->loadObjectList();
		echo $db->getErrorMsg();
		foreach ($options as $key=>$option) {
			$title = $option->ctitle;
			if (!is_null($option->ptitle)){
				$title = $option->ptitle."=>".$title;
			}
			if (!is_null($option->gptitle)){
				$title = $option->gptitle."=>".$title;
			}
			if (!is_null($option->ggptitle)){
				$title = $option->ggptitle."=>".$title;
			}
			/*
			if (!is_null($option->gggptitle)){
				$title = $option->gggptitle."=>".$title;
			}
			*/
			$options[$key]->title = $title;
		}
		JArrayHelper::sortObjects($options,"title");
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Category').' -', 'id', 'title'));

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="'.$class.'"', 'id', 'title', $value, $control_name.$name );
	}
}