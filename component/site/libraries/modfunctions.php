<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: modfunctions.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// functions used by the modules

defined( '_JEXEC' ) or die( 'Restricted access' );

function findAppropriateMenuID (&$catidsOut, &$modcatids, &$catidList, $modparams, &$showall){
	// Itemid, search for menuid with lowest access rights
	$user = JFactory::getUser();
	$db	= JFactory::getDBO();
	$jinput = JFactory::getApplication()->input;

	// Do we ignore category filters?
	$ignorecatfilter = 0;
	if (isset($modparams->ignorecatfilter) && $modparams->ignorecatfilter){
		$ignorecatfilter = $modparams->ignorecatfilter;
		$jinput->set("category_fv", 0);
	}
	
	$menu = JFactory::getApplication()->getMenu();
	$menuitems = $menu->getItems("component",JEV_COM_COMPONENT);
	// restrict this list to those accessible by the user
	if (!is_null($menuitems)){
		foreach ($menuitems as $index=>$menuitem) {
			if (version_compare(JVERSION, '1.6.0', '>=') ? !in_array($menuitem->access,JEVHelper::getAid($user, 'array')) : JEVHelper::getAid($user)<$menuitem->access){
				unset($menuitems[$index]);
			}
			// also drop admin functions
			else if (array_key_exists("task",$menuitem->query) && strpos($menuitem->query['task'],'admin')===0){
				unset($menuitems[$index]);
			}
		}
	}
	else {
		$menuitems = array();
	}
	$activeMenu = $menu->getActive();

	if (isset($modparams->target_itemid) && $modparams->target_itemid != '' && intval($modparams->target_itemid)>0){
		$targetid = intval($modparams->target_itemid);

		$myItemid = 0;
		foreach ($menuitems as $item) {
			if ($targetid == $item->id) {
				$myItemid = $targetid;
				break;
			}
		}
	}
	else if ($activeMenu && $activeMenu->component==JEV_COM_COMPONENT){

		// use result by reference
		$myItemid = $activeMenu->id;
	}
	else {
		if (count($menuitems)>0){
			reset($menuitems);
			$myItemid = current($menuitems);
			$myItemid = $myItemid->id;
		}
		else {
			// if no menu pointing the JEvents use itemid of home page and set empty menu array
			$myItemid = 1;
			$menuitems = array();
		}
	}
	
	// put the best guess first for checking category selections
	if ($myItemid>0){
		$newmenuitems =array();
		foreach ($menuitems as $item) {
			if ($myItemid == $item->id) {
				array_unshift($newmenuitems, $item);
			}
			else {
				array_push($newmenuitems, $item);
			}
		}
		$menuitems = $newmenuitems;
	}

	//Finds the first enclosing setof catids from menu item if it exists !
	//
	// First of all get the module paramaters
	$c=0;
	$modcatids = array();
	$catidList = "";
	// New system
	$newcats = isset($modparams->catidnew)?$modparams->catidnew: false;
	if ($newcats && is_array($newcats )){
		foreach ($newcats as $newcat){
			if ( !in_array( $newcat,$modcatids )){
				$modcatids[]=$newcat;
				$catidList .= (JString::strlen($catidList)>0?",":"").$newcat;
			}
		}				
	}
	else {	
		for ($c=0;$c<999;$c++){
			$nextCID="catid$c";
			//  stop looking for more catids when you reach the last one!
			if (!isset($modparams->$nextCID)) break;
			if ($modparams->$nextCID>0 && !in_array($modparams->$nextCID,$modcatids)){
				$modcatids[]=$modparams->$nextCID;
				$catidList .= (JString::strlen($catidList)>0?",":"").$modparams->$nextCID;
			}
		}
	}

	if (count($modcatids)==0){
		$showall = true;
	}
	$catidsOut = str_replace(",","|",$catidList);

	$params	=  JComponentHelper::getParams(JEV_COM_COMPONENT);
	$separator = $params->get("catseparator","|");
	$catidsOut = str_replace("|", $separator,$catidsOut);

	// Now check the catids from the URL
	$catidsin = JRequest::getString("catids","");
	// if ignoring catid filter then force to blank
	if ($ignorecatfilter) $catidsin = "";
	
	if (JString::strlen($catidsin)>0){
		$catidsin = explode("|",$catidsin);
		JArrayHelper::toInteger($catidsin);
	}
	else {
		// if no catids from the URL then stick to the module catids
		$catidsin = $modcatids;
	}

	// if  we have no modcatids then the enclosing set MUST be all categories and catids must therefore also be empty!
	if (count($modcatids)==0){
		if (count($catidsin)>0){
			$modcatids=$catidsin;
		}
		//return $myItemid;
	}
	else {
		foreach ($modcatids as $key=>$modcatid) {
			if (!in_array($modcatid,$catidsin)){
				unset($modcatids[$key]);
			}
		}
		// if we have no categories left then we have to force the result set to be empty so add a -1 category
		// since accessibleCategoryList uses a count of zero catids to allow them all!
		if (count($modcatids)==0){
			$modcatids[]=-1;
		}
	}
	$catidsOut=implode("|",$modcatids);
	$catidList=implode(",",$modcatids);
	$catidsOut = str_replace("|", $separator,$catidsOut);

	if ($myItemid == 0){
		// User has specified a non JEvents menu so catid filters won't work
		$myItemid = intval($modparams->target_itemid);
		return $myItemid;
	}
	
	// if we are not inside a real module i.e. in the legend beneath the calendar we just use the Itemid we are currently on
	if ((!isset($modparams->target_itemid) || $modparams->target_itemid=="") && $activeMenu && $activeMenu->component==JEV_COM_COMPONENT){
		return $activeMenu->id;
	}

	// now find an appropriate enclosing set and associated menu item
	$possibleset = array();
	foreach ($menuitems as $testparms) {
		$test = new JRegistry( $testparms->params);
		$c=0;
		$catids = array();
		// New system
		$newcats = $test->get("catidnew", false);
		if ($newcats && is_array($newcats )){
			foreach ($newcats as $newcat){
				if ( !in_array( $newcat,$catids )){
					$catids[]=$newcat;
				}
			}				
		}
		else {			
			while ($nextCatId = $test->get( "catid$c", null )){
				if (!in_array($nextCatId,$catids)){
					$catids[]=$nextCatId;
				}
				$c++;
			}
		}
		// Now check if its an enclosing set of catids so we use this one if the targetid has not been explicitly set
		if (count($catids)==0 && !isset($targetid) && !($activeMenu && $activeMenu->component==JEV_COM_COMPONENT)) {
			$Itemid = JEVHelper::getItemid();
			$myItemid = intval($testparms->id);
			return $myItemid;
			break;
		}
		else {
			// if  we have no modcatids then the enclosing set MUST be all categories and catids must therefore also be empty!
			if (count($modcatids)>0){
				$enclosed = true;
				foreach ($modcatids as $cid){
					if (!in_array($cid,$catids)) {
						$enclosed = false;
					}
				}
				// if enclosed or menu item is not constrained
				if ($enclosed) {
					$possibleset[]=intval($testparms->id);
					break;
				}
				else if ( count($catids)==0){
					$possibleset[]=intval($testparms->id);
				}
			}
		}
	}

	if (in_array($myItemid,$possibleset)){
		return $myItemid;
	}
	else if (count($possibleset)>0){
		return $possibleset[0];
	}
	return $myItemid;
}
