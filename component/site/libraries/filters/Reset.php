<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Reset.php 1976 2011-04-27 15:54:31Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

class jevResetFilter extends jevFilter
{
	function __construct($contentElement){
		$this->filterNullValue=-1;
		$this->filterType="reset";
		$this->filterField = "";
		parent::__construct($contentElement,"");
	}

	function _createFilter($prefix = ""){
		return "";
	}

	/**
 * Creates javascript session memory reset action
 *
 */
	function _createfilterHTML(){
		$reset["title"]= "";
		$reset["html"] = "<input type='hidden' name='filter_reset' id='filter_reset' value='0' /><input type='button' value='".JText::_( 'RESET' )."' onclick='jQuery(\"input[name=filter_reset]\").each(function(idx,el){el.value=1;});form.submit()' />";
		return $reset;

	}

}
