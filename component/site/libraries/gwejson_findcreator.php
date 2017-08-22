<?php
/**
* @copyright	Copyright (C) 2015-2017 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/

use Joomla\String\StringHelper;

function ProcessJsonRequest(&$requestObject, $returnData){

	$returnData->titles	= array();
	$returnData->exactmatch=false;

	ini_set("display_errors",0);

	include_once(JPATH_SITE."/components/com_jevents/jevents.defines.php");

	$token = JSession::getFormToken();;
	if ((isset($requestObject->token)  &&  $requestObject->token!=$token) || JFactory::getApplication()->input->get('token', '', 'string')!= $token) {
		PlgSystemGwejson::throwerror("There was an error - bad token.  Please refresh the page and try again.");
	}

	$user = JFactory::getUser();
	if ($user->id==0){
		PlgSystemGwejson::throwerror("There was an error");
	}

	// If user is jevents can deleteall or has backend access then allow them to specify the creator
	$jevuser = JEVHelper::getAuthorisedUser();
	$user = JFactory::getUser();
	//$access = JAccess::check($user->id, "core.deleteall", "com_jevents");
	$access = $user->authorise('core.admin', 'com_jevents') || $user->authorise('core.deleteall', 'com_jevents');

	$db = JFactory::getDBO();
	if (!($jevuser && $jevuser->candeleteall) &&  !$access)
	{
		PlgSystemGwejson::throwerror("There was an error - no access");
	}

	if ($requestObject->error){
		return "Error";
	}
	if (isset($requestObject->typeahead) && trim($requestObject->typeahead)!==""){
		$returnData->result = "title is ".$requestObject->typeahead;
	}
	else {
		PlgSystemGwejson::throwerror ( "There was an error - no valid argument");
	}

	$db = JFactory::getDBO();

	$title = JFilterInput::getInstance()->clean($requestObject->typeahead,"string");
	$text  = $db->Quote( '%'.$db->escape( $title, true ).'%', false );

	// Remove any dodgy characters from fields
	// Only allow a to z , 0 to 9, ', " space (\\040), hyphen (\\-), underscore (\\_)
	/*
	$regex     = '/[^a-zA-Z0-9_\'\"\'\\40\\-\\_]/';
	$title    = preg_replace($regex, "", $title);
	$title = JString::substr($title."    ",0,4);
	*/

	if (trim($title)=="" && trim($title)==""){
		PlgSystemGwejson::throwerror ( "There was an error - no valid argument");
	}

	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	$authorisedonly = $params->get("authorisedonly", 0);
	// if authorised only then load from database
	if ($authorisedonly)
	{
		$sql = "SELECT  ju.*  FROM #__jev_users AS tl ";
		$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
		$sql .= " WHERE tl.cancreate=1 and ju.username LIKE ($text) OR ju.name LIKE ($text) ";
		$sql .= " ORDER BY ju.name ASC";
		$sql .= " LIMIT 500";
		$db->setQuery($sql);
		$matches = $db->loadObjectList();
	}
	else
	{
		$rules = JAccess::getAssetRules("com_jevents", true);
		$creatorgroups = $rules->getData();
		// need to merge the arrays because of stupid way Joomla checks super user permissions
		//$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups["core.create"]->getData());
		// use union orf arrays sincee getData no longer has string keys in the resultant array
		//$creatorgroups = $creatorgroups["core.admin"]->getData()+ $creatorgroups["core.create"]->getData();
		// use union orf arrays sincee getData no longer has string keys in the resultant array
		$creatorgroupsdata = $creatorgroups["core.admin"]->getData();
		// take the higher permission setting
		foreach ($creatorgroups["core.create"]->getData() as $creatorgroup => $permission)
		{
			if ($permission)
			{
				$creatorgroupsdata[$creatorgroup] = $permission;
			}
		}

		$userids = array(0);
		foreach ($creatorgroupsdata as $creatorgroup => $permission)
		{
			if ($permission == 1)
			{
				$userids = array_merge(JAccess::getUsersByGroup($creatorgroup, true), $userids);
			}
		}
		$sql = "SELECT * FROM #__users "
				. "where id IN (" . implode(",", array_values($userids)) . ")  and username LIKE ($text) OR name LIKE ($text)  and block=0 "
				. "ORDER BY name asc LIMIT 500";
		$db->setQuery($sql);
		$matches = $db->loadObjectList();

	}

	if (count($matches)==0){
		$returnData = array();
	}
	else {
		$returnData = array();
		foreach ($matches as $match) {
			$result = new stdClass();
			$result->title = $match->name. " (".$match->username.")";
			$result->creator_id = $match->id;
			$returnData[] = $result;
		}
	}

	return $returnData;
}

