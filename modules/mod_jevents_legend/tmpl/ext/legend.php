<?php
/**
 * copyright (C) 2008-2016 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */
include_once(JPATH_SITE."/modules/mod_jevents_legend/tmpl/default/legend.php");

class ExtModLegendView extends DefaultModLegendView{

	var $_modid = null;

	var $_params	= null;

	function getViewName(){
		return "ext";
	}

	function displayCalendarLegend($style="list"){
		// do not display normal legend if dynamic legend is visible on this page
		$registry	= JRegistry::getInstance("jevents");
		if ($registry->get("jevents.dynamiclegend",0)) {
			return;
		}

		// since this is meant to be a comprehensive legend look for catids from menu first:
		$cfg = JEVConfig::getInstance();
		$Itemid = $this->myItemid;
		$user = JFactory::getUser();

		$db	= JFactory::getDBO();
		// Parameters - This module should only be displayed alongside a com_jevents calendar component!!!
		$cfg = JEVConfig::getInstance();

		$option = JRequest::getCmd('option');
		if ($this->disable && $option!=JEV_COM_COMPONENT) return;

		$catidList = "";

		include_once(JPATH_ADMINISTRATOR."/components/".JEV_COM_COMPONENT."/libraries/colorMap.php");

		$menu	= JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		if ((!is_null($active) && $active->component==JEV_COM_COMPONENT) || !isset($Itemid)){
			$params	=  JComponentHelper::getParams(JEV_COM_COMPONENT);
		}
		else {
			// If accessing this function from outside the component then I must load suitable parameters
			$params = $menu->getParams($Itemid);
		}
		$params	=  JComponentHelper::getParams(JEV_COM_COMPONENT);

		$c=0;
		$catids = array();
		// New system
		$newcats = $params->get("catidnew", false);
		if ($newcats && is_array($newcats))
		{
			foreach ($newcats as $newcat)
			{
				if (!in_array($newcat, $catids))
				{
					$catids[] = $newcat;
					$catidList .= (JString::strlen($catidList) > 0 ? "," : "") . $newcat;
				}
			}
		}
		else
		{
			while ($nextCatId = $params->get("catid$c", null))
			{
				if (!in_array($nextCatId, $catids))
				{
					$catids[] = $nextCatId;
					$catidList .= (JString::strlen($catidList) > 0 ? "," : "") . $nextCatId;
				}
				$c++;
			}
		}
		if ($catidList == "" && $params->get("catid0", "xxx") == "xxx") {
			modJeventsLegendHelper::getAllCats($this->_params,$catids,$catidList);
			}

		$separator = $params->get("catseparator","|");
		$catidsOut = str_replace(",",$separator,$catidList);

		// I should only show legend for items that **can** be shown in calendar so must filter based on GET/POST
		$catidsIn = JRequest::getVar('catids', "NONE" );
		if ($catidsIn!="NONE" && $catidsIn!="0") $catidsGP = explode($separator,$catidsIn);
		else $catidsGP = array();
		$catidsGPList = implode(",",$catidsGP);

		// This produces a full tree of categories
		$allrows = $this->getCategoryHierarchy($catidList, $catidsGPList);

		// This is the full set of top level catids
		$availableCatsIds="";
		foreach ($allrows as $row){
			$availableCatsIds.=(JString::strlen($availableCatsIds)>0?$separator:"").$row->id;
		}

		$allcats = new catLegend("0", JText::_('JEV_LEGEND_ALL_CATEGORIES'),"#d3d3d3",JText::_('JEV_LEGEND_ALL_CATEGORIES_DESC'));
		$allcats->activeBranch = true;

		array_push($allrows,$allcats);
		if (count($allrows)==0) return "";
		else {

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
			switch ($style) {
				case 'list':
					$content = '<div class="event_legend_container">';
					$content .= '<table border="0" cellpadding="0" cellspacing="5" width="100%">';
					foreach ($allrows as $row) {

						if (isset($row->activeBranch)){
							$content .= $this->listKids($row, $itm, $tsk, $availableCatsIds);
								}

							}
					$content .= "</table>\n";
					$content .= "</div>";
					break;

				case 'block':
				default:
					$content = '<div class="event_legend_container">';
					foreach ($allrows as $row) {

						if (isset($row->activeBranch)){
							$content .= $this->blockKids($row, $itm, $tsk, $availableCatsIds);
								}

						}
					// stop floating legend items
					$content .= '<br style="clear:both" />'."</div>\n";
			}

			// only if called from module
			if (isset($this->_params)) {
				if ($this->_params->get('show_admin', 0) && isset($year) && isset($month) && isset($day) && isset($Itemid)) {

					// This is only displayed when JEvents is the component so I can get the component view
					$component = JComponentHelper::getComponent(JEV_COM_COMPONENT);

					$registry	= JRegistry::getInstance("jevents");
					$controller =& $registry->get("jevents.controller",null);
					if (!$controller) return $content;
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

	function listKids($row, $itm, $tsk,$availableCatsIds, $activeParent = false, $activeSubCat=0){
		$catclass="";
		if ($row->parent_id>0) $catclass = "childcat";
		if ($row->parent_id>0 && isset($row->activeBranch)) $catclass = "activechildcat";
		if ($row->parent_id>0 && $activeParent) $catclass = "activechildcat";
		if ($row->parent_id>0 && $activeSubCat>0 && $row->id!=$activeSubCat  && !isset($row->activeNode))  $catclass = "childcat";

		$cat = $row->id>0 ? "&catids=$row->id" : "";
		$content = "<tr class='".$catclass."'><td style='border:solid 1px #000000;height:5px;width:5px;background-color:".$row->color."'></td>\n"
		."<td class='legend' >"
		."<a style='text-decoration:none' href='".JRoute::_("index.php?option=".JEV_COM_COMPONENT."$cat$itm$tsk")."' title='".JEventsHTML::special($row->name)."'>"
		.JEventsHTML::special($row->name)."</a></td></tr>\n";

		if (isset($row->activeBranch) && isset($row->subcats)){
			$activeSubCat = 0;
			foreach ($row->subcats as $subcatid => $subcat){
				if (isset($subcat->activeBranch)){
					$activeSubCat = $subcatid;
				}
			}
			foreach ($row->subcats as $subcatid => $subcat){
				$content .= $this->listKids($subcat, $itm, $tsk,$availableCatsIds, isset($row->activeNode),$activeSubCat);
			}
		}

		return $content;


	}

	function blockKids($row, $itm, $tsk, $availableCatsIds, $activeParent = false, $activeSubCat=0){

		$catclass="";
		if ($row->parent_id>0) $catclass = "childcat";
		if ($row->parent_id>0 && isset($row->activeBranch)) $catclass = "activechildcat";
		if ($row->parent_id>0 && $activeParent) $catclass = "activechildcat";
		if ($row->parent_id>0 && $activeSubCat>0 && $row->id!=$activeSubCat  && !isset($row->activeNode))  $catclass = "childcat";

		$cat = $row->id>0 ? "&catids=$row->id" : "";
		$content = '<div class="event_legend_item '.$catclass.'" style="border-color:'.$row->color.'">';
		$content .= '<div class="event_legend_name" style="border-color:'.$row->color.'">'
		. '<a href="'.JRoute::_("index.php?option=".JEV_COM_COMPONENT."$cat$itm$tsk").'" title="'.JEventsHTML::special($row->name).'">'
		. JEventsHTML::special($row->name).'</a>';
		$content .= '</div>'."\n";
		if (JString::strlen($row->description)>0) {
			$content .='<div class="event_legend_desc"  style="border-color:'.$row->color.'">'.$row->description.'</div>';
		}
		$content .= '</div>'."\n";

		if (isset($row->activeBranch) && isset($row->subcats)){
			$activeSubCat = 0;
			foreach ($row->subcats as $subcatid => $subcat){
				if (isset($subcat->activeBranch)){
					$activeSubCat = $subcatid;
				}
			}
			foreach ($row->subcats as $subcatid => $subcat){
				$content .= $this->blockKids($subcat, $itm, $tsk,$availableCatsIds, isset($row->activeNode), $activeSubCat);
			}
		}

		return $content;

	}

}
