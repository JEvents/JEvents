<?php

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\ModuleHelper;

require_once(dirname(__FILE__) . '/' . 'helper.php');

$moddata = modDetailHelper::getDetailBody($params->get("dynmodid", ""));
if ($moddata)
{
	require(ModuleHelper::getLayoutPath('mod_jevents_custom'));
}
