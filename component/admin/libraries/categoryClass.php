<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: categoryClass.php 3157 2012-01-05 13:12:19Z geraintedwards $
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
		
		if (JVersion::isCompatible("1.6.0"))  {

			$array['id'] = isset($array['id']) ? intval($array['id']) : 0;
			$array['extension']= "com_jevents";
			$array['parent_id']= 1;
			$array['language']= "*";
			if (!isset($array['access'])) {
				$array['access'] = JRequest::getInt("access", -1);
				if ($array['access'] == -1){
					 $array['access'] = (int) JFactory::getConfig()->get('access');
				}
			}
			$array['alias'] = JApplication::stringURLSafe($array['title']);
			$array['path'] = $array['alias'] ;
			
			$this->setLocation($array['parent_id'], 'last-child');

			parent::bind($array);

		}
		else {
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

			$this->_catextra->overlaps = array_key_exists("overlaps",$array)?intval($array['overlaps']):0;

			// Fill in the gaps
			$this->name=$this->title;
			$this->section="com_jevents";
			$this->image_position="left";
		}

		return true;
	}

	function load($oid=null){
		parent::load($oid);
		if (!JVersion::isCompatible("1.6.0"))  {
			if (!isset($this->_catextra)){
				$this->_catextra = new CatExtra($this->_db);
			}
			if ($this->id>0){
				$this->_catextra->load($this->id);
			}
		}
		else {
			$params = new JParameter($this->params);
			$this->color = $params->get("catcolour", "#000000");
			$this->overlaps = $params->get("overlaps",0);
			$this->admin = $params->get("admin",0);

		}
	}

	function store(){
		$success = parent::store();
		if (! JVersion::isCompatible("1.6.0"))  {
			if (isset($this->_catextra)){
				$this->_catextra->id = $this->id;
				$this->_catextra->store();
			}
		}
		if ($success){
			JPluginHelper::importPlugin("jevents");
			$dispatcher	=& JDispatcher::getInstance();
			$set = $dispatcher->trigger('afterSaveCategory', array ($this));
		}
		return $success;
	}

	function getColor(){
		if (isset($this->_catextra)){
			return $this->_catextra->color;
		}
		else if (isset($this->color)){
			return $this->color;
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
				// fix deleted admin users
				if ($catuser->username == ""){
					$db = JFactory::getDBO();
					$db->setQuery("UPDATE #__jevents_categories SET admin=0 WHERE admin=".$this->_catextra->admin);
					$db->query();
					return "";
				}
				return $catuser->username;
			}
		}
		else if (isset($this->admin) && $this->admin>0){
			$catuser = new JUser();
			$catuser->load($this->admin);			
			return $catuser->username;
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
		else if (isset($this->admin) && $this->admin>0){
			$catuser = new JUser();
			$catuser->load($this->admin);			
			return $catuser;
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
		if (JVersion::isCompatible("1.6.0"))  {
			$query = "SELECT *, parent_id as parent FROM #__categories  WHERE extension = '".JEV_COM_COMPONENT."' and published>=0";
			$query.=" ORDER BY parent, lft";
		}
		else {
			$query = "SELECT *, parent_id as parent FROM #__categories  WHERE section = '".JEV_COM_COMPONENT."'";
			$query.=" ORDER BY parent, ordering";
		}

		$db->setQuery($query);
		$mitems = $db->loadObjectList();
		echo $db->getErrorMsg();
		$children = array ();
		if ($mitems) {
			foreach ($mitems as $v) {
				if (JVersion::isCompatible("1.6.0"))  {
					if ($v->parent==1){
						$v->parent=$v->parent_id=0;
					}
					$v->level -= 1;
				}
				$pt = $v->parent;
				$list = @$children[$pt]?$children[$pt]: array ();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array (), $children, 9999, 0, 0);
		$mitems = array ();
		foreach ($list as $item) {
			if (JVersion::isCompatible("1.6.0"))  {
				$item->treename = str_replace("&#160;","  ",$item->treename);
				$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
			}
			else $mitems[] = JHTML::_('select.option', $item->id, $item->treename);
		}
		return $mitems;
	}


}

class CatExtra extends JTable {
	var $id 			= null;
	var $color			= null;
	var $admin		    = null;
	var $overlaps	    = null;

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
		$this->_db->setQuery( "REPLACE #__jevents_categories SET id='$this->id', color='$this->color', admin='$this->admin', overlaps='$this->overlaps'" );
		$this->_db->query();
	}

}
