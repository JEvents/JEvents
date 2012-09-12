<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: Category.php 3542 2012-04-20 08:17:05Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2012 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

// Multiple select version - still a work in progress DO NOT USE
class jevMulticategoryFilter extends jevFilter
{
	function __construct($tablename, $filterfield, $isstring=true){

		// setup for all required function and classes
		$file = JPATH_SITE . '/components/com_jevents/mod.defines.php';
		if (file_exists($file) ) {
			include_once($file);
		}
		$reg = & JevRegistry::getInstance("jevents");
		$this->datamodel = $reg->getReference("jevents.datamodel",false);		
		if (!$this->datamodel){
			$this->datamodel = new JEventsDataModel();
			$this->datamodel->setupComponentCatids();
		}
		
		// NO filtering of the list att all
		$this->allAccessibleCategories = $this->datamodel->accessibleCategoryList(null, $this->datamodel->mmcatids,$this->datamodel->mmcatidList);		
		$this->accessibleCategories = $this->allAccessibleCategories;

	}
	
	function _createfilterHTML(){

		$params	=  JComponentHelper::getParams(JEV_COM_COMPONENT);
		$separator = $params->get("catseparator","|");
		$catidsIn		= JRequest::getVar(	'catids', 		'NONE' ) ;
		$catidsIn = explode($separator , $catidsIn);
		JArrayHelper::toInteger($catidsIn);		
		
		$filterList=array();
		$filterList["title"]=JText::_("Select_Category");

		//$filterList["html"] = JEventsHTML::buildCategorySelect( $filter_value, 'multiple="multiple" size="5" onchange="if ($(\'catidsfv\')) $(\'catidsfv\').value=this.value;submit(this.form)" ',$this->allAccessibleCategories,false,false,0,$this->filterType.'_fv' );
		// Not auto submitting
$content = '<script type="text/javascript">';		
$content .= "function setmulticatfilter(){
				var selects = $('multicatfilter');
				var catids = $('multicatcatids');
				catids.value  = '';
				\$A(selects.options).each(function(opt) {
					if (opt.selected) {
						if (catids.value  == ''){
							catids.value  = opt.value;
						}
						else {
							catids.value  += '$separator'+opt.value;
						}
					}
				});
			}";
$content .= '</script>';
		
		$filterList["html"] = JEventsHTML::buildCategorySelect( $catidsIn, 'multiple="multiple" size="5" id="multicatfilter" onchange="setmulticatfilter()" ',$this->allAccessibleCategories,false,false,0,$this->filterType.'_fv[]' );
		$filterList["html"] .= '<br/><input type="text" name="catids" id="multicatcatids" value="' . implode($separator, $catidsIn) . '" />';
		$filterList["html"] .= $content;
		
		//$script = "function reset".$this->filterType."_fvs(){document.getElements('option',\$('".$this->filterType."_fv')).each(function(item){item.selected=(item.value==0)?true:false;})};\n";
		//$script .= "try {JeventsFilters.filters.push({action:'reset".$this->filterType."_fvs()',id:'".$this->filterType."_fv',value:".$this->filterNullValue."});} catch (e) {}\n";
		// try/catch  incase this is called without a filter module!
		$script = "try {JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:0});} catch (e) {}\n";
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		return $filterList;

	}

}