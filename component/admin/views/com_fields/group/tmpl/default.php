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

$jversion = new JVersion;

if ($jversion->isCompatible('4.0'))
{
	include(JPATH_COMPONENT_ADMINISTRATOR . "/tmpl/group/edit.php");
}
else
{
	include(JPATH_COMPONENT_ADMINISTRATOR . "/views/group/tmpl/edit.php");
}


echo LayoutHelper::render('gslframework.footer', null, JPATH_ADMINISTRATOR. "/components/com_jevents/layouts" );
