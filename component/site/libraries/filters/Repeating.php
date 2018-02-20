<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Repeating.php 3549 2013-09-03 09:26:21Z odp04y $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

class jevRepeatingFilter extends jevBooleanFilter
{
	const filterType = "repeating";

	function __construct($tablename, $filterfield, $isstring=true,$yesLabel="Jev_Yes", $noLabel="Jev_No"){
		$this->filterType=self::filterType;
		$this->filterLabel = JText::_("JEV_SHOW_REPEATING_EVENTS");
		parent::__construct($tablename,$filterfield, true);
        }

	function _createFilter($prefix=""){
		if (!$this->filterField ) return "";
		if ($this->filter_value ==0){
		$filter = "(rr.freq='none' OR rr.freq is null)";
                }
                else if ($this->filter_value ==1){
		$filter = "rr.freq<>'none'";
                }
                else $filter="";
		return $filter;	
	}
}
