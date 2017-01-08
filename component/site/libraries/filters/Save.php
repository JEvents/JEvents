<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: Reset.php 1976 2011-04-27 15:54:31Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

class jevSaveFilter extends jevFilter
{
	function __construct($contentElement){
		$this->filterNullValue=-1;
		$this->filterType="save";
		$this->filterField = "";
		parent::__construct($contentElement,"");
	}

	function _createFilter($prefix = ""){
		return "";
	}

	/**
        * Creates facility to save filter values
        *
        */
	function _createfilterHTML(){
                // Only save filters for non-guests
                if (JFactory::getUser()->id == 0){
                    return false;
                }
                
                $app = JFactory::getApplication();
                $activeModule = isset($app->activeModule)?$app->activeModule :  false;
                $activemodid = (isset($activeModule)? $activeModule->id : 0);
                
                $value = JFactory::getApplication()->input->getString("filtername","");
                $value = htmlspecialchars($value);
		$filter["title"]= JText::_("JEV_SAVE_FILTER");
		$filter["html"] = '<input type="text" name="filtername" id="filtername" value="'.$value.'" placeholder="'.JText::_("JEV_SAVE_FILTER_AS").'" />';
                $filter["html"] .= '<input type="hidden" name="modid" id="modid" value="'.$activemodid.'"  />';

                /*
		$filter["html"] = "<textarea name='filtername' id='filtername' rows='1' placeholder='".JText::_("JEV_SAVE_FILTER_AS")."' >"
                        . $value 
                        . "</textarea>";
                */
		return $filter;

	}

}
