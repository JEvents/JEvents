<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

/*
GWE mods
1. remove sidebar using JS
2. gsl wrapper
		echo LayoutHelper::render('gslframework.header');

		echo LayoutHelper::render('gslframework.footer');
3. use Joomla\CMS\Layout\LayoutHelper;
*/

echo LayoutHelper::render('gslframework.header', null, JPATH_ADMINISTRATOR. "/components/com_jevents/layouts" );

// Not sure why thse are not loaded by com_fields
// Load the jQuery plugin && CSS
JHtml::_('script', 'jui/jquery.searchtools.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'jui/jquery.searchtools.css', array('version' => 'auto', 'relative' => true));

$jversion = new JVersion;

if ($jversion->isCompatible('4.0'))
{
	include(JPATH_COMPONENT_ADMINISTRATOR . "/tmpl/groups/default.php");
}
else
{
	include(JPATH_COMPONENT_ADMINISTRATOR . "/views/groups/tmpl/default.php");
}


echo LayoutHelper::render('gslframework.footer', null, JPATH_ADMINISTRATOR. "/components/com_jevents/layouts" );
