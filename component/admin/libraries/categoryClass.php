<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: categoryClass.php 3157 2012-01-05 13:12:19Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
use Joomla\CMS\User\User;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

class JEventsCategory extends Joomla\CMS\Table\Category
{

	var $_catextra = null;
	// catid is a temporary field to ensure no duplicate mappings are created.
	// this can be removed from database and application after full migration
	var $catid = null;

	// security check

	public static function categoriesTree()
	{

		$db    = Factory::getDbo();
		$query = "SELECT *, parent_id as parent FROM #__categories  WHERE extension = '" . JEV_COM_COMPONENT . "' and published >= 0";
		$query .= " ORDER BY parent, lft";
		$db->setQuery($query);
		$mitems = 0;

		try
		{
			$mitems = $db->loadObjectList();
		} catch (Exception $e) {
			echo $e;
		}

		$children = array();
		if ($mitems)
		{
			foreach ($mitems as $v)
			{
				if ($v->parent == 1)
				{
					$v->parent = $v->parent_id = 0;
				}
				$v->level -= 1;
				$pt       = $v->parent;
				$list     = array_key_exists($pt, $children) ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list   = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$mitems = array();
		foreach ($list as $item)
		{
			$item->treename = str_replace("&#160;", "  ", $item->treename);
			$mitems[]       = HTMLHelper::_('select.option', $item->id, $item->treename);
		}

		return $mitems;
	}

	function bind($array, $ignore = array())
	{

		$cfg         = JEVConfig::getInstance();
		$array['id'] = isset($array['id']) ? intval($array['id']) : 0;

		if(empty($array['alias'])) {
			$array['alias'] = JFilterOutput::stringURLSafe($array['title']);
		}

		parent::bind($array);

		$params = new JevRegistry($this->params);
		if (!$params->get("catcolour", false))
		{
			$color = array_key_exists("color", $array) ? $array['color'] : "#000000";
			if (!preg_match("/^#[0-9a-f]+$/i", $color)) $color = "#000000";
			$params->set("catcolour", $color);
		}
		if (!$params->get("admin", false))
		{
			$admin = array_key_exists("admin", $array) ? $array['admin'] : 0;
			$params->set("admin", $admin);
		}
		if (!$params->get("overlaps", false))
		{
			$overlaps = array_key_exists("overlaps", $array) ? intval($array['overlaps']) : 0;
			$params->set("overlaps", $overlaps);
		}

		if (!$params->get("image", false))
		{
			$image = array_key_exists("image", $array) ? intval($array['image']) : "";
			$params->set("image", $image);
		}

		$this->params = (string) $params;

		// Fill in the gaps
		$this->parent_id = array_key_exists("parent_id", $array) ? intval($array['parent_id']) : 1;
		$this->level     = array_key_exists("level", $array) ? intval($array['level']) : 1;
		$this->extension = "com_jevents";
		$this->language  = "*";

		$this->setLocation(1, 'last-child');

		return true;
	}

	function load($oid = null, $reset = true)
	{

		parent::load($oid);
		$params         = new JevRegistry($this->params);
		$this->color    = $params->get("catcolour", "#000000");
		$this->overlaps = $params->get("overlaps", 0);
		$this->admin    = $params->get("admin", 0);
		$this->image    = $params->get("image", "");
	}

	function store($updateNulls = false)
	{

		$success = parent::store();
		if ($success)
		{
			PluginHelper::importPlugin("jevents");
			$set = Factory::getApplication()->triggerEvent('afterSaveCategory', array($this));
			/*
				$table = Table::getInstance('Category', 'Table', array('dbo' => Factory::getDbo()));
				if (!$table->rebuild())
				{
					throw new Exception( $table->getError(), 500);
				}
			*/
		}

		return $success;
	}

	function getAdminUser()
	{

		if (isset($this->_catextra))
		{
			if ($this->_catextra->admin > 0)
			{
				$catuser = new User();
				$catuser->load($this->_catextra->admin);
			}
		}
		else if (isset($this->admin) && $this->admin > 0)
		{
			$catuser = new User();
			$catuser->load($this->admin);
		}

		// Lets only return once.
		if (isset($catuser) && $catuser->id !== '')
		{
			return $catuser;
		}
		else
		{
			$params    = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$adminuser = new  User($params->get("jevadmin", 62));

			return $adminuser;
		}
	}


}
