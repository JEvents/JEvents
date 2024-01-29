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


if (version_compare(JVERSION, '4.0', 'ge'))
{
	Factory::getDbo()->setQuery("SELECT * FROM #__categories WHERE extension='com_jevents'");
	$categories = Factory::getDbo()->loadObjectList('id');
	$style = "";
	foreach ($categories as $cat)
	{
		$catparams = new JevRegistry($cat->params);
		if ($catparams->get("catcolour"))
		{
			$style .= "tr[data-item-id='$cat->id'] .cat-title a, tr[data-item-id='$cat->id'] th a {  border-left:solid 3px  " . $catparams->get("catcolour") . ";padding-left:5px;}\n";
		}
	}
	Factory::getApplication()->getDocument()->addStyleDeclaration($style);

	ob_start();
	include(JPATH_COMPONENT_ADMINISTRATOR . "/tmpl/categories/default.php");
	$output = ob_get_clean();

	$output = str_replace("btn ", "gsl-button gsl-button-small ", $output);
	$output = str_replace("btns ", " ", $output);
	$output = str_replace(array('btn-success','badge-danger',
        'badge','bg-secondary',
        'btn-dark', 'btn-secondary'),
        array('gsl-button-primary', 'gsl-button-danger',
        'gsl-button gsl-button-small','gsl-button-secondary',
        'gsl-button gsl-button-secondary', 'gsl-button  gsl-button-default'), $output);
	$output = str_replace(
        array("option=com_jevents", "filter[category_id]", "filter[published]"),
        array("option=com_jevents&task=icalevent.list", "filter[catid]", "filter[published]"),
        $output);
	echo $output;
}
else
{
	ob_start();
	include(JPATH_COMPONENT_ADMINISTRATOR . "/views/categories/tmpl/default.php");
	$output = ob_get_clean();
	$output = str_replace("badge ", "gsl-button gsl-button-small ", $output);
        $output = str_replace("btns ", " ", $output);
        $output = str_replace(array('badge-success','badge-important', 'badge-info', 'badge-inverse'), array('gsl-button-primary', 'gsl-button-secondary', 'gsl-button-danger', 'gsl-button-danger'), $output);

        $output = str_replace(
            array("filter[category_id]", "filter[published]", "filter[level]"),
            array("filter[catid]", "filter[published]", "filter[showpast]"),
            $output);

	echo $output;
}

echo LayoutHelper::render('gslframework.footer', null, JPATH_ADMINISTRATOR. "/components/com_jevents/layouts" );
