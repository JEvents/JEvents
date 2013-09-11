<?php

defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__) . '/' . 'helper.php');
$jevhelper = new modJeventsCreateHelper();
$createtitle = $params->get('event_name', '');
$createdesc = $params->get('event_desc', '');
$createcat = $params->get('catidnew', 0);
$createauto = "";
if (!$params->get('autosubmit', 0)) {
    $createauto = "//";
}
require( JModuleHelper::getLayoutPath('mod_jevents_create') );
?>