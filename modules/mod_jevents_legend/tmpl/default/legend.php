<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */

class DefaultModLegendView {

	var $_modid = null;

	var $_params	= null;
	var $inccss = false;
	var $disable = true;
	var $myItemid = 0;
	var $myTask = null;

	function getViewName(){
		return "default";
	}

	function DefaultModLegendView(&$params=null, $modid){

		$this->_modid = $modid;

		$this->_params	=& $params;
		$this->datamodel =& new JEventsdatamodel();
		$this->inccss	= $params->get('modlatest_inccss', 1);
		$this->disable	= $params->get('nonjeventsdisable', 1);
		
		if ($this->inccss){
			JEVHelper::componentStylesheet($this);
			JEVHelper::componentStylesheet($this,"modstyle.css");
		}		
		
		include_once(JEV_LIBS."/modfunctions.php");
		$this->myItemid = $this->datamodel->setupModuleCatids($this->_params);
		
		$menu =& JApplication::getMenu('site');
		$menuItem = $menu->getItem($this->myItemid);
		if ($menuItem->component == JEV_COM_COMPONENT){
			$this->myTask=$menuItem->query["task"] ;
		}
		else {
			$this->myTask="month.calendar";
		}
		
		
	}

	function displayCalendarLegend($style="list"){

		// since this is meant to be a comprehensive legend look for catids from menu first:
		global $mainframe;
		$cfg = & JEVConfig::getInstance();
		$Itemid = isset($this->myItemid)?$this->myItemid:JEVHelper::getItemid();;
		$user =& JFactory::getUser();

		$db	=& JFactory::getDBO();
		// Parameters - This module should only be displayed alongside a com_jevents calendar component!!!
		$cfg = & JEVConfig::getInstance();

		global $option; // NB $option must be global $option here!!!
		
		if ($this->disable && $option!=JEV_COM_COMPONENT) return;
		
		$catidList = "";

		$menu	=& JSite::getMenu();
		$active = $menu->getActive();
		if ((!is_null($active) && $active->component==JEV_COM_COMPONENT) || !isset($Itemid)){
			$params	=&  JComponentHelper::getParams(JEV_COM_COMPONENT);
		}
		else {
			// If accessing this function from outside the component then I must load suitable parameters
			$params = $menu->getParams($Itemid);
		}
		$params	=&  JComponentHelper::getParams(JEV_COM_COMPONENT);

		$c=0;
		$catids = array();
		while ($nextCatId = $params->get( "catid$c", null )){
			if (!in_array($nextCatId,$catids)){
				$catids[]=$nextCatId;
				$catidList .= (strlen($catidList)>0?",":"").$nextCatId;
			}
			$c++;
		}
		if ($catidList==""){
			$this->datamodel	= new JEventsDataModel();
			// find appropriate Itemid and setup catids for datamodel
			$this->myItemid = $this->datamodel->setupModuleCatids($this->_params);

			if (count($this->datamodel->catids)>0){
				$catidList = implode(",",$this->datamodel->catids);
				$catids = $this->datamodel->catids;
			}
		}
		
		$separator = $params->get("catseparator","|");
		$catidsOut = str_replace(",",$separator,$catidList);

		// I should only show legend for items that **can** be shown in calendar so must filter based on GET/POST
		$catidsIn = JRequest::getVar( 'catids', "NONE" );
		if ($catidsIn!="NONE") $catidsGP = explode($separator,$catidsIn);
		else $catidsGP = array();

		$sql = "SELECT cat.id, cat.title as name, cat.description, cat.access, evcat.color, cat.parent_id"
		. "\n FROM #__jevents_categories as evcat"
		."\n LEFT JOIN #__categories as cat ON evcat.id = cat.id"
		. "\n WHERE cat.section = '".JEV_COM_COMPONENT."'"
		. "\n AND cat.access <= $user->aid"
		. "\n AND cat.published = 1";
		if (strlen($catidList)>0) $sql .= " AND (cat.id IN ($catidList) OR cat.parent_id IN ($catidList))";
		$sql .= " ORDER BY cat.parent_id, cat.ordering ASC";

		$db->setQuery($sql);
		$allrows = $db->loadObjectList();

		$allcats =& new catLegend("0", JText::_('JEV_LEGEND_ALL_CATEGORIES'),"#d3d3d3",JText::_('JEV_LEGEND_ALL_CATEGORIES_DESC'));

		$availableCatsIds="";
		foreach ($allrows as $row){
			$availableCatsIds.=(strlen($availableCatsIds)>0?$separator:"").$row->id;
		}

		array_push($allrows,$allcats);
		if (count($allrows)==0) return "";
		else {

			$newrows = array();
			foreach ($allrows as $row) {
				if ($row->parent_id==0) {
					$newrows[$row->id] = array('row'=>$row, 'kids'=>array());
				}
				else if (array_key_exists($row->parent_id, $newrows)){
					$newrows[$row->parent_id]['kids'][$row->id] = array('row'=>$row, 'kids'=>array());
				}
				else {
					$matched = false;
					foreach ($newrows as $kid=>$newrow) {
						if (array_key_exists($row->parent_id, $newrow['kids'])){
							$newrows[$kid]['kids'][$row->id] = array('row'=>$row, 'kids'=>array());
							$matched = true;
							break;
						}
					}
					if (!$matched){
						$newrows[$row->id] = array('row'=>$row, 'kids'=>array());						
					}

				}
			}
			$allrows = array();
			foreach ($newrows as $key=>$row) {
				$allrows[$key] = $row['row'];
				foreach ($row['kids'] as $kid) {
					$allrows[$kid['row']->id] = $kid['row'];
					if (count($kid['kids'])>0){
						foreach ($kid['kids'] as $grandkey => $grandkid) {
							$allrows[$grandkey] = $grandkid['row'];
						}
					}
				}
			}

			if ($Itemid<999999) $itm = "&Itemid=$Itemid";
			$task 	= JRequest::getVar(	'jevcmd',	$cfg->get('com_startview'));

			list($year,$month,$day) = JEVHelper::getYMD();
			$tsk="";
			if ($task=="month.calendar" || $task=="year.listeventsevents" ||  $task=="week.listevents" || $task=="year.listevents" || $task=="day.listevents"|| $task=="cat.listevents"){
				$tsk="&task=$task&year=$year&month=$month&day=$day";
			}
			else {
				$tsk="&task=$this->myTask&year=$year&month=$month&day=$day";
			}
			include_once(JPATH_ADMINISTRATOR."/components/".JEV_COM_COMPONENT."/libraries/colorMap.php");

			switch ($style) {
				case 'list':
					$content = "<div class=\"event_legend_container\"><ul class=\"event_legend_list\">";
					foreach ($allrows as $row) {

						// do not show legend for categories exluded via GET/POST
						// but include parents of these categories and their children
						$ancestor = false;
						if (array_key_exists($row->id,$newrows) && count($newrows[$row->id]['kids']>0)){
							foreach ($newrows[$row->id]['kids'] as $key=>$val) {
								if (in_array($key,$catidsGP)){
									$ancestor = true;
									break;
								}
							}
						}
						$descendent = in_array($row->parent_id,$catidsGP);

						if (!$ancestor && !$descendent && $row->id>0 && count($catidsGP) && (!in_array($row->id, $catidsGP) && !in_array($row->parent_id, $catidsGP))) continue;
						$st1="background-color:".$row->color.";color:".JevMapColor($row->color);
						$cat = $row->id>0?"&catids=$row->id":"&catids=$availableCatsIds";
						$content .= "<li style='list-style:none;margin-top:5px;'>"
						."<div class='event_legend_name' style='".$st1."'>"
						//."$row->name ($row->id)</div>"
						."<a href='".JRoute::_("index.php?option=".JEV_COM_COMPONENT."$cat$itm$tsk")."' title='".JEventsHTML::special($row->name)."' style='color:inherit'>"
						.JEventsHTML::special($row->name)."</a></div>";
						if (strlen($row->description)>0) {
							$content .="<div class='event_legend_desc'>$row->description</div>";
						}
						$content .="</li>";
					}
					$content .= "</ul></div>";
					break;

				case 'block':
				default:
					$content = '<div class="event_legend_container">';
					foreach ($allrows as $row) {

						// do not show legend for categories exluded via GET/POST
						// but include parents of these categories and their children
						$ancestor = false;
						if (array_key_exists($row->id,$newrows) && count($newrows[$row->id]['kids']>0)){
							foreach ($newrows[$row->id]['kids'] as $key=>$val) {
								if (in_array($key,$catidsGP)){
									$ancestor = true;
									break;
								}
							}
						}
						$descendent = in_array($row->parent_id,$catidsGP);

						if (!$ancestor && !$descendent && $row->id>0 && count($catidsGP) && (!in_array($row->id, $catidsGP) && !in_array($row->parent_id, $catidsGP))) continue;

						$cat = $row->id>0 ? "&catids=$row->id" : "&catids=$availableCatsIds";
						$content .= '<div class="event_legend_item" style="border-color:'.$row->color.'">';
						$content .= '<div class="event_legend_name" style="border-color:'.$row->color.'">'
						. '<a href="'.JRoute::_("index.php?option=".JEV_COM_COMPONENT."$cat$itm$tsk").'" title="'.JEventsHTML::special($row->name).'">'
						. JEventsHTML::special($row->name).'</a>';
						$content .= '</div>'."\n";
						if (strlen($row->description)>0) {
							$content .='<div class="event_legend_desc"  style="border-color:'.$row->color.'">'.$row->description.'</div>';
						}
						$content .= '</div>'."\n";
					}
					// stop floating legend items
					$content .= '<br style="clear:both" />'."</div>\n";
			}
			// only if called from module
			if (isset($this->_params)) {
				if ($this->_params->get('show_admin', 0) && isset($year) && isset($month) && isset($day) && isset($Itemid)) {

					// This is only displayed when JEvents is the component so I can get the component view
					$component =& JComponentHelper::getComponent(JEV_COM_COMPONENT);

					$registry	=& JRegistry::getInstance("jevents");
					$controller =& $registry->getValue("jevents.controller",null);
					$view = $controller->view;

					//include_once(JPATH_SITE."/components/$option/events.html.php");
					ob_start();
					if (method_exists($view,"_viewNavAdminPanel")){
						echo $view->_viewNavAdminPanel();
					}
					$content .= ob_get_contents();
					ob_end_clean();
				}
			}

			return $content;
		}
	}
}
