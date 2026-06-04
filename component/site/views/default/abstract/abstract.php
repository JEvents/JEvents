<?php
/**
 * @package     JEvents
 */
defined('_JEXEC') or die();

// Class body moved to component/site/libraries/src/Site/View/DefaultView.php
// This file is kept for extensions that include it directly.
if (!class_exists('JEvents\\Site\\View\\DefaultView', false))
{
    require_once JPATH_SITE . '/components/com_jevents/libraries/src/Site/View/DefaultView.php';
}

if (!class_exists('JEventsDefaultView', false))
{
    class_alias('JEvents\\Site\\View\\DefaultView', 'JEventsDefaultView');
}
