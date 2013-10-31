<?php

defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__) . '/' . 'helper.php');

$moddata = modDetailHelper::getDetailBody($params->get("dynmodid", ""));
if ($moddata) {
	require( JModuleHelper::getLayoutPath('mod_jevents_custom') );
}
