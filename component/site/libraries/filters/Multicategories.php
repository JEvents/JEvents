<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Category.php 3542 2012-04-20 08:17:05Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class jevMulticategoriesFilter extends jevFilter
{
	const filterType = "multicategories";

	function __construct($tablename, $filterfield, $isstring = true)
	{

		$this->filterType = self::filterType;
		// setup for all required function and classes
		$file = JPATH_SITE . '/components/com_jevents/mod.defines.php';
		if (file_exists($file))
		{
			include_once($file);
		}
		$reg             = JevRegistry::getInstanceWithReferences("jevents");
		$this->datamodel = $reg->getReference("jevents.datamodel", false);
		if (!$this->datamodel)
		{
			$this->datamodel = new JEventsDataModel();
			$this->datamodel->setupComponentCatids();
		}

		$this->filterLabel     = Text::_('CATEGORIES');
		$this->filterNullValue = 0;
		parent::__construct($tablename, "catids", true);

		$catid = $this->filter_value;
		// NO filtering of the list att all
		$this->allAccessibleCategories = $this->datamodel->accessibleCategoryList(null, $this->datamodel->mmcatids, $this->datamodel->mmcatidList);
		if ($this->filter_value == array($this->filterNullValue) || $this->filter_value == "")
		{
			$this->accessibleCategories = $this->allAccessibleCategories;
		}
		else
		{
			$this->accessibleCategories = $this->datamodel->accessibleCategoryList(null, array($catid), $catid);
		}
	}

	function _createFilter($prefix = "")
	{

		if (!$this->filterField) return "";
		if ($this->filter_value == array($this->filterNullValue) || $this->filter_value == "")
		{
			return "";
		}

		/*
		 * code to allow filter to force events to be in ALL selected categories
		 */
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		if ($this->filter_value == array($this->filterNullValue) || $this->filter_value == "")
		{
			$input  = Factory::getApplication()->input;

			$catidsIn = $input->getString('catid', 'NONE');
			if ($catidsIn == "NONE" || $catidsIn == 0)
			{
				$catidsIn = $input->getString('category_fv', 'NONE');
			}

			$separator = $params->get("catseparator", "|");
			$catids    = explode($separator, $catidsIn);

			//$catids=  $this->datamodel->catids;
			if (count($catids) && $params->get("multicategory", 0))
			{
				// Ths is 'Relational Division'
				$filter = " ev.ev_id in ( "
					. " SELECT catmaprd.evid "
					. " FROM #__jevents_catmap as catmaprd "
					. " WHERE catmaprd.catid IN(" . implode(",", $catids) . ") "
					. " AND catmaprd.catid IN(" . $this->accessibleCategories . ")"
					. " GROUP BY catmaprd.evid "
					. " HAVING COUNT(catmaprd.catid) = " . count($catids) . ")";

			}
			else
			{
				$filter = " ev.catid IN (" . $this->accessibleCategories . ")";
			}

			return $filter;

		}

		/*
		$sectionname = JEV_COM_COMPONENT;

		$db = Factory::getDbo();
		$q_published = Factory::getApplication()->isClient('administrator') ? "\n WHERE c.published >= 0" : "\n WHERE c.published = 1";
		$where = ' AND (c.id =' . $this->filter_value .' OR p.id =' . $this->filter_value .' OR gp.id =' . $this->filter_value .' OR ggp.id =' . $this->filter_value .')';
		$query = "SELECT c.id"
			. "\n FROM #__categories AS c"
			. ' LEFT JOIN #__categories AS p ON p.id=c.parent_id'
			. ' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id '
			. ' LEFT JOIN #__categories AS ggp ON ggp.id=gp.parent_id '
			. $q_published
			. "\n AND c.section = '".$sectionname."'"
			. "\n " . $where;
			;

			$db->setQuery($query);
			$catlist = $db->loadColumn();
			array_unshift($catlist,-1);

		$filter = " ev.catid IN (".implode(",",$catlist).")";
		*/
		$filter = " ev.catid IN (" . $this->accessibleCategories . ")";

		$user = Factory::getUser();
		if ($params->get("multicategory", 0))
		{
			// access will already be checked
			$filter             = " catmap.catid IN(" . $this->accessibleCategories . ")";
			$this->needsgroupby = true;
		}

		return $filter;
	}

	// Ths join to catmap tables should already be done!
	/*
	function _createJoinFilter($prefix=""){
		if (!$this->filterField ) return "";
		if (intval($this->filter_value)==$this->filterNullValue) return "";

		$filter = "";
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("multicategory",0)){
			$filter .= "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$filter .=  "\n LEFT JOIN #__categories AS catmapcat ON catmap.catid = catmapcat.id";
		}
		return $filter;
	}
	*/

	function _createfilterHTML()
	{

		return $this->createfilterHTML(true);
	}

	function createfilterHTML($allowAutoSubmit = true)
	{

		if (!$this->filterField) return "";

		$filter_value = $this->filter_value;
		$input        = Factory::getApplication()->input;

		// if catids come from the URL then use this if filter is blank
		if ($filter_value == array($this->filterNullValue) || $filter_value == "")
		{
			if ($input->getInt("catids", 0) > 0)
			{
				$filter_value = $input->getInt("catids", 0);
			}
		}

		$filterList          = array();
		$filterList["title"] = "<label class='evcategory_label' for='" . $this->filterType . "_fv'>" . Text::_("JEV_SELECT_CATEGORIES") . "</label>";

		if ($allowAutoSubmit)
		{
			$filterList["html"] = JEventsHTML::buildCategorySelect($filter_value, 'onchange="if (document.getElementById(\'catidsfv\')) document.getElementById(\'catidsfv\').value=this.value;submit(this.form)" ', $this->allAccessibleCategories, false, false, 0, $this->filterType . '_fv',
				// Use this to allow multi-category filtering
				// JEV_COM_COMPONENT, false,  "ordering",  false, true );
				JEV_COM_COMPONENT, false,  "ordering",  false, true );
		}
		else
		{
			$filterList["html"] = JEventsHTML::buildCategorySelect($filter_value, 'onchange="if (document.getElementById(\'catidsfv\')) document.getElementById(\'catidsfv\').value=this.value;" ', $this->allAccessibleCategories, false, false, 0, $this->filterType . '_fv',
				// Use this to allow multi-category filtering
				// JEV_COM_COMPONENT, false,  "ordering",  false, true );
				JEV_COM_COMPONENT, false,  "ordering",  false, true );
		}

		// if there is only one category then do not show the filter
		if (strpos($filterList["html"], "<select") === false)
		{
			return "";
		}

		// try/catch  incase this is called without a filter module!
		$script = <<<SCRIPT
try {
	JeventsFilters.filters.push(
		{
			id:'{$this->filterType}_fv',
			value:0
		}
	);
}
catch (e) {}
function reset{$this->filterType}_fvs(){
	if (document.getElementById('catidsfv')) {
		document.getElementById('catidsfv').value=0;
	}
	jQuery('#{$this->filterType}_fv option').each(function(idx, item){
		item.selected=(item.value==0)?true:false;
	})
};
try {
	JeventsFilters.filters.push(
		{
			action:'reset{$this->filterType}_fvs()',
			id:'{$this->filterType}_fv',
			value:{$this->filterNullValue}
		}
	);
}
catch (e) {}
SCRIPT;

		$document = Factory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;

	}

	function createfilterHtmlUIkit($allowAutoSubmit = true)
	{

		if (!$this->filterField) return "";

		$filter_value = $this->filter_value;
		$input        = Factory::getApplication()->input;

		// if catids come from the URL then use this if filter is blank
		if ($filter_value == $this->filterNullValue || $filter_value == "")
		{
			if ($input->getInt("catids", 0) > 0)
			{
				$filter_value = $input->getInt("catids", 0);
			}
		}

		$filterList          = array();
		$filterList["title"] = "<label  for='" . $this->filterType . "_fv'>" . Text::_("JEV_SELECT_CATEGORY") . "</label>";

		if ($allowAutoSubmit)
		{
			$filterList["html"] = JEventsHTML::buildCategorySelect($filter_value, 'onchange="if (document.getElementById(\'catidsfv\')) document.getElementById(\'catidsfv\').value=this.value;submit(this.form)" ', $this->allAccessibleCategories, false, false, 0, $this->filterType . '_fv',
				// Use this to allow multi-category filtering
				// JEV_COM_COMPONENT, false,  "ordering",  false, true );
				JEV_COM_COMPONENT, false,  "ordering",  false, true );
		}
		else
		{
			$filterList["html"] = JEventsHTML::buildCategorySelect($filter_value, 'onchange="if (document.getElementById(\'catidsfv\')) document.getElementById(\'catidsfv\').value=this.value;" ', $this->allAccessibleCategories, false, false, 0, $this->filterType . '_fv',
				// Use this to allow multi-category filtering
				// JEV_COM_COMPONENT, false,  "ordering",  false, true );
				JEV_COM_COMPONENT, false,  "ordering",  false, true );
		}

		// if there is only one category then do not show the filter
		if (strpos($filterList["html"], "<select") === false)
		{
			return "";
		}

		$filterList["html"] =  str_replace("gsl-select", "gsl-select uk-select uk-form-medium uk-form-width-medium", $filterList["html"]) ;

		// try/catch  incase this is called without a filter module!
		$script = <<<SCRIPT
try {
	JeventsFilters.filters.push(
		{
			id:'{$this->filterType}_fv',
			value:0
		}
	);
}
catch (e) {}
function reset{$this->filterType}_fvs(){
	if (document.getElementById('catidsfv')) {
		document.getElementById('catidsfv').value=0;
	}
	jQuery('#{$this->filterType}_fv option').each(function(idx, item){
		item.selected=(item.value==0)?true:false;
	})
};
try {
	JeventsFilters.filters.push(
		{
			action:'reset{$this->filterType}_fvs()',
			id:'{$this->filterType}_fv',
			value:{$this->filterNullValue}
		}
	);
}
catch (e) {}
SCRIPT;

		$document = Factory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;

	}

}
