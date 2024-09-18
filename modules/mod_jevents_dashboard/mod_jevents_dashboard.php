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

require(ModuleHelper::getLayoutPath('mod_jevents_dashboard'));
