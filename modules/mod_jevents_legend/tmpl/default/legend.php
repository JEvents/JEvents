<?php

/**
 * copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd - All rights reserved
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * HTML View class for the component frontend
 *
 * @static
 */
#[\AllowDynamicProperties]
class DefaultModLegendView
{

	var $_modid = null;
	var $_params = null;
	var $inccss = false;
	var $disable = true;
	var $myItemid = 0;
	var $myTask = null;

	function __construct(&$params, $modid)
	{

		$this->_modid = $modid;

		$this->_params   = &$params;
		$this->datamodel = new JEventsdatamodel();
		$this->inccss    = $params->get('modlatest_inccss', 1);
		$this->disable   = $params->get('nonjeventsdisable', 1);

		if ($this->inccss)
		{
			//JEVHelper::componentStylesheet($this);
			JEVHelper::componentStylesheet($this, "modstyle.css");
		}

		include_once(JEV_LIBS . "/modfunctions.php");
		$this->myItemid = $this->datamodel->setupModuleCatids($this->_params);

		$menu     = Factory::getApplication()->getMenu('site');
		$menuItem = $menu->getItem($this->myItemid);
		if ($menuItem && $menuItem->component == JEV_COM_COMPONENT)
		{
			$this->myTask = isset($menuItem->query["task"]) ? $menuItem->query["task"] : ($menuItem->query["view"] . "." . $menuItem->query["layout"]);
		}
		else
		{
			$this->myTask = "month.calendar";
		}

		static $css;

		if (!isset($css) && $params->get("hideinactivekids", 1))
		{
			$document = Factory::getDocument();
			$document->addStyleDeclaration(".childcat {display:none;}");
			$css = 1;
		}

	}

	function getViewName()
	{

		return "default";

	}

	function displayCalendarLegend($style = "list")
	{

		// do not display normal legend if dynamic legend is visible on this page
		$registry = JevRegistry::getInstance("jevents");
		if ($registry->get("jevents.dynamiclegend", 0))
		{
			return;
		}

		// since this is meant to be a comprehensive legend look for catids from menu first:
		$cfg    = JEVConfig::getInstance();
		$Itemid = isset($this->myItemid) ? $this->myItemid : JEVHelper::getItemid();

		$app    = Factory::getApplication();
		$input  = $app->input;

		$user = Factory::getUser();

		$db = Factory::getDbo();
		// Parameters - This module should only be displayed alongside a com_jevents calendar component!!!
		$cfg = JEVConfig::getInstance();

		$option = $input->getCmd('option');

		if ($this->disable && $option != JEV_COM_COMPONENT)
			return;

		$catidList = "";

		$menu   = $app->getMenu();
		$active = $menu->getActive();
		if ((!is_null($active) && $active->component == JEV_COM_COMPONENT) || !isset($Itemid))
		{
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		}
		else
		{
			// If accessing this function from outside the component then I must load suitable parameters
			$params = $menu->getParams($Itemid);
		}
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$c      = 0;
		$catids = array();
		// New system
		$newcats = $params->get("catidnew", false);
		if ($newcats && is_array($newcats))
		{
			foreach ($newcats as $newcat)
			{
				if (!in_array($newcat, $catids))
				{
					$catids[]  = $newcat;
					$catidList .= (StringHelper::strlen($catidList) > 0 ? "," : "") . $newcat;
				}
			}
		}
		else
		{
			while ($nextCatId = $params->get("catid$c", null))
			{
				if (!in_array($nextCatId, $catids))
				{
					$catids[]  = $nextCatId;
					$catidList .= (StringHelper::strlen($catidList) > 0 ? "," : "") . $nextCatId;
				}
				$c++;
			}
		}
		// special case where params are not yet saved
		if ($catidList == "" && $params->get("catid0", "xxx") == "xxx")
		{
			modJeventsLegendHelper::getAllCats($this->_params, $catids, $catidList);
		}

		$separator = $params->get("catseparator", "|");
		$catidsOut = str_replace(",", $separator, $catidList);

		// I should only show legend for items that **can** be shown in calendar so must filter based on GET/POST
		$catidsIn = $input->getString('catids', "NONE");
		if ($catidsIn != "NONE" && $catidsIn != "0")
			$catidsGP = explode($separator, $catidsIn);
		else
			$catidsGP = array();
		$catidsGP = ArrayHelper::toInteger($catidsGP);
		$catidsGPList = implode(",", $catidsGP);

		// This produces a full tree of categories
		$allrows = $this->getCategoryHierarchy($catidList, $catidsGPList);

		// This is the full set of top level catids
		$availableCatsIds = "";
		foreach ($allrows as $row)
		{
			$availableCatsIds .= (StringHelper::strlen($availableCatsIds) > 0 ? $separator : "") . $row->id;
		}

		$allcats               = new catLegend("0", Text::_('JEV_LEGEND_ALL_CATEGORIES'), "#d3d3d3", Text::_('JEV_LEGEND_ALL_CATEGORIES_DESC'));
		$allcats->activeBranch = true;

		array_push($allrows, $allcats);
		if (count($allrows) == 0)
			return "";
		else
		{


			if ($Itemid < 999999)
				$itm = "&Itemid=$Itemid";
			$task = $input->getCmd('jevcmd', $cfg->get('com_startview'));

			list($year, $month, $day) = JEVHelper::getYMD();
			$tsk = "";
			if ($task == "month.calendar" || $task == "year.listeventsevents" || $task == "week.listevents" || $task == "year.listevents" || $task == "day.listevents" || $task == "cat.listevents")
			{
				$tsk = "&task=$task&year=$year&month=$month&day=$day";
			}
			else
			{
				$tsk = "&task=$this->myTask&year=$year&month=$month&day=$day";
			}
			include_once(JPATH_ADMINISTRATOR . "/components/" . JEV_COM_COMPONENT . "/libraries/colorMap.php");

			switch ($style)
			{
				case 'list':
					$content = "<div class=\"event_legend_container\"><ul class=\"event_legend_list\">";
					foreach ($allrows as $row)
					{

						if (isset($row->activeBranch))
						{
							$content .= $this->listKids($row, $itm, $tsk, $availableCatsIds);
						}
					}
					$content .= "</ul></div>";
					break;

				case 'block':
				default:
					$content = '<div class="event_legend_container">';
					foreach ($allrows as $row)
					{

						if (isset($row->activeBranch))
						{
							$content .= $this->blockKids($row, $itm, $tsk, $availableCatsIds);
						}
					}
					// stop floating legend items
					$content .= '<br style="clear:both" />' . "</div>\n";
			}
			// only if called from module
			if (isset($this->_params))
			{
				if ($this->_params->get('show_admin', 0) && isset($year) && isset($month) && isset($day) && isset($Itemid))
				{

					// This is only displayed when JEvents is the component so I can get the component view
					$component = ComponentHelper::getComponent(JEV_COM_COMPONENT);

					$registry   = JevRegistry::getInstance("jevents");
					$controller = $registry->get("jevents.controller", null);
					if (!$controller) return $content;
					$view = $controller->view;

					//include_once(JPATH_SITE."/components/$option/events.html.php");
					ob_start();
					if (method_exists($view, "_viewNavAdminPanel"))
					{
						echo $view->_viewNavAdminPanel();
					}
					$content .= ob_get_contents();
					ob_end_clean();
				}
			}

			return $content;
		}

	}

	protected function getCategoryHierarchy($catidList, $catidsGPList)
	{

		$db   = Factory::getDbo();
		$aid  = $this->datamodel->aid;
		$user = Factory::getUser();

		// Get all the categories
		$sql = "SELECT c.* FROM #__categories as c WHERE extension='" . JEV_COM_COMPONENT . "'"
			. " AND  c.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user))
			// language filter
			. "\n  AND c.language in (" . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')'
			. " AND c.published = 1"
			. "\n ORDER BY c.lft";
		$db->setQuery($sql);
		$catlist = $db->loadObjectList('id');
		foreach ($catlist as &$cat)
		{
			$cat->name     = $cat->title;
			$params        = new JevRegistry($cat->params);
			$cat->color    = $params->get("catcolour", "");
			$cat->overlaps = $params->get("overlaps", 0);
			$cat->image    = $params->get("image", "");
		}
		unset($cat);

		// any plugin based resitrictions
		// remember NOT to reindex the list
		Factory::getApplication()->triggerEvent('onGetAccessibleCategories', array(& $catlist, false));

		// Copy the array
		$clonedCatList = unserialize(serialize($catlist));

		$validcats = array();
		if (StringHelper::strlen($catidsGPList) > 0)
			$validcats = array_merge($validcats, explode(",", $catidsGPList));

		// convert to a tree
		$cattree = $this->mapTree($catlist, $validcats);

		// constrain tree by component or module paramaters
		if (StringHelper::strlen($catidList) > 0)
		{
			$validcats = array();
			$validcats = array_merge($validcats, explode(",", $catidList));
			if (count($validcats) > 0)
			{
				$cattree2        = $this->mapTree($clonedCatList, $validcats);
				$combinedCatTree = $this->constrainTree($cattree, $cattree2);
			}
		}


		return $cattree;

	}

	function mapTree($dataset, $validcats)
	{

		$treeroot = version_compare(JVERSION, '1.6.0', '>=') ? 1 : 0;
		// For some sites recovered from backups or migrated the root may not be 1 or 0
		foreach ($dataset as $id => &$node)
		{
			if ($node->level == 1)
			{
				$treeroot = $node->parent_id;
				break;
			}
		}

		$tree     = array();
		foreach ($dataset as $id => &$node)
		{
			if (in_array($node->id, $validcats))
			{
				$this->markParentsActive($node, $dataset, $validcats);
			}
			if (count($validcats) == 0 && $node->parent_id == $treeroot)
			{
				$this->markParentsActive($node, $dataset, $validcats);
			}
			if ($node->parent_id == $treeroot)
			{ // root node
				$tree[$id] = &$node;
			}
			else
			{ // sub node
				// If this node's parent is not in the dataset because of plugin restrictions perhaps then ignore it
				if (!array_key_exists($node->parent_id, $dataset))
					continue;
				if (array_key_exists($node->parent_id, $dataset) && !isset($dataset[$node->parent_id]->subcats))
					$dataset[$node->parent_id]->subcats = array();
				$dataset[$node->parent_id]->subcats[$id] = &$node;
			}
		}

		return $tree;

	}

	// build the tree

	function markParentsActive(&$node, $dataset, $validcats)
	{

		$node->activeBranch = true;

		// if we have selected one node then mark is as active so we can show its children
		if (count($validcats) == 1)
		{
			$node->activeNode = true;
		}

		if ($node->parent_id > 0 && array_key_exists($node->parent_id, $dataset))
			$this->markParentsActive($dataset[$node->parent_id], $dataset, $validcats);

	}

	function constrainTree(&$cattree, &$constrainTree)
	{

		foreach (array_keys($cattree) as $id)
		{
			if (array_key_exists($id, $constrainTree))
			{
				if ((isset($constrainTree[$id]->activeBranch)) ||
					($cattree[$id]->parent_id > 0 && isset($constrainTree[$cattree[$id]->parent_id]->activeBranch)))
				{
					if (isset($cattree[$id]->subcats) && isset($constrainTree[$id]->subcats))
					{
						$this->constrainTree($cattree[$id]->subcats, $constrainTree[$id]->subcats);
					}
				}
				else
				{
					unset($cattree[$id]);
				}
			}
			else
			{
				unset($cattree[$id]);
			}
		}

	}

	function listKids($row, $itm, $tsk, $availableCatsIds, $activeParent = false, $activeSubCat = 0)
	{

		$catclass = "";
		if ($row->parent_id > 0)
			$catclass = "childcat";
		if ($row->parent_id > 0 && isset($row->activeBranch))
			$catclass = "activechildcat";
		if ($row->parent_id > 0 && $activeParent)
			$catclass = "activechildcat";
		if ($row->parent_id > 0 && $activeSubCat > 0 && $row->id != $activeSubCat && !isset($row->activeNode))
			$catclass = "childcat";

		$st1 = "background-color:" . $row->color . ";color:" . JevMapColor($row->color);
		//$cat = $row->id > 0 ? "&catids=$row->id" : "&catids=$availableCatsIds";
		$cat     = $row->id > 0 ? "&catids=$row->id" : "";
		$content = "\n<li style='list-style:none;margin-top:5px;'>"
			. "<div class='event_legend_name' style='" . $st1 . "'>"
			//."$row->name ($row->id)</div>"
			. "<a href='" . Route::_("index.php?option=" . JEV_COM_COMPONENT . "$cat$itm$tsk") . "' title='" . JEventsHTML::special($row->name) . "' style='color:inherit'>"
			. JEventsHTML::special($row->name) . "</a></div>";
		if (StringHelper::strlen($row->description) > 0)
		{
			$content .= "<div class='event_legend_desc'>$row->description</div>";
		}
		$content .= "</li>";

		if (isset($row->activeBranch) && isset($row->subcats))
		{
			$activeSubCat = 0;
			foreach ($row->subcats as $subcatid => $subcat)
			{
				if (isset($subcat->activeBranch))
				{
					$activeSubCat = $subcatid;
				}
			}
			foreach ($row->subcats as $subcatid => $subcat)
			{
				$content .= $this->listKids($subcat, $itm, $tsk, $availableCatsIds, isset($row->activeNode), $activeSubCat);
			}
		}

		return $content;

	}

	function blockKids($row, $itm, $tsk, $availableCatsIds, $activeParent = false, $activeSubCat = 0)
	{

		$catclass = "";
		if ($row->parent_id > 0)
			$catclass = "childcat";
		if ($row->parent_id > 0 && isset($row->activeBranch))
			$catclass = "activechildcat";
		if ($row->parent_id > 0 && $activeParent)
			$catclass = "activechildcat";
		if ($row->parent_id > 0 && $activeSubCat > 0 && $row->id != $activeSubCat && !isset($row->activeNode))
			$catclass = "childcat";

		//$cat = $row->id > 0 ? "&catids=$row->id" : "&catids=$availableCatsIds";
		$cat     = $row->id > 0 ? "&catids=$row->id" : "";
		$content = '<div class="event_legend_item ' . $catclass . '" style="border-color:' . $row->color . '">';
		$content .= '<div class="event_legend_name" style="border-color:' . $row->color . '">'
			. '<a href="' . Route::_("index.php?option=" . JEV_COM_COMPONENT . "$cat$itm$tsk") . '" title="' . JEventsHTML::special($row->name) . '">'
			. JEventsHTML::special($row->name) . '</a>';
		$content .= '</div>' . "\n";
		if (StringHelper::strlen($row->description) > 0)
		{
			$content .= '<div class="event_legend_desc"  style="border-color:' . $row->color . '">' . $row->description . '</div>';
		}
		$content .= '</div>' . "\n";

		if (isset($row->activeBranch) && isset($row->subcats))
		{
			$activeSubCat = 0;
			foreach ($row->subcats as $subcatid => $subcat)
			{
				if (isset($subcat->activeBranch))
				{
					$activeSubCat = $subcatid;
				}
			}
			foreach ($row->subcats as $subcatid => $subcat)
			{
				$content .= $this->blockKids($subcat, $itm, $tsk, $availableCatsIds, isset($row->activeNode), $activeSubCat);
			}
		}

		return $content;

	}

}

