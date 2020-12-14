<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 2749 2011-10-13 08:54:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminIcalrepeatViewIcalrepeat extends JEventsAbstractView
{

	function overview($tpl = null)
	{

		$document = Factory::getDocument();
		$document->setTitle(Text::_('ICAL_EVENT_REPEATS'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('ICAL_EVENT_REPEATS'), 'jevents');

		JToolbarHelper::addNew('icalrepeat.addRepeat', 'Add repeat');
		JToolbarHelper::editList('icalrepeat.edit');
		JToolbarHelper::deleteList('Delete this repeat?', 'icalrepeat.delete');
		JToolbarHelper::cancel('icalevent.list');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// This is actually HIDE PAST so switch boolean values
		$showpast = intval($this->getModel()->getState("filter.showpast", 0));
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '0', JText::_('JEV_NO'));
		$options[] = HTMLHelper::_('select.option', '1', JText::_('JEV_YES'));

		$this->filters =
			array('showpast' =>
				       Text::_('JEV_SHOW_PAST') . " " .  HTMLHelper::_('select.genericlist', $options, 'filter[showpast]', 'class="gsl-select"  onchange="document.adminForm.submit();"', 'value', 'text', $showpast)
			);

	}

	function edit($tpl = null)
	{

		$document    = Factory::getDocument();
		$editStrings = '';

		include(JEV_ADMINLIBS . "editStrings.php");

		$document->addScriptDeclaration($editStrings);

		JEVHelper::script('editicalJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		if (!GSLMSIE10)
		{
			JEVHelper::script('editicalGSL.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		}
		JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		JEVHelper::script('JevStdRequiredFieldsJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');

		$document->setTitle(Text::_('EDIT_ICAL_REPEAT'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('EDIT_ICAL_REPEAT'), 'jevents');

		JToolbarHelper::apply('icalrepeat.apply', "JEV_SAVE");
		JToolbarHelper::apply('icalrepeat.save', "JEV_SAVE_CLOSE");

		// We will need this when we offer to change one or more repeats on save!
		//$this->addSaveToolBarButton();

		JToolbarHelper::cancel('icalrepeat.list');
		//JToolbarHelper::help( 'screen.icalrepeat.edit', true);

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->get("section",0);



		// load Joomla javascript classes
		HTMLHelper::_('behavior.core');
		$this->setLayout("edit");

		$this->setupEditForm();

	}

	function addSaveToolBarButton()
	{

		$toolbar = JToolBar::getInstance('toolbar');

		// Add a standard button
		//$bar->appendButton( 'Standard', $icon, $alt, $task, $listSelect, $x );
		JLoader::register('JevModal', JPATH_LIBRARIES . "/jevents/jevmodal/jevmodal.php");
		JevModal::modal("icalrepeat_dialogJQ");

		$buttontext = Text::_('JEV_SAVE');
		$buttonhtml = '<button onclick="javascript:return jQuery(\'#icalrepeat_dialogJQ\').modal(\'show\');" class="btn btn-small btn-success">
		<span class="icon-apply" title="' . $buttontext . '"></span>' . $buttontext . '</button>';
		$toolbar->appendButton('Custom', $buttonhtml, "apply");

		$submitbutton = "Joomla.submitbutton";
		// reuse action_dialog for sizing purposes
		?>
		<div id="icalrepeat_dialogJQ" class="action_dialogJQ modal hide fade" tabindex="-1" role="dialog"
		     aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel"><?php echo Text::_("JEV_SAVE"); ?></h4>
					</div>
					<div class="modal-body">
						<?php
						$buttontext = Text::_('JEV_SAVE_THIS');
						$buttontask = "icalrepeat.save";
						?>
						<div class="icalrepeat-saveoptions">
							<div style="margin-bottom:10px;">
								<button onclick="<?php echo $submitbutton; ?>('<?php echo $buttontask; ?>');return false;"
								        class="btn btn-small">
									<span class="icon-save" style='margin:0px'
									      title="<?php echo strip_tags($buttontext); ?>"></span>
									<?php echo $buttontext; ?></button>
							</div>

							<?php
							/*
							  $buttontext = Text::_( 'JEV_SAVE_FUTURE' );
							  $buttontask = "icalrepeat.savefuture";
							  <div><a href="#" onclick="javascript:return submitbutton('<?php echo $buttontask;?>')" class="toolbar"><span class="icon-32-save" style='margin:0px' title="<?php echo strip_tags($buttontext);?>"></span><?php echo $buttontext;?></a></div>
							 */
							?>

							<?php
							$buttontext = Text::_('JEV_SAVE_ALL');
							$buttontask = "icalevent.save";
							?>
							<div>
								<button onclick="<?php echo $submitbutton; ?>('<?php echo $buttontask; ?>');return false;"
								        class="btn btn-small">
								<span class="icon-save" style='margin:0px'
								      title="<?php echo strip_tags($buttontext); ?>">
								</span><?php echo $buttontext; ?>
								</button>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default"
						        data-dismiss="modal"><?php echo Text::_("JEV_CLOSE"); ?></button>
					</div>

				</div>
			</div>
		</div>
		<?php

	}

}
