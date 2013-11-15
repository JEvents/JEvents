<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevuser.php 1975 2011-04-27 15:52:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport("joomla.html.parameter.element");

class JElementJevuser extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'JEVUser';

	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$db = JFactory::getDBO();
		$class		= $node->attributes('class');
		if (!$class) {
			$class = "inputbox";
		}

		//jimport("joomla.html.html.list");
		$params = JComponentHelper::getParams("com_jevents");

		$db = JFactory::getDBO();

		$rules = JAccess::getAssetRules("com_jevents", true);
		$creatorgroups = $rules->getData();
		if (strpos($name,"jevadmin")===0){
			$action = "core.admin";
		}
		else if (strpos($name,"jeveditor")===0){
			$action = "core.edit";
		}
		else if (strpos($name,"jevpublisher")===0){
			$action = "core.edit.state";
		}
		else if (strpos($name,"admin")===0){
			$action = "core.edit.state";
		}
		else {
			$action = "core.create";
		}
		// need to merge the arrays because of stupid way Joomla checks super user permissions
		//$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups[$action]->getData());
		// use union orf arrays sincee getData no longer has string keys in the resultant array
		//$creatorgroups = $creatorgroups["core.admin"]->getData()+ $creatorgroups["core.create"]->getData();
		// use union orf arrays sincee getData no longer has string keys in the resultant array
		$creatorgroupsdata = $creatorgroups["core.admin"]->getData();
		// take the higher permission setting
		foreach ($creatorgroups[$action]->getData() as $creatorgroup => $permission)
		{
			if ($permission){
				$creatorgroupsdata[$creatorgroup]=$permission;
			}
		}

		$users = array(0);
		foreach ($creatorgroupsdata as $creatorgroup => $permission)
		{
			if ($permission == 1)
								{
				$users = array_merge(JAccess::getUsersByGroup($creatorgroup, true), $users);
			}
		}
		$sql = "SELECT id AS value, name AS text FROM #__users where id IN (".implode(",",array_values($users)).") ORDER BY name asc";
		$db->setQuery( $sql );
		$users = $db->loadObjectList();

		$users2[] = JHTML::_('select.option',  '0', '- '. JText::_( 'SELECT_USER' ) .' -' );
		$users2 = array_merge( $users2, $users );

		$users = JHTML::_('select.genericlist',   $users2, $control_name.'['.$name.']', 'class="'.$class.'" size="1" ', 'value', 'text', $value );

		return $users;
	}
}