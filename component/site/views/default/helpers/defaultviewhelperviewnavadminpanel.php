<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: defaultviewhelperviewnavadminpanel.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

function DefaultViewHelperViewNavAdminPanel($view)
{

	$cfg = JEVConfig::getInstance();

	if ($cfg->get('show_adminpanel', 1) == 1)
	{
		$is_event_editor = JEVHelper::isEventCreator();
		$user            = Factory::getUser();
		$input          = Factory::getApplication()->input;

		JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");

		JLoader::register('jevFilterProcessing', JEV_PATH . "/libraries/filters.php");
		$pluginsDir = JPATH_ROOT . '/' . 'plugins' . '/' . 'jevents';
		$filters    = jevFilterProcessing::getInstance(array("published", "justmine", "category", "reset"));

		$cfg = JEVConfig::getInstance();

		if ($input->getInt('pop', 0))
			return;

		if (strpos($cfg->get('framework', 'bootstrap'), 'uikit') === 0)
		{

			if ($is_event_editor)
			{
				// Load event adding language string
				Text::script('JEV_ADD_EVENT');
				Text::script('JEV_IMPORT_ICALEVENT');
				?>
				<div class="uk-card uk-card-default uk-card-body">
					<div  class="uk-button-group">
						<?php
						$editLink = Route::_('index.php?option=' . JEV_COM_COMPONENT
							. '&task=icalevent.edit' . '&year=' . $view->year . '&month=' . $view->month . '&day=' . $view->day
							. '&Itemid=' . $view->Itemid, true);
						$popup    = false;
						$params   = ComponentHelper::getParams(JEV_COM_COMPONENT);
						if ($params->get("editpopup", 0) && JEVHelper::isEventCreator())
						{
							JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
							JevModal::framework();

							$popup = true;
						}
						$eventlinkadd = $popup ? "javascript:jevModalNoHeader('myEditModal','" . $editLink . "');" : $editLink;
						?>
						<a href="<?php echo $eventlinkadd; ?>" title="<?php echo Text::_('JEV_ADDEVENT'); ?>" class="uk-button uk-button-small uk-button-primary">
							<b><span class="uk-icon uk-margin-small-right" data-uk-icon="icon:plus;ratio:0.8;"></span><?php echo Text::_('JEV_ADDEVENT'); ?></b>
						</a>
						<?php
						// offer frontend import ?
						if ($params->get("feimport", 0))
						{
							$importLink = Route::_('index.php?option=' . JEV_COM_COMPONENT
								. '&task=icals.importform&tmpl=component&Itemid=' . $view->Itemid, true);

							JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
							JevModal::framework();

							$eventimport = "javascript:jevModalPopup('myImportModal','" . $importLink . "', '" .  Text::_('JEV_IMPORT_ICALEVENT', true) . "');";
							?>
							<a href="<?php echo $eventimport; ?>" class="uk-button uk-button-small uk-button-secondary"
							   title="<?php echo Text::_('JEV_IMPORT_ICALEVENT', true); ?>">
								<b><span class="uk-icon uk-margin-small-right" data-uk-icon="icon:pull;ratio:0.8;"></span><?php echo Text::_('JEV_IMPORT_ICALEVENT'); ?></b>
							</a>
							<?php
						}
						?>
					</div>
					<div class="uk-margin-top">
						<?php

						if ($user->id > 0)
						{

							$app        = Factory::getApplication();
							$input      = $app->input;

							$datamodel = new JEventsDataModel();
							// find appropriate Itemid and setup catids for datamodel
							$myItemid = JEVHelper::getItemid();
							$datamodel->setupComponentCatids();

							list($year, $month, $day) = JEVHelper::getYMD();
							$evid    = $input->getInt("evid", false);
							$jevtype = $input->get("jevtype", false);
							// FORM for filter submission
							$form_link = Route::_(
								'index.php?option=' . JEV_COM_COMPONENT
								. '&task=' . $input->getCmd("jevtask", "month.calendar")
								. ($evid ? '&evid=' . $evid : '')
								. ($jevtype ? '&jevtype=' . $jevtype : '')
								. ($year ? '&year=' . $year : '')
								. ($month ? '&month=' . $month : '')
								. ($day ? '&day=' . $day : '')
								. "&Itemid=" . $myItemid
								, false);
							?>
							<form action="<?php echo $form_link; ?>" method="post" class="uk-form uk-form-horizontal">
								<?php
								$filterHTML = $filters->getFilterHtmlUIkit();

								$Itemid = JEVHelper::getItemid();

								foreach ($filterHTML as $filter)
								{
									if (empty($filter["title"]))
									{
										echo "<div class='uk-margin-small-bottom'>" . $filter["html"] . "</div>";
									}
									else
									{
										echo "<div class='uk-margin-small-bottom'><div class='uk-form-label'>" . $filter["title"] . "</div><div class='uk-form-controls'>" . $filter["html"] . "</div></div>";
									}
								}
								/*
								  $eventmylinks = Route::_( 'index.php?option=' . JEV_COM_COMPONENT . '&task=admin.listevents'
								  . '&year=' . $view->year . '&month=' . $view->month . '&day=' . $view->day
								  . '&Itemid=' . $view->Itemid ); ?>
								  <a href="<?php echo $eventmylinks; ?>" title="<?php echo Text::_('JEV_MYEVENTS'); ?>">
								  <b><?php echo Text::_('JEV_MYEVENTS'); ?></b>
								  </a>
								  <?php
								 */
								?>
							</form>
						<?php }
						?>
					</div>
				</div>
				<?php
			}
		}
		else
		{

			if ($is_event_editor)
			{
				if ($cfg->get("bootstrapchosen", 1))
				{
					$jversion = new Joomla\CMS\Version;
					if (!$jversion->isCompatible('4.0'))
					{
						// Load Bootstrap
						JevHtmlBootstrap::framework();
						HTMLHelper::_('formbehavior.chosen', '#jevents select:not(.notchosen)');
					}
				}
				JevHtmlBootstrap::loadCss();

				// Load event adding language string
				Text::script('JEV_ADD_EVENT');
				Text::script('JEV_IMPORT_ICALEVENT');
				?>
				<div class="ev_adminpanel">
					<div align="left" class="nav_bar_cell">
						<?php
						$editLink = Route::_('index.php?option=' . JEV_COM_COMPONENT
							. '&task=icalevent.edit' . '&year=' . $view->year . '&month=' . $view->month . '&day=' . $view->day
							. '&Itemid=' . $view->Itemid, true);
						$popup    = false;
						$params   = ComponentHelper::getParams(JEV_COM_COMPONENT);
						if ($params->get("editpopup", 0) && JEVHelper::isEventCreator())
						{
							JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
							JevModal::framework();

							$popup = true;
						}
						$eventlinkadd = $popup ? "javascript:jevModalNoHeader('myEditModal','" . $editLink . "');" : $editLink;
						?>
						<a href="<?php echo $eventlinkadd; ?>" title="<?php echo Text::_('JEV_ADDEVENT'); ?>">
							<b><?php echo Text::_('JEV_ADDEVENT'); ?></b>
						</a>
						<?php
						// offer frontend import ?
						if ($params->get("feimport", 0))
						{
							$importLink = Route::_('index.php?option=' . JEV_COM_COMPONENT
								. '&task=icals.importform&tmpl=component&Itemid=' . $view->Itemid, true);

							JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
							JevModal::framework();

							$eventimport = "javascript:jevModalPopup('myImportModal','" . $importLink . "', '" .  Text::_('JEV_IMPORT_ICALEVENT', true) . "');";
							?>
							<br/><a href="<?php echo $eventimport; ?>"
							        title="<?php echo Text::_('JEV_IMPORT_ICALEVENT', true); ?>">
							<b><?php echo Text::_('JEV_IMPORT_ICALEVENT'); ?></b>
						</a>
							<?php
						}

						if ($user->id > 0)
						{

							$app        = Factory::getApplication();
							$input      = $app->input;

							$datamodel = new JEventsDataModel();
							// find appropriate Itemid and setup catids for datamodel
							$myItemid = JEVHelper::getItemid();
							$datamodel->setupComponentCatids();

							list($year, $month, $day) = JEVHelper::getYMD();
							$evid    = $input->getInt("evid", false);
							$jevtype = $input->get("jevtype", false);
							// FORM for filter submission
							$form_link = Route::_(
								'index.php?option=' . JEV_COM_COMPONENT
								. '&task=' . $input->getCmd("jevtask", "month.calendar")
								. ($evid ? '&evid=' . $evid : '')
								. ($jevtype ? '&jevtype=' . $jevtype : '')
								. ($year ? '&year=' . $year : '')
								. ($month ? '&month=' . $month : '')
								. ($day ? '&day=' . $day : '')
								. "&Itemid=" . $myItemid
								, false);
							?>
							<form action="<?php echo $form_link; ?>" method="post">
								<?php
								$filterHTML = $filters->getFilterHTML();

								$Itemid = JEVHelper::getItemid();

								foreach ($filterHTML as $filter)
								{
									echo "<div>" . $filter["title"] . " " . $filter["html"] . "</div>";
								}
								/*
								  $eventmylinks = Route::_( 'index.php?option=' . JEV_COM_COMPONENT . '&task=admin.listevents'
								  . '&year=' . $view->year . '&month=' . $view->month . '&day=' . $view->day
								  . '&Itemid=' . $view->Itemid ); ?>
								  <a href="<?php echo $eventmylinks; ?>" title="<?php echo Text::_('JEV_MYEVENTS'); ?>">
								  <b><?php echo Text::_('JEV_MYEVENTS'); ?></b>
								  </a>
								  <?php
								 */
								?>
							</form>
						<?php }
						?>
					</div>
				</div>
				<?php
			}
		}
	}

}
