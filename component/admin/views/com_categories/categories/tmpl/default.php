<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Version;
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

$jversion = new Version;
if ($jversion->isCompatible('4.0'))
{
	Factory::getDbo()->setQuery("SELECT * FROM #__categories WHERE extension='com_jevents'");
	$categories = Factory::getDbo()->loadObjectList('id');
	$style = "";
	foreach ($categories as $cat)
	{
		$catparams = new JevRegistry($cat->params);
		if ($catparams->get("catcolour"))
		{
			$style .= "tr[data-item-id='$cat->id'] .cat-title a {  border-left:solid 3px  " . $catparams->get("catcolour") . ";padding-left:5px;}\n";
		}
	}
	Factory::getApplication()->getDocument()->addStyleDeclaration($style);


	include(JPATH_COMPONENT_ADMINISTRATOR . "/tmpl/categories/default.php");
}
else
{
	include(JPATH_COMPONENT_ADMINISTRATOR . "/views/categories/tmpl/default.php");
}

echo LayoutHelper::render('gslframework.footer', null, JPATH_ADMINISTRATOR. "/components/com_jevents/layouts" );
