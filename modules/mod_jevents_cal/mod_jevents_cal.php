<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: mod_jevents_cal.php 3143 2011-12-29 10:15:10Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Calendar
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */
defined('_JEXEC') or die ('Restricted access');

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

use Joomla\CMS\Language\Associations;
use Joomla\CMS\Factory;

if (version_compare(JVERSION, '4.0.1', 'lt'))
{
    ?>
    <h3>This version of JEvents is designed for Joomla 4.x and later.</h3>
    <?php
    return;
}


require_once(dirname(__FILE__) . '/' . 'helper.php');

$jevhelper = new modJeventsCalHelper ();

PluginHelper::importPlugin("jevents");

// record what is running - used by the filters
$registry = JevRegistry::getInstance("jevents");
$registry->set("jevents.activeprocess", "mod_jevents_cal");
$registry->set("jevents.moduleid", $module->id);
$registry->set("jevents.moduleparams", $params);

// See http://www.php.net/manual/en/timezones.php
$compparams = ComponentHelper::getParams(JEV_COM_COMPONENT);
$tz         = $compparams->get("icaltimezonelive", "");
if ($tz != "" && is_callable("date_default_timezone_set"))
{
	$timezone = date_default_timezone_get();
	// echo "timezone is ".$timezone."<br/>";
	date_default_timezone_set($tz);
	$registry->set("jevents.timezone", $timezone);
}

$theme    = JEV_CommonFunctions::getJEventsViewName();
$modtheme = $params->get("com_calViewName", $theme);
if ($modtheme == "global" || $modtheme == "")
{
	$modtheme = $theme;
}
$theme = $modtheme;
/*
$menu     = Factory::getApplication()->getMenu();
$lang = Factory::getLanguage();

// 307 308 339 340
foreach (array(Factory::getApplication()->input->getInt('Itemid'), 307, 308, 339, 340) as $Itemid)
{
    if ( Associations::isEnabled() )
    {
        $associations = Associations::getAssociations( 'com_menus', '#__menu', 'com_menus.item', (int) $Itemid, 'id', '', '' );
        $menuitem     = $menu->getItem( $Itemid );
        if ( $menuitem->language === $lang->getTag() )
        {
            echo "Original $Itemid for " . $menuitem->language . "<Br>";
        }
        else if ( array_key_exists( $lang->getTag(), $associations ) )
        {
            echo "Original was $Itemid which is " . $menuitem->language . " translates to " . $associations[$lang->getTag()]->id . " which is " . $associations[$lang->getTag()]->language . "<Br>";
        }
        else
        {
            echo "Problem menu item lang is " . $menuitem->language . "<br>";
        }
        echo "<br><br>";
    }
}
*/

$viewclass          = $jevhelper->getViewClass($theme, 'mod_jevents_cal', $theme . '/' . "calendar", $params);
$modview            = new $viewclass ($params, $module->id);
$modview->jevlayout = $theme;
echo $modview->getCal();

// Must reset the timezone back!!
if ($tz && is_callable("date_default_timezone_set"))
{
	date_default_timezone_set($timezone);
}
