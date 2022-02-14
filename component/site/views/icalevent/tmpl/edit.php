<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

if (!isset($this->jevviewdone))
{
	HTMLHelper::_('stylesheet', 'jui/icomoon.css', array('version' => 'auto', 'relative' => true));
	$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

    if ($params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($params->get('page_heading')); ?>
    </h1>
    <?php endif;

	if ($params->get("newfrontendediting", 1))
	{
		echo LayoutHelper::render('gslframework.header', null, JPATH_COMPONENT_ADMINISTRATOR . "/layouts");
	}

	$this->loadModules("jevpreeditevent");

/*
	echo "<pre>";
	debug_print_backtrace();
	echo "</pre>";
*/
	include_once(JEV_ADMINPATH . "/views/icalevent/tmpl/" . basename(__FILE__));

	/*
	$bar = JToolBar::getInstance('toolbar');
	$barhtml = $bar->render();
	$barhtml = str_replace('id="','id="x', $barhtml);
	echo $barhtml;
	 */
	$this->jevviewdone = true;

	$this->loadModules("jevposteditevent");

	if ($params->get("newfrontendediting", 1))
	{
		echo LayoutHelper::render('gslframework.footer', null, JPATH_COMPONENT_ADMINISTRATOR . "/layouts");
	}
}