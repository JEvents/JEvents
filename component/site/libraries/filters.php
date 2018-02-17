<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: filters.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

class jevFilterProcessing
{
	var $filters;
	var $filterpath;
	var $where = array();
	var $join = array();
	var $filterHTML;
	var $filterReset;
	var $needsgroupby=false;

	static public $visiblefilters;
	static public $indexedvisiblefilters;

	function __construct($item, $filterpath=false){
		jimport('joomla.filesystem.folder');

		$this->filterpath = array();
		if (is_array($filterpath)){
			$this->filterpath = array_merge($this->filterpath,$filterpath);
		}
		else {
			$this->filterpath[] = $filterpath;
		}

		settype($this->filterpath, 'array'); //force to array
		$this->filterpath[]=dirname(__FILE__).'/'."filters";
		jimport('joomla.filesystem.folder');
		if (JFolder::exists(JPATH_SITE."/plugins/jevents")){
			$others = JFolder::folders(JPATH_SITE."/plugins/jevents",'filters',true,true);
			if (is_array($others)){
				$this->filterpath = array_merge($this->filterpath,$others);
			}
		}

		// Find if filter type module is visible and therefore if the filters should have 'memory'
		if (!isset(self::$visiblefilters)){
			self::$visiblefilters = array();

			// TODO Watch out if this becomes private - it just saves a DB query for the time being
			$visblemodules = JevModuleHelper::getVisibleModules();

			// note that $visblemodules are only those modules 'visible' on this page - could be overruled by special template
			//  but we can't do anything about that
			foreach ($visblemodules as $module) {
				if ($module->module == "mod_jevents_filter"  || (!is_array($module->params) && strpos($module->params, "jevfilters"))){

				    $modparams =  new JRegistry($module->params);
				    if ($module->module == "mod_jevents_filter" ){
					    $filters = $modparams->get("filters","");
				    }
				    else {
					    $filters = $modparams->get("jevfilters","");
				    }
				    if (trim($filters)!=""){
					    self::$visiblefilters = array_merge(explode(",",$filters),self::$visiblefilters);
				    }
				}
			}
			foreach (self::$visiblefilters as &$vf){
				$vf = ucfirst(trim($vf));
			}
			unset($vf);

			// Make sure the visible filters are preloaded before they appear in the modules - I need to know their filtertype values!!
			self::$indexedvisiblefilters = array();
			$registry	= JRegistry::getInstance("jevents");
			$registry->set("indexedvisiblefilters",false);

			foreach (self::$visiblefilters as $filtername) {
				$filter = "jev".ucfirst($filtername)."Filter";
				if (!class_exists($filter)){
					$filterFile = ucfirst($filtername).'.php';

					$filterFilePath = JPath::find($this->filterpath,$filterFile);
					if ($filterFilePath){
						include_once($filterFilePath);
					}
					else {
						//echo "Missing filter file $filterFile<br/>";
						continue;
					}
				}
				if ( defined($filter."::filterType") ){
					$thefilter =  new $filter("",$filtername);
					self::$indexedvisiblefilters[$filtername] = $thefilter->filterType;
					//self::$indexedvisiblefilters[$filtername] = $filter::filterType;
				}
				else {
					$thefilter =  new $filter("",$filtername);
					self::$indexedvisiblefilters[$filtername] = $thefilter->filterType;
				}

			}

			$registry	= JRegistry::getInstance("jevents");
			$registry->set("indexedvisiblefilters",self::$indexedvisiblefilters);
		}

		// get filter details
		if (is_object($item)){
			$filters = $item->getFilters();
		}
		else if (is_array($item)){
			$filters = $item;
		}
		else if (is_string($item)){
			$filters = array();
		}

		$this->filters = array();
		// extract filters if set
		foreach ($filters as $filtername) {
			$filter = "jev".ucfirst($filtername)."Filter";
			if (!class_exists($filter)){
				$filterFile = ucfirst($filtername).'.php';

				$filterFilePath = JPath::find($this->filterpath,$filterFile);

				if ($filterFilePath){
					include_once($filterFilePath);
				}
				else {
					echo "Missing filter file $filterFile<br/>";
					continue;
				}

			}
			$theFilter =  new $filter("",$filtername);
			$this->filters[] = $theFilter;
		}

		foreach ($this->filters as $filter) {
			$sqlFilter = $filter->_createFilter();
			if ($sqlFilter!="") $this->where[]=$sqlFilter;
			$joinFilter =  $filter->_createJoinFilter();
			if ($joinFilter!="") $this->join[] = $joinFilter;
			if ($filter->needsgroupby) $this->needsgroupby=true;
		}

	}


	public static function & getInstance($item, $filterpath="", $unsetfilter=false, $uid = ""){

		if (is_numeric($uid) && $uid == 0){
			$uid ="";
		}
		if ($uid==""){
			// find what is running - used by the filters
			$registry	= JRegistry::getInstance("jevents");
			$activeprocess = $registry->get("jevents.activeprocess","");
			$moduleid = $registry->get("jevents.moduleid","");
			$moduleparams = $registry->get("jevents.moduleparams", false);
			if ($moduleparams && $moduleparams->get("ignorefiltermodule",false) && $moduleid){
				$uid="mod".$moduleid;
			}
			else if ($moduleid && $registry->get("getnewfilters")){
				$uid="mod".$moduleid;
			}
		}

		$pluginsDir = JPATH_ROOT.'/'.'plugins'.'/'.'jevents';
		if ($filterpath=="") $filterpath=$pluginsDir.'/'."filters";

		static $instances;
		if ($unsetfilter && is_array($instances)){
			foreach (array_keys($instances) as $key){
				$newkey = @unserialize($key);
				if (is_array($newkey) && in_array($unsetfilter,$newkey)){
					unset($instances[$key]);
				}
			}
		}
		if (!isset($instances)){
			$instances=array();
		}
		if (is_object($item)){
			$key = get_class($item);
		}
		else if (is_array($item)){
			$key = serialize($item);
		}
		// enable the use of unique filters for a specific module or component instance
		$key .= $uid;
		if (!array_key_exists($key,$instances)){
			$instances[$key]= new jevFilterProcessing($item, $filterpath);
		}
		return $instances[$key];
	}

	function setWhereJoin(&$where, &$join){
		$where = array_merge($where, $this->where);
		$join = array_merge($join, $this->join);
	}

	function setSearchKeywords(&$extrasearchfields, &$extrajoin){
		foreach ($this->filters as $filter) {
			if (method_exists($filter,"setSearchKeywords")){
				$extrasearchfields = array_merge($extrasearchfields,  $filter->setSearchKeywords($extrajoin));
			}
		}
	}
	
	function needsGroupBy(){
		return 	$this->needsgroupby;
	}

	function getFilterHTML($allowAutoSubmit = true){
		if (isset($this->filterHTML)){
			return $this->filterHTML;
		}

		$this->filterHTML = array();
		foreach ($this->filters as $filter) {
			if (method_exists($filter,"createfilterHTML")){
				$filterHTML = $filter->createfilterHTML($allowAutoSubmit);
			}
			else {
				$filterHTML = $filter->_createfilterHTML();
			}
			if (!is_array($filterHTML)){
				continue;
			}
			if (array_key_exists("merge",$filterHTML)){
				$this->filterHTML = array_merge($this->filterHTML,$filterHTML["merge"]);
			}
			else {
				if (!isset($filterHTML["title"]) || !isset($filterHTML["html"]) || ($filterHTML["title"]=="" && $filterHTML["html"]=="")){
					continue;
				}
				$this->filterHTML[] = $filterHTML;
			}
		}
		return $this->filterHTML;
	}

	function getFilterReset(){
		if (!isset($this->filterReset)){
			$this->filterReset = array();
			foreach ($this->filters as $filter) {
				$this->filterReset[] = $filter->_createfilterReset();
			}
		}
		return $this->filterReset;
	}

}

class jevFilter
{
	var $filterNullValue;
	var $filterType;
	var $filterIsString = false;
	var $filter_value;
	var $needsgroupby = false;

	// number of filter fields on top of standard 1 (TODO in time merge these concepts)
	var $valueNum = 0;
	var $filterNullValues = array();
	var $filter_values = array();

	var $filterField = false;
	var $tableName = "";
	var $filterHTML="";
	var $session = null;
	var $useMemory = false;

	var $fieldset=false;
	// is this filter visible in a module or the core component - this determines if it should remember its value
	var $visible = false;

	function __construct($tablename, $filterfield, $isString=false){


		$registry	= JRegistry::getInstance("jevents");
		$indexedvisiblefilters = $registry->get("indexedvisiblefilters",array());
		if (!is_array($indexedvisiblefilters)) $indexedvisiblefilters = array();

		// This is our best guess as to whether this filter is visible on this page.
		$this->isVisible(in_array($this->filterType,$indexedvisiblefilters));

		// No longer needed since we set useCache = false just below this call.

		// If using caching should disable session filtering if not logged in
		//$cfg	 = JEVConfig::getInstance();
		//$joomlaconf = JFactory::getConfig();
		//$useCache = (int)$cfg->get('com_cache', 0) && $joomlaconf->get('caching', 1);
		
		// New special code in jevents.php sets the session variables in the cache id calculation!
		$useCache = false;
		
		// Is the filter module setup to reset automatically
		$module = JModuleHelper::getModule("mod_jevents_filter");
		if ($module){
			$modparams = new JRegistry($module->params);
			$option = JRequest::getCmd("option");
			if ($modparams->get("resetfilters")=="nonjevents" && $option!="com_jevents" && $option!="com_jevlocations" && $option!="com_jevpeople" && $option!="com_rsvppro"  && $option!="com_jevtags") {
				JRequest::setVar('filter_reset',1);
				JFactory::getApplication()->setUserState( 'active_filter_menu ',JRequest::getInt("Itemid", 0));
			}
			// DO NOT RESET if posting a filter form!
			else if ($modparams->get("resetfilters")=="newmenu" && JRequest::getCmd("jevents_filter_submit", false,"POST")!==false) {
				// Must use JRequest::getInt("Itemid") since missing event finder resets active menu item!
				if (JRequest::getInt("Itemid",0) && JRequest::getInt("Itemid", 0) != JFactory::getApplication()->getUserState("jevents.filtermenuitem",0)){
					JRequest::setVar('filter_reset',1);
					JFactory::getApplication()->setUserState( 'active_filter_menu ',JRequest::getInt("Itemid", 0));
				}				
			}
		}
		
		$user = JFactory::getUser();
		// TODO chek this logic
		if ((int)JRequest::getVar('filter_reset',0)){
			$this->filter_value =  $this->filterNullValue;
			for ($v=0;$v<$this->valueNum;$v++){
				$this->filter_values[$v] = $this->filterNullValues[$v] ;
			}
			$this->filter_value = JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
			for ($v=0;$v<$this->valueNum;$v++){
				$this->filter_values[$v] = JFactory::getApplication()->setUserState( $this->filterType.'_fvs_ses'.$v,$this->filterNullValues[$v] );
			}
		}
		// if not logged in and using cache then do not use session data
		// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
		else if ( ($user->get('id')==0 && $useCache) || !$this->visible) {
			$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue );
			for ($v=0;$v<$this->valueNum;$v++){
				$this->filter_values[$v] = JRequest::getVar($this->filterType.'_fvs'.$v, $this->filterNullValues[$v]);
			}
		}
		else {
			$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
			for ($v=0;$v<$this->valueNum;$v++){
				$this->filter_values[$v] = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fvs_ses'.$v, $this->filterType.'_fvs'.$v,$this->filterNullValues[$v] );
			}
		}
                $menuFilterValue = JEVHelper::getMenuFilter($this->filterType.'_fv', "@something abstract@");
                if ($menuFilterValue !="@something abstract@"){
                    $this->filter_value = $menuFilterValue;
                }
		/*
}
else {
$this->filter_value = JRequest::getString($this->filterType.'_fv', $this->filterNullValue );
for ($v=0;$v<$this->valueNum;$v++){
$this->filter_values[$v] = JRequest::getInt($this->filterType."_fvs".$v, $this->filterNullValues[$v] );
}
}
*/
		$this->tableName = $tablename;
		$this->filterField = $filterfield;
		$this->filterIsString = $isString;
	}

	public function isVisible($value = null){
		if (is_null($value)){
			return $this->visible;
		}
		else {
			$this->visible = $value;
		}
	}

	// simple utility function
	function _getFilterValue($filterType, $filterNullValue ){
		
		if (JFactory::getApplication()->isAdmin()){
			$filterValue = JFactory::getApplication()->getUserStateFromRequest( $filterType.'_fv_ses', $filterType.'_fv', $filterNullValue );
		}
		else {
			$filterValue = JRequest::getInt($filterType.'_fv', $filterNullValue );
		}
		return $filterValue;
	}

	function &getSession(){
		static $session;
		if (!isset($session)){
			include_once(dirname(__FILE__)."/Session.php");
			$session = new gweSession();
		}
		return $session;
	}

	function _createFilter($prefix=""){
		if (!$this->filterField ) return "";
		$filter="";
		if ($this->filter_value!=$this->filterNullValue){
			if ($this->filterIsString){
				$filter = "$prefix".$this->filterField."='$this->filter_value'";
			}
			else {
				$filter = "$prefix".$this->filterField."=$this->filter_value";
			}
		}
		return $filter;
	}

	function _createJoinFilter($prefix=""){
		if (!$this->filterField ) return "";
		$filter="";
		return $filter;
	}


	function _createfilterHTML(){ return "";}

	function _createfilterReset(){
		return 'elems = document.getElementsByName("'.$this->filterType.'_fv");if (elems.length>0) {elems[0].value="'.$this->filterNullValue.'"};';
	}
	
}

class jevBooleanFilter extends jevFilter
{
	var $label="";
	var $bothLabel = "";
	var $yesLabel = "";
	var $noLabel = "";

	public function __construct($tablename, $filterfield, $isstring=true,$bothLabel="Both", $yesLabel="Yes", $noLabel="No"){
		$this->filterNullValue="-1";
		$this->yesLabel = $yesLabel;
		$this->noLabel = $noLabel;
		$this->bothLabel = $bothLabel;
		parent::__construct($tablename, $filterfield, $isstring);
	}

	public function _createFilter($prefix=""){
		if (!$this->filterField ) return "";
		if ($this->filter_value==$this->filterNullValue) return "";
		$filter = "$prefix".$this->filterField."=".$this->filter_value;
		return $filter;
	}

	public function _createfilterHTML(){
		$filterList=array();
		$filterList["title"] = $this->filterLabel;
		$options = array();
		$options[] = JHTML::_('select.option', "-1", $this->bothLabel, "value","yesno");
		$options[] = JHTML::_('select.option', "0", $this->noLabel,"value","yesno");
		$options[] = JHTML::_('select.option',  "1", $this->yesLabel,"value","yesno");
		$filterList["html"] = JHTML::_('select.genericlist',$options, $this->filterType.'_fv', 'class="inputbox" size="1" onchange="form.submit();"', 'value', 'yesno', $this->filter_value );
		return $filterList;
	}

}

class jevTitleFilter extends jevFilter
{
	public function __construct ($tablename, $filterfield, $isstring=true){
		$this->filterNullValue="";
		$this->filterType="title";
		parent::__construct($tablename,$filterfield, true);
	}

	public function _createFilter($prefix=""){
		if (!$this->filterField ) return "";
		$filter="";
		if ($this->filter_value!=$this->filterNullValue){
			$filter =  "LOWER( cont.title ) LIKE '%".$this->filter_value."%'";
		}
		return $filter;
	}

	public function _createJoinFilter($prefix=""){
		if (!$this->filterField ) return "";
		if ($this->filter_value==$this->filterNullValue) return "";
		$filter="#__content AS cont ON cont.id=c.target_id";
		return $filter;
	}

	public function _createfilterHTML(){

		if (!$this->filterField) return "";


		if (!$this->filterField) return "";
		$filterList=array();
		$filterList["title"]="Content Title";
		$filterList["html"] = 	'<input type="text" name="'.$this->filterType.'_fv" value="'.$this->filter_value.'" class="text_area" onchange="form.submit();" />';

		return $filterList;

	}

}
