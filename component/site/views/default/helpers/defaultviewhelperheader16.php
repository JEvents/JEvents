<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.application.module.helper');

function DefaultViewHelperHeader16($view)
{

	$input = Factory::getApplication()->input;

	$task = $input->getString('jevtask', '');
	$view->loadModules("jevprejevents");
	$view->loadModules("jevprejevents_" . $task);

	Factory::getApplication()->triggerEvent('onJEventsHeader', array($view));

	$cfg     = JEVConfig::getInstance();
	$version = JEventsVersion::getInstance();
	$jevtype = $input->get('jevtype', null, null);
	$evid    = $input->getInt('evid', '');
	$pop     = $input->getInt('pop', '0');
	$params  = ComponentHelper::getParams(JEV_COM_COMPONENT);

	$view->copyrightComment();

	// stop crawler and set meta tag
	JEVHelper::checkRobotsMetaTag();

	// Call the MetaTag setter function.
	JEVHelper::SetMetaTags();

	$lang = Factory::getLanguage();
	?>
	<div id="jevents">
	<div class="contentpaneopen jeventpage<?php echo $params->get('pageclass_sfx'); ?>  jevbootstrap"
	     id="jevents_header">
		<?php if ($params->get('show_page_heading', 0)) : ?>
			<h1>
				<?php echo $view->escape($params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>
		<?php
		$t_headline = '';
		switch ($cfg->get('com_calHeadline', 'comp'))
		{
			case 'none':
				$t_headline = '';
				break;
			case 'menu':
				$menu2 = Factory::getApplication()->getMenu();
				$menu  = $menu2->getActive();
				if (isset($menu) && isset($menu->title))
				{
					$t_headline = $menu->title;
				}
				break;
			default:
				$t_headline = Text::_('JEV_EVENT_CALENDAR');
				break;
		}
		if ($t_headline != "")
		{
			?>
			<h2 class="contentheading gsl-h2"><?php echo $t_headline; ?></h2>
			<?php
		}
		$task = $input->getString('jevtask', '');
		ob_start();
		$view->information16();
		$info = ob_get_clean();

		if ($cfg->get('com_print_icon_view', 1) || $cfg->get('com_email_icon_view', 1) || strpos($info, "<li>") !== false)
		{
			?>
			<ul class="actions">
				<?php
				if ($cfg->get('com_print_icon_view', 1))
				{
					$print_link = 'index.php?option=' . JEV_COM_COMPONENT
						. '&task=' . $task
						. ($evid ? '&evid=' . $evid : '')
						. ($jevtype ? '&jevtype=' . $jevtype : '')
						. ($view->year ? '&year=' . $view->year : '')
						. ($view->month ? '&month=' . $view->month : '')
						. ($view->day ? '&day=' . $view->day : '')
						. $view->datamodel->getItemidLink()
						. $view->datamodel->getCatidsOutLink()
						. '&print=1'
						. '&pop=1'
						. '&tmpl=component';
					$print_link = Route::_($print_link);

					if ($pop)
					{ ?>
						<li class="print-icon">
							<a href="javascript:void(0);" rel="nofollow"
							   onclick="window.print(); return false;"
							   title="<?php echo Text::_('JEV_CMN_PRINT'); ?>">
								<span class="icon-print"> </span>
							</a>
						</li> <?php
					}
					else
					{ ?>
						<li class="print-icon">
							<a href="javascript:void(0);" rel="nofollow"
							   onclick="window.open('<?php echo $print_link; ?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=600,height=600,directories=no,location=no');"
							   title="<?php echo Text::_('JEV_CMN_PRINT'); ?>">
								<span class="icon-print"> </span>
							</a>
						</li> <?php
					}
				}
				$jversion = new Joomla\CMS\Version;
				if ($cfg->get('com_email_icon_view', 1) && !$jversion->isCompatible('4.0'))
				{

					$task = $input->getString('jevtask', '');
					$link = 'index.php?option=' . JEV_COM_COMPONENT
						. '&task=' . $task
						. ($evid ? '&evid=' . $evid : '')
						. ($jevtype ? '&jevtype=' . $jevtype : '')
						. ($view->year ? '&year=' . $view->year : '')
						. ($view->month ? '&month=' . $view->month : '')
						. ($view->day ? '&day=' . $view->day : '')
						. $view->datamodel->getItemidLink()
						. $view->datamodel->getCatidsOutLink();
					$link = Route::_($link);
					//if (strpos($link,"/")===0) $link = StringHelper::substr($link,1);
					$uri  = Uri::getInstance(Uri::base());
					$root = $uri->toString(array('scheme', 'host', 'port'));

					$link = $root . $link;
					require_once(JPATH_SITE . '/' . 'components' . '/' . 'com_mailto' . '/' . 'helpers' . '/' . 'mailto.php');
					$url = Route::_('index.php?option=com_mailto&tmpl=component&link=' . MailToHelper::addLink($link));

					?>
					<li class="email-icon">
						<a href="javascript:void(0);" rel="nofollow"
						   onclick="window.open('<?php echo $url; ?>','emailwin','width=400,height=350,menubar=yes,resizable=yes'); return false;"
						   title="<?php echo Text::_('EMAIL'); ?>">
							<span class="icon-envelope"> </span>
						</a>
					</li>
					<?php
				}
				echo $info;
				?>
			</ul>
			<?php

		}
		?>
	</div>
	<?php
	$view->loadModules("jevprejevents2");
	$view->loadModules("jevprejevents2_" . $task);
	?>
<div class="contentpaneopen  jeventpage<?php echo $params->get('pageclass_sfx'); ?>  jevbootstrap" id="jevents_body">
	<?php
}
