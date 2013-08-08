<?php

defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__) . '/' . 'helper.php');
$detail = modDetailHelper::getDetailBody();
$modulename = $module->title;
if (isset($detail)){
foreach ($detail as $key => $value) {
    if ($key == $modulename) {
        $detailbody = $detail[$key];
    }
}
}
require( JModuleHelper::getLayoutPath('mod_jevents_detail') );
?>