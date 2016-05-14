<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: categoryClass.php 3157 2012-01-05 13:12:19Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if (version_compare(JVERSION, "3.2.0", "lt")){
	JLoader::register('JTableCategory', JPATH_PLATFORM . '/joomla/database/table/category.php');
}

class JEventsCategory extends JTableCategory {

	var $_catextra			= null;
	// catid is a temporary field to ensure no duplicate mappings are created.
	// this can be removed from database and application after full migration
	var $catid 			= null;

	// security check
	function bind( $array , $ignore=array()) {
		$cfg = JEVConfig::getInstance();
		$array['id'] = isset($array['id']) ? intval($array['id']) : 0;
		parent::bind($array);
		
		$params = new JRegistry($this->params);
		if (!$params->get("catcolour", false)){
			$color = array_key_exists("color",$array)?$array['color']:"#000000";
			if(!preg_match("/^#[0-9a-f]+$/i", $color)) $color= "#000000";
			$params->set("catcolor",$color);
		}
		if (!$params->get("admin", false)){
			$admin = array_key_exists("admin",$array)?$array['admin']:0;
			$params->set("admin",$admin);
		}
		if (!$params->get("overlaps", false)){
			$overlaps = array_key_exists("overlaps",$array)?intval($array['overlaps']):0;
			$params->set("overlaps",$overlaps);
		}

		if (!$params->get("image", false)){
			$image = array_key_exists("image",$array)?intval($array['image']):"";
			$params->set("image",$image);
		}
		$this->params = (string)  $params;
				
		// Fill in the gaps
		$this->parent_id = array_key_exists("parent_id",$array)?intval($array['parent_id']):1;
		$this->level = array_key_exists("level",$array)?intval($array['level']):1;		
		$this->extension="com_jevents";
		$this->language ="*";
		
		$this->setLocation(1, 'last-child');
		
		return true;
	}

	function load($oid = NULL, $reset = true){
		parent::load($oid);
		$params = new JRegistry($this->params);
		$this->color = $params->get("catcolour", "#000000");
		$this->overlaps = $params->get("overlaps",0);
		$this->admin = $params->get("admin",0);		
		$this->image = $params->get("image","");
	}

	function store($updateNulls = false){
		$success = parent::store();
		if ($success){
			JPluginHelper::importPlugin("jevents");
			$dispatcher	= JEventDispatcher::getInstance();
			$set = $dispatcher->trigger('afterSaveCategory', array ($this));
/*			
			$table = JTable::getInstance('Category', 'JTable', array('dbo' => JFactory::getDbo()));
			if (!$table->rebuild())
			{
				throw new Exception( $table->getError(), 500);
			}
*/			
		}
		return $success;
	}

	function getAdminUser(){
		static $adminuser;
		if (!isset($adminuser)){
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$adminuser = new  JUser($params->get("jevadmin",62));
		}

		if (isset($this->_catextra)){
			if ($this->_catextra->admin>0){
				$catuser = new JUser();
				$catuser->load($this->_catextra->admin);
				return $catuser;
			}
		}
		else if (isset($this->admin) && $this->admin>0){
			$catuser = new JUser();
			$catuser->load($this->admin);			
			return $catuser;
		}
		return $adminuser;
	}

	public static function categoriesTree() {

		$db = JFactory::getDBO();
		$query = "SELECT *, parent_id as parent FROM #__categories  WHERE extension = '".JEV_COM_COMPONENT."' and published>=0";
		$query.=" ORDER BY parent, lft";
		$db->setQuery($query);
		$mitems = $db->loadObjectList();
		echo $db->getErrorMsg();
		$children = array ();
		if ($mitems) {
			foreach ($mitems as $v) {
				if ($v->parent==1){
					$v->parent=$v->parent_id=0;
				}
				$v->level -= 1;
				$pt = $v->parent;
				$list = @$children[$pt]?$children[$pt]: array ();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array (), $children, 9999, 0, 0);
		$mitems = array ();
		foreach ($list as $item) {
			$item->treename = str_replace("&#160;","  ",$item->treename);
			$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
		}
		return $mitems;
	}


}