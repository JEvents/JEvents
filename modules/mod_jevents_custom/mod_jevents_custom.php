<?php

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\ModuleHelper;

if (version_compare(JVERSION, '4.0.1', 'lt'))
{
    ?>
    <h3>This version of JEvents is designed for Joomla 4.x and later.</h3>
    <?php
    return;
}

require_once(dirname(__FILE__) . '/' . 'helper.php');

$moddata = modDetailHelper::getDetailBody($params->get("dynmodid", ""));
if ($moddata)
{
	require(ModuleHelper::getLayoutPath('mod_jevents_custom'));
}
