<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents\Administrator;

defined('_JEXEC') or die('Restricted access');

use JEvents\Site\Registry;
use Joomla\CMS\User\User;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;

/**
 * JEvents category table class.
 * Legacy class name: JEventsCategory
 */
class Category extends \Joomla\CMS\Table\Category
{
	var $_catextra = null;
	var $catid     = null;

	public static function categoriesTree(): array
	{
		$db    = Factory::getDbo();
		$query = "SELECT *, parent_id as parent FROM #__categories WHERE extension = '" . JEV_COM_COMPONENT . "' and published >= 0";
		$query .= " ORDER BY parent, lft";
		$db->setQuery($query);
		$mitems = 0;

		try
		{
			$mitems = $db->loadObjectList();
		}
		catch (\Exception $e)
		{
			echo $e;
		}

		$children = [];

		if ($mitems)
		{
			foreach ($mitems as $v)
			{
				if ($v->parent == 1)
				{
					$v->parent = $v->parent_id = 0;
				}

				$v->level -= 1;
				$pt        = $v->parent;
				$list      = array_key_exists($pt, $children) ? $children[$pt] : [];
				$list[]    = $v;
				$children[$pt] = $list;
			}
		}

		$list   = HTMLHelper::_('menu.treerecurse', 0, '', [], $children, 9999, 0, 0);
		$mitems = [];

		foreach ($list as $item)
		{
			$item->treename = str_replace("&#160;", "  ", $item->treename);
			$mitems[]       = HTMLHelper::_('select.option', $item->id, $item->treename);
		}

		return $mitems;
	}

	function bind($array, $ignore = [])
	{
		$array['id'] = isset($array['id']) ? intval($array['id']) : 0;

		if (empty($array['alias']))
		{
			$array['alias'] = OutputFilter::stringURLSafe($array['title']);
		}

		parent::bind($array);

		$params = new Registry($this->params);

		if (!$params->get("catcolour", false))
		{
			$color = array_key_exists("color", $array) ? $array['color'] : "#000000";
			if (!preg_match("/^#[0-9a-f]+$/i", $color)) $color = "#000000";
			$params->set("catcolour", $color);
		}

		if (!$params->get("admin", false))
		{
			$params->set("admin", array_key_exists("admin", $array) ? $array['admin'] : 0);
		}

		if (!$params->get("overlaps", false))
		{
			$params->set("overlaps", array_key_exists("overlaps", $array) ? intval($array['overlaps']) : 0);
		}

		if (!$params->get("image", false))
		{
			$params->set("image", array_key_exists("image", $array) ? intval($array['image']) : "");
		}

		$this->params    = (string) $params;
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
		$params         = new Registry($this->params);
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
			Factory::getApplication()->triggerEvent('afterSaveCategory', [$this]);
		}

		return $success;
	}

	function getAdminUser(): User
	{
		if (isset($this->_catextra) && $this->_catextra->admin > 0)
		{
			$catuser = new User();
			$catuser->load($this->_catextra->admin);
		}
		elseif (isset($this->admin) && $this->admin > 0)
		{
			$catuser = new User();
			$catuser->load($this->admin);
		}

		if (isset($catuser) && $catuser->id !== '')
		{
			return $catuser;
		}

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		return new User($params->get("jevadmin", 62));
	}
}