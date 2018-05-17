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

class jevSavedfiltersFilter extends jevFilter
{
	function __construct($contentElement){
		$this->filterNullValue=-1;
		$this->filterType="savedfilters";
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
                        
                $filter = array();
		$filter["title"]= JText::_("JEV_SAVED_FILTERS");
                $db = JFactory::getDbo();
                $db->setQuery("SELECT * FROM #__jevents_filtermap where userid = " . $db->quote(JFactory::getUser()->id." and modid=".$activemodid));
		$filters = $db->loadObjectList();
                $filter["html"] = '<input type="hidden" name="deletefilter" id="deletefilter" value="0"  />';
                if ($filters){
                    foreach($filters as $fltr){
                        $base = JUri::current();
                        $base .= (strpos($base,"?")>0 ? "&":"?")."jfilter=".$fltr->fid;
                        // OR USE this
                        /*
                        $router = JRouter::getInstance("site");
                        $vars = $router->getVars();
                        $vars["jfilter"]=$fltr->fid;
                        $base = "index.php?".http_build_query($vars);
                        $base = JRoute::_($base);
                        */
                        
                        $filter["html"] .= '<div class="saved_filter_buttons"><a href="'.$base.'" class="btn" >'.$fltr->name.' </a>';
                        $filter["html"] .= '<button id="saved_filter_buttons_img" class="btn" type="button" onclick="jQuery(\'#deletefilter\').val('.$fltr->fid.');form.submit();" ><span class="icon-trash"></span></button>';
                        $filter["html"] .= '</div>';
                    }
                     $filter["html"] .= "</br/>";
                }
		return $filter;

	}

}
