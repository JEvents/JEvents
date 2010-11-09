<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

class jevResetFilter extends jevFilter
{
	function jevResetFilter ($contentElement){
		$this->filterNullValue=-1;
		$this->filterType="reset";
		$this->filterField = "";
		parent::jevFilter($contentElement,"");
	}

	function _createFilter(){
		return "";
	}

	/**
 * Creates javascript session memory reset action
 *
 */
	function _createfilterHTML(){
		$reset["title"]= "";
		$reset["html"] = "<input type='hidden' name='filter_reset' id='filter_reset' value='0' /><input type='button' value='".JText::_("reset")."' onclick='$$(\"input[name=filter_reset]\").each(function(el){el.value=1;});form.submit()' />";
		return $reset;

	}

}
