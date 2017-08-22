<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: abstract.php 3153 2012-01-05 10:11:45Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


// option masks
define( 'MASK_BACKTOLIST', 0x0001 );
define( 'MASK_READON',     0x0002 );
define( 'MASK_POPUP',      0x0004 );
define( 'MASK_HIDEPDF',    0x0008 );
define( 'MASK_HIDEPRINT',  0x0010 );
define( 'MASK_HIDEEMAIL',  0x0020 );
define( 'MASK_IMAGES',     0x0040 );
define( 'MASK_VOTES',      0x0080 );
define( 'MASK_VOTEFORM',   0x0100 );

define( 'MASK_HIDEAUTHOR',     0x0200 );
define( 'MASK_HIDECREATEDATE', 0x0400 );
define( 'MASK_HIDEMODIFYDATE', 0x0800 );

define( 'MASK_LINK_TITLES', 0x1000 );

// mos_content.mask masks
define( 'MASK_HIDE_TITLE', 0x0001 );
define( 'MASK_HIDE_INTRO', 0x0002 );

use Joomla\String\StringHelper;

/**
 * HTML Abstract view class for the component frontend
 *
 * @static
 */
class JEventsDefaultView extends JEventsAbstractView
{
	var $jevlayout = null;

	function __construct($config = null)
	{
		parent::__construct($config);

		$this->jevlayout="default";	
		
		$this->addHelperPath(realpath(dirname(__FILE__)."/../helpers"));
		
		$this->addHelperPath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'."helpers");

		// attach data model
		$this->datamodel  =  new JEventsDataModel();
		$this->datamodel->setupComponentCatids();
		
		$reg = JevRegistry::getInstance("jevents");
		$reg->setReference("jevents.datamodel",$this->datamodel);		

	}

	function getViewName(){
		return $this->jevlayout;
	}

	function loadHelper( $file = null)
	{
		if (function_exists($file) || class_exists($file)) return true;
		
		// load the template script
		jimport('joomla.filesystem.path');
		$helper = JPath::find($this->_path['helper'], $this->_createFileName('helper', array('name' => $file)));

		if ($helper != false)
		{
			// include the requested template filename in the local scope
			include_once $helper;
		}
		return $helper;
	}
/*
	function _header() {
		$this->loadHelper("DefaultViewHelperHeader");
		DefaultViewHelperHeader($this);
	}

	function _footer() {
		$this->loadHelper("DefaultViewHelperFooter");
		DefaultViewHelperFooter($this);
	}

	function _showNavTableBar() {
		$this->loadHelper("DefaultViewHelperShowNavTableBar");
		DefaultViewHelperShowNavTableBar($this);
	}

	function _viewNavAdminPanel(){
		$this->loadHelper("DefaultViewHelperViewNavAdminPanel");
		DefaultViewHelperViewNavAdminPanel($this);
	}
*/
	// this doens't follow naming convention so must declare
	function _datecellAddEvent($year, $month,$day){
		$this->loadHelper("DefaultViewDatecellAddEvent");
		DefaultViewDatecellAddEvent($this,$year, $month,$day);
	}

	// These don't follow argument pattern
	function paginationForm($total, $limitstart, $limit){
		if ($this->loadHelper("DefaultPaginationForm")){
			DefaultPaginationForm($total, $limitstart, $limit, isset($this->keyword)?$this->keyword:"");
		}
	}
	
	function paginationSearchForm($total, $limitstart, $limit){
		if ($this->loadHelper("DefaultPaginationSearchForm")){
			DefaultPaginationSearchForm($total, $limitstart, $limit, isset($this->keyword)?$this->keyword:"");
		}
	}

	// This handles all methods where the view is passed as the first argument
	function __call($name, $arguments){
		if (strpos($name,"_")===0){
			$name="ViewHelper".ucfirst(JString::substr($name,1));
		}
		$helper = ucfirst($this->jevlayout).ucfirst($name);
		if (!$this->loadHelper($helper)){
			$helper = "Default".ucfirst($name);
			if (!$this->loadHelper($helper)){
				return;
			}
		}
		$args = array_unshift($arguments,$this);
		if (class_exists($helper)){
			if (class_exists("ReflectionClass") ){
				$reflectionObj = new ReflectionClass($helper);
				if (method_exists($reflectionObj,"newInstanceArgs")){
					$var = $reflectionObj->newInstanceArgs($arguments);	
				}
				else {
					$var = $this->CreateClass($helper,$arguments);
				}
			}
			else {
				$var = $this->CreateClass($helper,$arguments);
			}
			return;
		}
		else if (is_callable($helper)){
			return call_user_func_array($helper,$arguments);
		}
	}

	protected function CreateClass($className, $params) {
		switch (count($params)) {
			case 0:
				return new $className();
				break;
			case 1:
				return new $className($params[0]);
				break;
			case 2:
				return new $className($params[0], $params[1]);
				break;
			case 3:
				return new $className($params[0], $params[1], $params[2]);
				break;
			case 4:
				return new $className($params[0], $params[1], $params[2], $params[3]);
				break;
			case 5:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4]);
				break;
			case 6:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
				break;
			case 7:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]);
				break;
			case 8:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7]);
				break;
			case 9:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8]);
				break;
			case 10:
				return new $className($params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $params[7], $params[8], $params[9]);
				break;
			default:
				echo "Too many arguments";
				return null;
				break;
		}
	}

}
