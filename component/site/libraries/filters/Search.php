<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: Search.php 1976 2011-04-27 15:54:31Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_VALID_MOS') or defined('_JEXEC') or die( 'No Direct Access' );

// searches event
class jevSearchFilter extends jevFilter
{
	const filterType="search";

	function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType=self::filterType;
		$this->filterLabel=JText::_( 'SEARCH_EVENT' );
		$this->filterNullValue="";
		parent::__construct($tablename,$filterfield, true);
		// Should these be ignored?
		$reg = JFactory::getConfig();
		$modparams = $reg->get("jev.modparams",false);
		if ($modparams && $modparams->get("ignorefiltermodule",false)){
			$this->filter_value = $this->filterNullValue;
		}
		
	}

	function _createFilter($prefix=""){
		if (!$this->filterField ) return "";
		if (trim($this->filter_value)==$this->filterNullValue) return "";

		$db = JFactory::getDBO();
		$text = $db->Quote( '%'.$db->escape( $this->filter_value, true ).'%', false );
				
		$filter = "(det.summary LIKE $text OR det.description LIKE $text OR det.extra_info LIKE $text)";

		return $filter;
	}

	function _createJoinFilter($prefix=""){
		return "";
	}

	function _createfilterHTML(){

		if (!$this->filterField) return "";

		$db = JFactory::getDBO();
				
		$filterList=array();
		$filterList["title"]="<label class='evsearch_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		$filterList["html"] = "<input type='text' name='".$this->filterType."_fv' id='".$this->filterType."_fv'  class='evsearch'  value='".$this->filter_value."' />";

		$script = "try {JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:''});} catch (e) {}";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		return $filterList;

	}
}
