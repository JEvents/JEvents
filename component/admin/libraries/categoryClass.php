<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: categoryClass.php 1630 2009-11-26 05:31:25Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');

class JEventsCategory extends JTableCategory {

	var $_catextra			= null;
	// catid is a temporary field to ensure no duplicate mappings are created.
	// this can be removed from database and application after full migration
	var $catid 			= null;

	// security check
	function bind( $array ) {
		$cfg = & JEVConfig::getInstance();
		// TODO fix this when migrated data
		$section_name  = "com_jevents";
		$array['id'] = isset($array['id']) ? intval($array['id']) : 0;
		parent::bind($array);
		if (!isset($this->_catextra)){
			$this->_catextra = new CatExtra($this->_db);
		}
		$this->_catextra->color = array_key_exists("color",$array)?$array['color']:"#000000";
		if(!preg_match("/^#[0-9a-f]+$/i", $this->_catextra->color)) $this->_catextra->color= "#000000";
		unset($this->color);

		$this->_catextra->admin = array_key_exists("admin",$array)?$array['admin']:0;
		unset($this->admin);

		// Fill in the gaps
		$this->name=$this->title;
		$this->section=$section_name;
		$this->image_position="left";



		return true;
	}

	function load($oid=null){
		parent::load($oid);
		if (!isset($this->_catextra)){
			$this->_catextra = new CatExtra($this->_db);
		}
		if ($this->id>0){
			$this->_catextra->load($this->id);
		}
	}

	function store(){
		parent::store();
		if (isset($this->_catextra)){
			$this->_catextra->id = $this->id;
			$this->_catextra->store();
		}
		return true;
	}

	function getColor(){
		if (isset($this->_catextra)){
			return $this->_catextra->color;
		}
		else return "#000000";
	}

	function getAdmin(){
		static $adminuser;
		if (!isset($adminuser)){
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$adminuser = new  JUser($params->get("jevadmin",62));
		}
		
		if (isset($this->_catextra)){
			if ($this->_catextra->admin>0){
				$catuser = new JUser();
				 $catuser->load($this->_catextra->admin);
				return $catuser->username;
			}
		}
		return $adminuser->username;
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
		return $adminuser;
	}

	function getAdminId(){
	
		if (isset($this->_catextra)){
			return $this->_catextra->admin;
		}
		return 0;
	}

	public static function categoriesTree() {
	
		$db = & JFactory::getDBO();
		$query = "SELECT *, parent_id as parent FROM #__categories  WHERE section = '".JEV_COM_COMPONENT."'";
				
		$query.=" ORDER BY parent, ordering";
		$db->setQuery($query);
		$mitems = $db->loadObjectList();
		echo $db->getErrorMsg();
		$children = array ();
		if ($mitems) {
			foreach ($mitems as $v) {
				$pt = $v->parent;
				$list = @$children[$pt]?$children[$pt]: array ();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array (), $children, 9999, 0, 0);
		$mitems = array ();
		foreach ($list as $item) {
			$mitems[] = JHTML::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;'.$item->treename);
		}
		return $mitems;
	}

	
}

class CatExtra extends JTable {
	var $id 			= null;
	var $color			= null;
	var $admin		    = null;

	/**
	 * consturcotr
	 *
	 * @param string $db database reference
	 * @param string $tablename (including #__)
	 * @return gKwdMap
	 */
	function CatExtra( &$db ) {
		parent::__construct( '#__jevents_categories', "id", $db );
	}

	function store(){
		$this->_db->setQuery( "REPLACE #__jevents_categories SET id='$this->id', color='$this->color', admin='$this->admin'" );
		$this->_db->query();
	}

}
